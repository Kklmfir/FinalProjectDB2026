<?php
require_once '../../config/app.php';
require_once '../../config/database.php';
require_once '../../helpers/functions.php';
require_once '../../helpers/validation.php';
require_once '../../helpers/security.php';

$pdo = require '../../config/database.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$id) {
    header('Location: index.php');
    exit;
}

// Fetch transfer with related data
$stmt = $pdo->prepare("SELECT t.*, fp.Pocket_Name as From_Pocket_Name, tp.Pocket_Name as To_Pocket_Name
                       FROM Transfer t
                       LEFT JOIN Pocket fp ON t.From_Pocket_ID = fp.Pocket_ID
                       LEFT JOIN Pocket tp ON t.To_Pocket_ID = tp.Pocket_ID
                       WHERE t.Transfer_ID = ?");
$stmt->execute([$id]);
$transfer = $stmt->fetch();

if (!$transfer) {
    $_SESSION['error'] = 'Transfer not found!';
    header('Location: index.php');
    exit;
}

// Get related transfers between same pockets
$relatedStmt = $pdo->prepare("SELECT t.*, fp.Pocket_Name as From_Pocket_Name, tp.Pocket_Name as To_Pocket_Name
                              FROM Transfer t
                              LEFT JOIN Pocket fp ON t.From_Pocket_ID = fp.Pocket_ID
                              LEFT JOIN Pocket tp ON t.To_Pocket_ID = tp.Pocket_ID
                              WHERE ((t.From_Pocket_ID = ? AND t.To_Pocket_ID = ?) OR (t.From_Pocket_ID = ? AND t.To_Pocket_ID = ?))
                              AND t.Transfer_ID != ?
                              ORDER BY t.Transfer_Date DESC LIMIT 5");
$relatedStmt->execute([
    $transfer['From_Pocket_ID'], $transfer['To_Pocket_ID'],
    $transfer['To_Pocket_ID'], $transfer['From_Pocket_ID'],
    $id
]);
$relatedTransfers = $relatedStmt->fetchAll();

// Get pocket balances
$fromPocketBalanceStmt = $pdo->prepare("SELECT
    (SELECT COALESCE(SUM(Amount), 0) FROM Transactions WHERE Pocket_ID = ? AND Type = 'Income') -
    (SELECT COALESCE(SUM(Amount), 0) FROM Transactions WHERE Pocket_ID = ? AND Type = 'Expense') as balance");
$fromPocketBalanceStmt->execute([$transfer['From_Pocket_ID'], $transfer['From_Pocket_ID']]);
$fromPocketBalance = $fromPocketBalanceStmt->fetchColumn() ?: 0;

$toPocketBalanceStmt = $pdo->prepare("SELECT
    (SELECT COALESCE(SUM(Amount), 0) FROM Transactions WHERE Pocket_ID = ? AND Type = 'Income') -
    (SELECT COALESCE(SUM(Amount), 0) FROM Transactions WHERE Pocket_ID = ? AND Type = 'Expense') as balance");
$toPocketBalanceStmt->execute([$transfer['To_Pocket_ID'], $transfer['To_Pocket_ID']]);
$toPocketBalance = $toPocketBalanceStmt->fetchColumn() ?: 0;

include '../../components/header.php';
?>

<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h3 mb-0 text-gray-800">
                    <i class="fas fa-eye text-primary"></i> View Transfer
                </h1>
                <p class="text-muted">Detailed transfer information</p>
            </div>
            <div>
                <a href="edit.php?id=<?php echo $transfer['Transfer_ID']; ?>" class="btn btn-warning">
                    <i class="fas fa-edit"></i> Edit
                </a>
                <a href="index.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to List
                </a>
            </div>
        </div>
    </div>
</div>

<?php include '../../components/alerts.php'; ?>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-info-circle"></i> Transfer Details
                </h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Transfer ID</label>
                            <p class="form-control-plaintext"><?php echo htmlspecialchars($transfer['Transfer_ID']); ?></p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Transfer Date</label>
                            <p class="form-control-plaintext"><?php echo formatDate($transfer['Transfer_Date']); ?></p>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">From Pocket</label>
                            <p class="form-control-plaintext">
                                <i class="fas fa-arrow-right text-danger fa-lg"></i>
                                <span class="ms-2"><?php echo htmlspecialchars($transfer['From_Pocket_Name'] ?? 'N/A'); ?></span>
                            </p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">To Pocket</label>
                            <p class="form-control-plaintext">
                                <i class="fas fa-arrow-left text-success fa-lg"></i>
                                <span class="ms-2"><?php echo htmlspecialchars($transfer['To_Pocket_Name'] ?? 'N/A'); ?></span>
                            </p>
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Amount</label>
                    <p class="form-control-plaintext fw-bold text-primary fs-4">
                        <?php echo formatCurrency($transfer['Amount']); ?>
                    </p>
                </div>

                <?php if ($transfer['Description']): ?>
                <div class="mb-3">
                    <label class="form-label fw-bold">Description</label>
                    <p class="form-control-plaintext"><?php echo nl2br(htmlspecialchars($transfer['Description'])); ?></p>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Pocket Balances Section -->
        <div class="card mt-4">
            <div class="card-header">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-balance-scale"></i> Pocket Balances
                </h6>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <h5 class="text-danger"><?php echo formatCurrency($fromPocketBalance); ?></h5>
                            <p class="text-muted mb-0"><?php echo htmlspecialchars($transfer['From_Pocket_Name'] ?? 'From Pocket'); ?> Balance</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <h5 class="text-success"><?php echo formatCurrency($toPocketBalance); ?></h5>
                            <p class="text-muted mb-0"><?php echo htmlspecialchars($transfer['To_Pocket_Name'] ?? 'To Pocket'); ?> Balance</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Transfer Flow Visualization -->
        <div class="card mt-4">
            <div class="card-header">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-exchange-alt"></i> Transfer Flow
                </h6>
            </div>
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-4 text-center">
                        <div class="p-3 border rounded">
                            <i class="fas fa-wallet fa-2x text-danger mb-2"></i>
                            <h6><?php echo htmlspecialchars($transfer['From_Pocket_Name'] ?? 'From Pocket'); ?></h6>
                            <small class="text-muted">Source</small>
                        </div>
                    </div>
                    <div class="col-md-4 text-center">
                        <i class="fas fa-arrow-right fa-2x text-primary"></i>
                        <div class="mt-2">
                            <h4 class="text-primary"><?php echo formatCurrency($transfer['Amount']); ?></h4>
                            <small class="text-muted">Transferred Amount</small>
                        </div>
                    </div>
                    <div class="col-md-4 text-center">
                        <div class="p-3 border rounded">
                            <i class="fas fa-wallet fa-2x text-success mb-2"></i>
                            <h6><?php echo htmlspecialchars($transfer['To_Pocket_Name'] ?? 'To Pocket'); ?></h6>
                            <small class="text-muted">Destination</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Related Transfers -->
        <?php if ($relatedTransfers): ?>
        <div class="card mt-4">
            <div class="card-header">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-link"></i> Related Transfers
                </h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Direction</th>
                                <th>Amount</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($relatedTransfers as $related): ?>
                            <tr>
                                <td><?php echo formatDate($related['Transfer_Date']); ?></td>
                                <td>
                                    <?php if ($related['From_Pocket_ID'] == $transfer['From_Pocket_ID'] && $related['To_Pocket_ID'] == $transfer['To_Pocket_ID']): ?>
                                        <span class="badge bg-primary">Same Direction</span>
                                    <?php else: ?>
                                        <span class="badge bg-info">Reverse Direction</span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo formatCurrency($related['Amount']); ?></td>
                                <td>
                                    <a href="view.php?id=<?php echo $related['Transfer_ID']; ?>" class="btn btn-sm btn-outline-primary">
                                        View
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <div class="text-center mt-3">
                    <a href="index.php?from_pocket_id=<?php echo $transfer['From_Pocket_ID']; ?>&to_pocket_id=<?php echo $transfer['To_Pocket_ID']; ?>" class="btn btn-primary btn-sm">
                        View All Transfers Between These Pockets
                    </a>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-cogs"></i> Actions
                </h6>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="edit.php?id=<?php echo $transfer['Transfer_ID']; ?>" class="btn btn-warning">
                        <i class="fas fa-edit"></i> Edit Transfer
                    </a>
                    <a href="delete.php?id=<?php echo $transfer['Transfer_ID']; ?>" class="btn btn-danger delete-btn">
                        <i class="fas fa-trash"></i> Delete Transfer
                    </a>
                    <a href="create.php?from_pocket_id=<?php echo $transfer['From_Pocket_ID']; ?>&to_pocket_id=<?php echo $transfer['To_Pocket_ID']; ?>" class="btn btn-success">
                        <i class="fas fa-plus"></i> Add Similar Transfer
                    </a>
                    <a href="../pocket/view.php?id=<?php echo $transfer['From_Pocket_ID']; ?>" class="btn btn-outline-primary">
                        <i class="fas fa-wallet"></i> View From Pocket
                    </a>
                    <a href="../pocket/view.php?id=<?php echo $transfer['To_Pocket_ID']; ?>" class="btn btn-outline-primary">
                        <i class="fas fa-wallet"></i> View To Pocket
                    </a>
                </div>
            </div>
        </div>

        <div class="card mt-4">
            <div class="card-header">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-chart-pie"></i> Transfer Summary
                </h6>
            </div>
            <div class="card-body">
                <p><strong>From:</strong> <?php echo htmlspecialchars($transfer['From_Pocket_Name'] ?? 'N/A'); ?></p>
                <p><strong>To:</strong> <?php echo htmlspecialchars($transfer['To_Pocket_Name'] ?? 'N/A'); ?></p>
                <p><strong>Amount:</strong> <?php echo formatCurrency($transfer['Amount']); ?></p>
                <p><strong>Date:</strong> <?php echo formatDate($transfer['Transfer_Date']); ?></p>
            </div>
        </div>

        <div class="card mt-4">
            <div class="card-header">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-tags"></i> Transfer Purpose
                </h6>
            </div>
            <div class="card-body">
                <p class="small text-muted">Transfers allow you to move money between different pockets for better financial organization and management.</p>
                <ul class="small">
                    <li>Reallocate funds between accounts</li>
                    <li>Move money towards specific goals</li>
                    <li>Transfer between savings and spending</li>
                    <li>Maintain optimal pocket balances</li>
                    <li>Track internal money movements</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<?php include '../../components/footer.php'; ?>