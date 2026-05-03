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

// Fetch transfer
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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    validateCSRF();

    if (isset($_POST['confirm_delete'])) {
        try {
            $stmt = $pdo->prepare("DELETE FROM Transfer WHERE Transfer_ID = ?");
            $stmt->execute([$id]);

            $_SESSION['success'] = 'Transfer deleted successfully!';
            header('Location: index.php');
            exit;
        } catch (PDOException $e) {
            $_SESSION['error'] = 'Failed to delete transfer: ' . $e->getMessage();
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
                    <i class="fas fa-trash text-danger"></i> Delete Transfer
                </h1>
                <p class="text-muted">Confirm deletion of transfer</p>
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
                    <strong>Warning!</strong> This action cannot be undone. Are you sure you want to delete this transfer?
                </div>

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
                                <i class="fas fa-arrow-right text-danger"></i> <?php echo htmlspecialchars($transfer['From_Pocket_Name'] ?? 'N/A'); ?>
                            </p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">To Pocket</label>
                            <p class="form-control-plaintext">
                                <i class="fas fa-arrow-left text-success"></i> <?php echo htmlspecialchars($transfer['To_Pocket_Name'] ?? 'N/A'); ?>
                            </p>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Amount</label>
                            <p class="form-control-plaintext fw-bold text-primary fs-5">
                                <?php echo formatCurrency($transfer['Amount']); ?>
                            </p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Description</label>
                            <p class="form-control-plaintext"><?php echo htmlspecialchars($transfer['Description'] ?? 'No description'); ?></p>
                        </div>
                    </div>
                </div>

                <form method="POST" class="mt-4">
                    <input type="hidden" name="<?php echo CSRF_TOKEN_NAME; ?>" value="<?php echo $_SESSION[CSRF_TOKEN_NAME]; ?>">
                    <input type="hidden" name="confirm_delete" value="1">

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-trash"></i> Yes, Delete Transfer
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
                <h6>Deleting this transfer will:</h6>
                <ul class="small">
                    <li>Remove the transfer from all reports</li>
                    <li>Affect pocket balance calculations</li>
                    <li>Impact transfer statistics and summaries</li>
                    <li>Remove transfer from pocket transaction history</li>
                </ul>

                <div class="alert alert-info mt-3">
                    <small>
                        <i class="fas fa-lightbulb"></i>
                        <strong>Tip:</strong> Consider editing the transfer instead of deleting it if you need to make corrections.
                    </small>
                </div>
            </div>
        </div>

        <div class="card mt-4">
            <div class="card-header">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-exchange-alt"></i> Transfer Summary
                </h6>
            </div>
            <div class="card-body">
                <p><strong>From:</strong> <?php echo htmlspecialchars($transfer['From_Pocket_Name'] ?? 'N/A'); ?></p>
                <p><strong>To:</strong> <?php echo htmlspecialchars($transfer['To_Pocket_Name'] ?? 'N/A'); ?></p>
                <p><strong>Amount:</strong> <?php echo formatCurrency($transfer['Amount']); ?></p>
                <p><strong>Date:</strong> <?php echo formatDate($transfer['Transfer_Date']); ?></p>
            </div>
        </div>
    </div>
</div>

<?php include '../../components/footer.php'; ?>