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

// Fetch transaction
$stmt = $pdo->prepare("SELECT t.*, p.Pocket_Name, c.Category_Name, sc.Sub_Category_Name
                       FROM Transactions t
                       LEFT JOIN Pocket p ON t.Pocket_ID = p.Pocket_ID
                       LEFT JOIN Category c ON t.Category_ID = c.Category_ID
                       LEFT JOIN Sub_Category sc ON t.Sub_Category_ID = sc.Sub_Category_ID
                       WHERE t.Transaction_ID = ?");
$stmt->execute([$id]);
$transaction = $stmt->fetch();

if (!$transaction) {
    $_SESSION['error'] = 'Transaction not found!';
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    validateCSRF();

    if (isset($_POST['confirm_delete'])) {
        try {
            $stmt = $pdo->prepare("DELETE FROM Transactions WHERE Transaction_ID = ?");
            $stmt->execute([$id]);

            $_SESSION['success'] = 'Transaction deleted successfully!';
            header('Location: index.php');
            exit;
        } catch (PDOException $e) {
            $_SESSION['error'] = 'Failed to delete transaction: ' . $e->getMessage();
            header('Location: index.php');
            exit;
        }
    } else {
        header('Location: index.php');
        exit;
    }
}

include '../../components/header.php';
?>

<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h3 mb-0 text-gray-800">
                    <i class="fas fa-trash text-danger"></i> Delete Transaction
                </h1>
                <p class="text-muted">Confirm deletion of transaction</p>
            </div>
            <a href="index.php" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to List
            </a>
        </div>
    </div>
</div>

<?php include '../../components/alerts.php'; ?>

<div class="row">
    <div class="col-lg-8">
        <div class="card border-danger">
            <div class="card-header bg-danger text-white">
                <h6 class="m-0 font-weight-bold">
                    <i class="fas fa-exclamation-triangle"></i> Delete Confirmation
                </h6>
            </div>
            <div class="card-body">
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle"></i>
                    <strong>Warning!</strong> This action cannot be undone. Are you sure you want to delete this transaction?
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Transaction ID</label>
                            <p class="form-control-plaintext"><?php echo htmlspecialchars($transaction['Transaction_ID']); ?></p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Transaction Date</label>
                            <p class="form-control-plaintext"><?php echo formatDate($transaction['Transaction_Date']); ?></p>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Type</label>
                            <p class="form-control-plaintext">
                                <span class="badge bg-<?php echo $transaction['Type'] === 'Income' ? 'success' : 'danger'; ?> fs-6">
                                    <?php echo htmlspecialchars($transaction['Type']); ?>
                                </span>
                            </p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Amount</label>
                            <p class="form-control-plaintext fw-bold text-<?php echo $transaction['Type'] === 'Income' ? 'success' : 'danger'; ?>">
                                <?php echo formatCurrency($transaction['Amount']); ?>
                            </p>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Category</label>
                            <p class="form-control-plaintext"><?php echo htmlspecialchars($transaction['Category_Name'] ?? 'N/A'); ?></p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Sub Category</label>
                            <p class="form-control-plaintext"><?php echo htmlspecialchars($transaction['Sub_Category_Name'] ?? 'N/A'); ?></p>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Pocket</label>
                            <p class="form-control-plaintext"><?php echo htmlspecialchars($transaction['Pocket_Name'] ?? 'N/A'); ?></p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Description</label>
                            <p class="form-control-plaintext"><?php echo htmlspecialchars($transaction['Description'] ?? 'No description'); ?></p>
                        </div>
                    </div>
                </div>

                <form method="POST" class="mt-4">
                    <input type="hidden" name="<?php echo CSRF_TOKEN_NAME; ?>" value="<?php echo $_SESSION[CSRF_TOKEN_NAME]; ?>">
                    <input type="hidden" name="confirm_delete" value="1">

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-trash"></i> Yes, Delete Transaction
                        </button>
                        <a href="index.php" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-info-circle"></i> Impact of Deletion
                </h6>
            </div>
            <div class="card-body">
                <h6>Deleting this transaction will:</h6>
                <ul class="small">
                    <li>Remove the transaction from all reports</li>
                    <li>Affect total income/expense calculations</li>
                    <li>Impact budget tracking if applicable</li>
                    <li>Remove transaction from pocket balance</li>
                </ul>

                <div class="alert alert-info mt-3">
                    <small>
                        <i class="fas fa-lightbulb"></i>
                        <strong>Tip:</strong> Consider editing the transaction instead of deleting it if you need to make corrections.
                    </small>
                </div>
            </div>
        </div>

        <div class="card mt-4">
            <div class="card-header">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-history"></i> Transaction Summary
                </h6>
            </div>
            <div class="card-body">
                <p><strong>Type:</strong> <?php echo htmlspecialchars($transaction['Type']); ?></p>
                <p><strong>Amount:</strong> <?php echo formatCurrency($transaction['Amount']); ?></p>
                <p><strong>Date:</strong> <?php echo formatDate($transaction['Transaction_Date']); ?></p>
                <p><strong>Category:</strong> <?php echo htmlspecialchars($transaction['Category_Name'] ?? 'N/A'); ?></p>
            </div>
        </div>
    </div>
</div>

<?php include '../../components/footer.php'; ?>