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

// Fetch contact
$stmt = $pdo->prepare("SELECT * FROM Contact WHERE Contact_ID = ?");
$stmt->execute([$id]);
$contact = $stmt->fetch();

if (!$contact) {
    $_SESSION['error'] = 'Contact not found!';
    header('Location: index.php');
    exit;
}

// Get related debt/loan count
$debtStmt = $pdo->prepare("SELECT COUNT(*) FROM Debt_Loan WHERE Contact_ID = ?");
$debtStmt->execute([$id]);
$debtCount = $debtStmt->fetchColumn();

// Get recent debt/loan records
$recentDebtStmt = $pdo->prepare("SELECT * FROM Debt_Loan WHERE Contact_ID = ? ORDER BY Due_Date DESC LIMIT 5");
$recentDebtStmt->execute([$id]);
$recentDebts = $recentDebtStmt->fetchAll();

include '../../components/header.php';
?>

<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h3 mb-0 text-gray-800">
                    <i class="fas fa-eye text-primary"></i> View Contact
                </h1>
                <p class="text-muted">Contact details and related information</p>
            </div>
            <div>
                <a href="edit.php?id=<?php echo $contact['Contact_ID']; ?>" class="btn btn-warning">
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
                    <i class="fas fa-info-circle"></i> Contact Information
                </h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Contact ID</label>
                            <p class="form-control-plaintext"><?php echo htmlspecialchars($contact['Contact_ID']); ?></p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Relation Type</label>
                            <p class="form-control-plaintext">
                                <span class="badge bg-primary"><?php echo htmlspecialchars($contact['Relation_Type']); ?></span>
                            </p>
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Contact Name</label>
                    <p class="form-control-plaintext"><?php echo htmlspecialchars($contact['Contact_Name']); ?></p>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Phone Number</label>
                    <p class="form-control-plaintext">
                        <i class="fas fa-phone text-success"></i> <?php echo htmlspecialchars($contact['Phone_Number']); ?>
                    </p>
                </div>
            </div>
        </div>

        <!-- Statistics Card -->
        <div class="card mt-4">
            <div class="card-header">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-chart-bar"></i> Contact Statistics
                </h6>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <h4 class="text-primary"><?php echo $debtCount; ?></h4>
                            <p class="text-muted mb-0">Total Debt/Loan Records</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <h4 class="text-success">IDR 0</h4>
                            <p class="text-muted mb-0">Total Amount</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Debt/Loan Records -->
        <?php if ($recentDebts): ?>
        <div class="card mt-4">
            <div class="card-header">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-history"></i> Recent Debt/Loan Records
                </h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Amount</th>
                                <th>Due Date</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recentDebts as $debt): ?>
                            <tr>
                                <td><?php echo formatCurrency($debt['Amount']); ?></td>
                                <td><?php echo formatDate($debt['Due_Date']); ?></td>
                                <td>
                                    <span class="badge bg-<?php echo $debt['Status'] === 'Paid' ? 'success' : 'warning'; ?>">
                                        <?php echo htmlspecialchars($debt['Status']); ?>
                                    </span>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <div class="text-center mt-3">
                    <a href="../debt_loan/index.php?contact_id=<?php echo $contact['Contact_ID']; ?>" class="btn btn-primary btn-sm">
                        View All Debt/Loan Records
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
                    <a href="edit.php?id=<?php echo $contact['Contact_ID']; ?>" class="btn btn-warning">
                        <i class="fas fa-edit"></i> Edit Contact
                    </a>
                    <a href="delete.php?id=<?php echo $contact['Contact_ID']; ?>" class="btn btn-danger delete-btn">
                        <i class="fas fa-trash"></i> Delete Contact
                    </a>
                    <a href="../debt_loan/create.php?contact_id=<?php echo $contact['Contact_ID']; ?>" class="btn btn-success">
                        <i class="fas fa-plus"></i> Add Debt/Loan
                    </a>
                    <a href="tel:<?php echo htmlspecialchars($contact['Phone_Number']); ?>" class="btn btn-info">
                        <i class="fas fa-phone"></i> Call Contact
                    </a>
                </div>
            </div>
        </div>

        <div class="card mt-4">
            <div class="card-header">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-user-tag"></i> Relation Summary
                </h6>
            </div>
            <div class="card-body">
                <p><strong>Type:</strong> <?php echo htmlspecialchars($contact['Relation_Type']); ?></p>
                <p><strong>Financial Relationship:</strong> <?php echo $debtCount > 0 ? 'Active' : 'None'; ?></p>
            </div>
        </div>
    </div>
</div>

<?php include '../../components/footer.php'; ?>