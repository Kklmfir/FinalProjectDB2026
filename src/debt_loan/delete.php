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

// Fetch debt/loan with related data
$stmt = $pdo->prepare("SELECT dl.*, c.Contact_Name, p.Pocket_Name
                       FROM Debt_Loan dl
                       LEFT JOIN Contact c ON dl.Contact_ID = c.Contact_ID
                       LEFT JOIN Pocket p ON dl.Pocket_ID = p.Pocket_ID
                       WHERE dl.Debt_Loan_ID = ?");
$stmt->execute([$id]);
$debtLoan = $stmt->fetch();

if (!$debtLoan) {
    $_SESSION['error'] = 'Debt/Loan record not found!';
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    validateCSRF();

    try {
        // Delete debt/loan record
        $stmt = $pdo->prepare("DELETE FROM Debt_Loan WHERE Debt_Loan_ID = ?");
        $stmt->execute([$id]);

        $_SESSION['success'] = 'Debt/Loan record deleted successfully!';
        header('Location: index.php');
        exit;
    } catch (PDOException $e) {
        $_SESSION['error'] = 'Failed to delete debt/loan record: ' . $e->getMessage();
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
                    <i class="fas fa-trash text-danger"></i> Delete Debt/Loan Record
                </h1>
                <p class="text-muted">Confirm deletion of debt/loan record</p>
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
                    <i class="fas fa-exclamation-triangle"></i> Confirm Deletion
                </h6>
            </div>
            <div class="card-body">
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle"></i>
                    <strong>Warning!</strong> This action cannot be undone. Are you sure you want to delete this debt/loan record?
                </div>

                <div class="card mb-4">
                    <div class="card-header">
                        <h6 class="m-0 font-weight-bold text-primary">Debt/Loan Details</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>ID:</strong> <?php echo htmlspecialchars($debtLoan['Debt_Loan_ID']); ?></p>
                                <p><strong>Contact:</strong> <?php echo htmlspecialchars($debtLoan['Contact_Name'] ?? 'N/A'); ?></p>
                                <p><strong>Pocket:</strong> <?php echo htmlspecialchars($debtLoan['Pocket_Name'] ?? 'N/A'); ?></p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Amount:</strong> <?php echo formatCurrency($debtLoan['Amount']); ?></p>
                                <p><strong>Due Date:</strong> <?php echo formatDate($debtLoan['Due_Date']); ?></p>
                                <p><strong>Status:</strong>
                                    <span class="badge bg-<?php echo $debtLoan['Status'] === 'Paid' ? 'success' : 'warning'; ?>">
                                        <?php echo htmlspecialchars($debtLoan['Status']); ?>
                                    </span>
                                </p>
                            </div>
                        </div>
                        <?php if ($debtLoan['Description']): ?>
                        <div class="mt-3">
                            <p><strong>Description:</strong></p>
                            <p class="text-muted"><?php echo htmlspecialchars($debtLoan['Description']); ?></p>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>

                <form method="POST">
                    <input type="hidden" name="<?php echo CSRF_TOKEN_NAME; ?>" value="<?php echo $_SESSION[CSRF_TOKEN_NAME]; ?>">

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-trash"></i> Yes, Delete Record
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
                    <i class="fas fa-info-circle"></i> Important Notes
                </h6>
            </div>
            <div class="card-body">
                <ul class="small text-muted">
                    <li>Deleting this record will permanently remove it</li>
                    <li>Consider archiving instead of deleting for audit purposes</li>
                    <li>Paid records should be kept for financial tracking</li>
                    <li>This action cannot be reversed</li>
                </ul>
            </div>
        </div>

        <div class="card mt-4">
            <div class="card-header">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-chart-line"></i> Impact Analysis
                </h6>
            </div>
            <div class="card-body">
                <p><strong>Financial Impact:</strong></p>
                <ul class="small">
                    <li>Amount: <?php echo formatCurrency($debtLoan['Amount']); ?></li>
                    <li>Status: <?php echo htmlspecialchars($debtLoan['Status']); ?></li>
                    <li>Pocket: <?php echo htmlspecialchars($debtLoan['Pocket_Name'] ?? 'N/A'); ?></li>
                </ul>
                <p class="text-muted small">Deleting this record will affect financial calculations and reports.</p>
            </div>
        </div>
    </div>
</div>

<?php include '../../components/footer.php'; ?>