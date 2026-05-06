<?php
/**
 * counter_helper.php
 * Portable gapless sequential ID system using the Counter Table +
 * SELECT ... FOR UPDATE pattern.
 *
 * Compatible with MySQL InnoDB (local) and PostgreSQL (Supabase).
 * All counter operations and the actual INSERT run in a single transaction,
 * so if the INSERT fails the counter increment is also rolled back — no gaps.
 *
 * Usage:
 *   require_once __DIR__ . '/counter_helper.php';
 *   $newId = acquireSequentialIdAndInsert($pdo, 'transactions', function(int $id) use ($pdo, ...) {
 *       $stmt = $pdo->prepare("INSERT INTO Transactions (Transaction_ID, ...) VALUES (?, ...)");
 *       $stmt->execute([$id, ...]);
 *   });
 *
 * Deployment / restore note:
 *   After restoring a table from backup, sync the counter:
 *     INSERT INTO counters (name, current_value)
 *     VALUES ('transactions', (SELECT COALESCE(MAX(Transaction_ID), 0) FROM Transactions))
 *     ON DUPLICATE KEY UPDATE current_value = VALUES(current_value);   -- MySQL
 *   For PostgreSQL:
 *     INSERT INTO counters (name, current_value)
 *     VALUES ('transactions', (SELECT COALESCE(MAX("Transaction_ID"), 0) FROM "Transactions"))
 *     ON CONFLICT (name) DO UPDATE SET current_value = EXCLUDED.current_value;
 */

/**
 * Acquire the next sequential ID for $counterName and atomically execute $insertCallback.
 *
 * The function:
 *  1. Starts a transaction.
 *  2. Locks the counter row with SELECT ... FOR UPDATE (row-level lock, InnoDB & PG).
 *  3. Increments current_value (or bootstraps to 1 if missing).
 *  4. Calls $insertCallback($newId) — caller must perform the actual INSERT using
 *     the same $pdo connection (inside the same open transaction).
 *  5. COMMITs. On any exception the transaction is rolled back and retried up to
 *     $retry times with a 100 ms back-off (handles InnoDB deadlocks gracefully).
 *
 * @param PDO      $pdo           Active PDO connection (MySQL InnoDB or PostgreSQL).
 * @param string   $counterName   Logical counter name matching counters.name.
 * @param callable $insertCallback function(int $newId): void — must NOT begin/commit its own transaction.
 * @param int      $retry         Maximum retry attempts on deadlock/serialisation failure (default 3).
 * @return int                    The newly assigned sequential ID.
 * @throws Exception              Re-thrown after all retries are exhausted.
 */
function acquireSequentialIdAndInsert(PDO $pdo, string $counterName, callable $insertCallback, int $retry = 3): int
{
    $lastException = null;

    while ($retry > 0) {
        $retry--;
        try {
            $pdo->beginTransaction();

            // Lock the counter row for this entity (row-level, no table lock).
            $stmt = $pdo->prepare("SELECT current_value FROM counters WHERE name = ? FOR UPDATE");
            $stmt->execute([$counterName]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($row === false) {
                // Bootstrap: counter row does not exist yet.
                $newId = 1;
                $ins = $pdo->prepare("INSERT INTO counters (name, current_value) VALUES (?, ?)");
                $ins->execute([$counterName, $newId]);
            } else {
                $newId = (int)$row['current_value'] + 1;
                $upd = $pdo->prepare("UPDATE counters SET current_value = ? WHERE name = ?");
                $upd->execute([$newId, $counterName]);
            }

            // Caller performs the real INSERT inside this same open transaction.
            $insertCallback($newId);

            $pdo->commit();
            return $newId;

        } catch (PDOException $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            $lastException = $e;
            if ($retry > 0) {
                // Small back-off before retry (handles deadlocks / serialisation failures).
                usleep(100000); // 100 ms
            }
        } catch (Exception $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            // Non-deadlock errors are not retried.
            throw $e;
        }
    }

    throw $lastException;
}
