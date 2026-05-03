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
$stmt = $pdo->prepare("SELECT dl.*, c.Contact_Name, c.Phone_Number, p.Pocket_Name
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

// Calculate days until due
$dueDate = new DateTime($debtLoan['Due_Date']);
$today = new DateTime();
$daysDiff = $today->diff($dueDate)->days;
$isOverdue = $today > $dueDate && $debtLoan['Status'] === 'Pending';

include '../../components/header.php';
?>

<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h3 mb-0 text-gray-800">
                    <i class="fas fa-eye text-primary"></i> View Debt/Loan Record
                </h1>
                <p class="text-muted">Detailed information about the debt/loan</p>
            </div>
            <div>
                <a href="edit.php?id=<?php echo $debtLoan['Debt_Loan_ID']; ?>" class="btn btn-warning">
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
                    <i class="fas fa-info-circle"></i> Debt/Loan Information
                </h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Record ID</label>
                            <p class="form-control-plaintext"><?php echo htmlspecialchars($debtLoan['Debt_Loan_ID']); ?></p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Status</label>
                            <p class="form-control-plaintext">
                                <span class="badge bg-<?php echo $debtLoan['Status'] === 'Paid' ? 'success' : 'warning'; ?> fs-6">
                                    <?php echo htmlspecialchars($debtLoan['Status']); ?>
                                </span>
                            </p>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Contact</label>
                            <p class="form-control-plaintext">
                                <i class="fas fa-user text-primary"></i> <?php echo htmlspecialchars($debtLoan['Contact_Name'] ?? 'N/A'); ?>
                                <?php if ($debtLoan['Phone_Number']): ?>
                                <br><small class="text-muted"><i class="fas fa-phone"></i> <?php echo htmlspecialchars($debtLoan['Phone_Number']); ?></small>
                                <?php endif; ?>
                            </p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Pocket</label>
                            <p class="form-control-plaintext">
                                <i class="fas fa-wallet text-success"></i> <?php echo htmlspecialchars($debtLoan['Pocket_Name'] ?? 'N/A'); ?>
                            </p>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Amount</label>
                            <p class="form-control-plaintext fs-5 text-primary fw-bold">
                                IDR <?php echo number_format($debtLoan['Amount'], 0, ',', '.'); ?>
                            </p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Due Date</label>
                            <p class="form-control-plaintext">
                                <i class="fas fa-calendar text-warning"></i> <?php echo formatDate($debtLoan['Due_Date']); ?>
                                <?php if ($debtLoan['Status'] === 'Pending'): ?>
                                <br><small class="<?php echo $isOverdue ? 'text-danger' : 'text-muted'; ?>">
                                    <i class="fas fa-<?php echo $isOverdue ? 'exclamation-triangle' : 'clock'; ?>"></i>
                                    <?php if ($isOverdue): ?>
                                    Overdue by <?php echo $daysDiff; ?> days
                                    <?php else: ?>
                                    Due in <?php echo $daysDiff; ?> days
                                    <?php endif; ?>
                                </small>
                                <?php endif; ?>
                            </p>
                        </div>
                    </div>
                </div>

                <?php if ($debtLoan['Description']): ?>
                <div class="mb-3">
                    <label class="form-label fw-bold">Description</label>
                    <p class="form-control-plaintext"><?php echo nl2br(htmlspecialchars($debtLoan['Description'])); ?></p>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Status Timeline -->
        <div class="card mt-4">
            <div class="card-header">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-history"></i> Status Timeline
                </h6>
            </div>
            <div class="card-body">
                <div class="timeline">
                    <div class="timeline-item">
                        <div class="timeline-marker bg-primary"></div>
                        <div class="timeline-content">
                            <h6 class="timeline-title">Record Created</h6>
                            <p class="timeline-text">Debt/Loan record was created with amount <?php echo formatCurrency($debtLoan['Amount']); ?></p>
                            <small class="text-muted">Due: <?php echo formatDate($debtLoan['Due_Date']); ?></small>
                        </div>
                    </div>

                    <?php if ($debtLoan['Status'] === 'Paid'): ?>
                    <div class="timeline-item">
                        <div class="timeline-marker bg-success"></div>
                        <div class="timeline-content">
                            <h6 class="timeline-title">Marked as Paid</h6>
                            <p class="timeline-text">This debt/loan has been marked as paid</p>
                            <small class="text-muted">Status updated to Paid</small>
                        </div>
                    </div>
                    <?php elseif ($isOverdue): ?>
                    <div class="timeline-item">
                        <div class="timeline-marker bg-danger"></div>
                        <div class="timeline-content">
                            <h6 class="timeline-title">Overdue</h6>
                            <p class="timeline-text">This debt/loan is now overdue</p>
                            <small class="text-danger">Overdue by <?php echo $daysDiff; ?> days</small>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
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
                    <a href="edit.php?id=<?php echo $debtLoan['Debt_Loan_ID']; ?>" class="btn btn-warning">
                        <i class="fas fa-edit"></i> Edit Record
                    </a>
                    <a href="delete.php?id=<?php echo $debtLoan['Debt_Loan_ID']; ?>" class="btn btn-danger delete-btn">
                        <i class="fas fa-trash"></i> Delete Record
                    </a>
                    <?php if ($debtLoan['Phone_Number']): ?>
                    <a href="tel:<?php echo htmlspecialchars($debtLoan['Phone_Number']); ?>" class="btn btn-info">
                        <i class="fas fa-phone"></i> Call Contact
                    </a>
                    <?php endif; ?>
                    <a href="../contact/view.php?id=<?php echo $debtLoan['Contact_ID']; ?>" class="btn btn-outline-primary">
                        <i class="fas fa-user"></i> View Contact
                    </a>
                    <a href="../pocket/view.php?id=<?php echo $debtLoan['Pocket_ID']; ?>" class="btn btn-outline-success">
                        <i class="fas fa-wallet"></i> View Pocket
                    </a>
                </div>
            </div>
        </div>

        <div class="card mt-4">
            <div class="card-header">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-chart-pie"></i> Financial Summary
                </h6>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-12">
                        <h4 class="text-primary mb-1"><?php echo formatCurrency($debtLoan['Amount']); ?></h4>
                        <p class="text-muted mb-0">Total Amount</p>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-6 text-center">
                        <small class="text-muted">Status</small>
                        <br>
                        <span class="badge bg-<?php echo $debtLoan['Status'] === 'Paid' ? 'success' : 'warning'; ?>">
                            <?php echo htmlspecialchars($debtLoan['Status']); ?>
                        </span>
                    </div>
                    <div class="col-6 text-center">
                        <small class="text-muted">Due In</small>
                        <br>
                        <span class="<?php echo $isOverdue ? 'text-danger' : 'text-muted'; ?>">
                            <?php if ($isOverdue): ?>
                            <?php echo $daysDiff; ?> days ago
                            <?php else: ?>
                            <?php echo $daysDiff; ?> days
                            <?php endif; ?>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <?php if ($isOverdue && $debtLoan['Status'] === 'Pending'): ?>
        <div class="card mt-4 border-danger">
            <div class="card-header bg-danger text-white">
                <h6 class="m-0 font-weight-bold">
                    <i class="fas fa-exclamation-triangle"></i> Overdue Notice
                </h6>
            </div>
            <div class="card-body">
                <p class="text-danger mb-2">This debt/loan is overdue by <?php echo $daysDiff; ?> days.</p>
                <p class="small text-muted">Consider contacting the person and updating the status or due date.</p>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<style>
.timeline {
    position: relative;
    padding-left: 30px;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 15px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #e9ecef;
}

.timeline-item {
    position: relative;
    margin-bottom: 20px;
}

.timeline-marker {
    position: absolute;
    left: -22px;
    top: 5px;
    width: 12px;
    height: 12px;
    border-radius: 50%;
    border: 2px solid #fff;
}

.timeline-content {
    background: #f8f9fa;
    padding: 15px;
    border-radius: 5px;
}

.timeline-title {
    margin: 0 0 5px 0;
    font-size: 14px;
    font-weight: bold;
    color: #495057;
}

.timeline-text {
    margin: 0 0 5px 0;
    font-size: 13px;
    color: #6c757d;
}
</style>

<?php include '../../components/footer.php'; ?>