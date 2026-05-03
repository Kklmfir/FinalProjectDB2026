<?php
require_once '../../config/app.php';
require_once '../../config/database.php';
require_once '../../helpers/functions.php';
require_once '../../helpers/validation.php';
require_once '../../helpers/security.php';

$pdo = require '../../config/database.php';

// Handle search and filters
$search = sanitize($_GET['search'] ?? '');
$status = sanitize($_GET['status'] ?? '');
$contact_id = isset($_GET['contact_id']) ? (int)$_GET['contact_id'] : null;

// Build query
$query = "SELECT dl.*, c.Contact_Name, p.Pocket_Name
          FROM Debt_Loan dl
          LEFT JOIN Contact c ON dl.Contact_ID = c.Contact_ID
          LEFT JOIN Pocket p ON dl.Pocket_ID = p.Pocket_ID
          WHERE 1=1";

$params = [];

if ($search) {
    $query .= " AND (c.Contact_Name LIKE ? OR dl.Description LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if ($status) {
    $query .= " AND dl.Status = ?";
    $params[] = $status;
}

if ($contact_id) {
    $query .= " AND dl.Contact_ID = ?";
    $params[] = $contact_id;
}

$query .= " ORDER BY dl.Due_Date DESC";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$debtLoans = $stmt->fetchAll();

// Get summary statistics
$totalStmt = $pdo->prepare("SELECT COUNT(*) FROM Debt_Loan");
$totalStmt->execute();
$totalCount = $totalStmt->fetchColumn();

$pendingStmt = $pdo->prepare("SELECT COUNT(*) FROM Debt_Loan WHERE Status = 'Pending'");
$pendingStmt->execute();
$pendingCount = $pendingStmt->fetchColumn();

$paidStmt = $pdo->prepare("SELECT COUNT(*) FROM Debt_Loan WHERE Status = 'Paid'");
$paidStmt->execute();
$paidCount = $paidStmt->fetchColumn();

$totalAmountStmt = $pdo->prepare("SELECT SUM(Amount) FROM Debt_Loan");
$totalAmountStmt->execute();
$totalAmount = $totalAmountStmt->fetchColumn() ?: 0;

include '../../components/header.php';
?>

<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h3 mb-0 text-gray-800">
                    <i class="fas fa-hand-holding-usd text-primary"></i> Debt & Loan Management
                </h1>
                <p class="text-muted">Manage your debts and loans</p>
            </div>
            <a href="create.php<?php echo $contact_id ? '?contact_id=' . $contact_id : ''; ?>" class="btn btn-primary">
                <i class="fas fa-plus"></i> Add Debt/Loan
            </a>
        </div>
    </div>
</div>

<?php include '../../components/alerts.php'; ?>

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            Total Records</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $totalCount; ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-file-invoice-dollar fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                            Pending</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $pendingCount; ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-clock fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            Paid</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $paidCount; ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                            Total Amount</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo formatCurrency($totalAmount); ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-money-bill-wave fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="card mb-4">
    <div class="card-header">
        <h6 class="m-0 font-weight-bold text-primary">
            <i class="fas fa-filter"></i> Filters
        </h6>
    </div>
    <div class="card-body">
        <form method="GET" class="row g-3">
            <div class="col-md-4">
                <input type="text" class="form-control" name="search" placeholder="Search by contact or description..."
                       value="<?php echo htmlspecialchars($search); ?>">
            </div>
            <div class="col-md-3">
                <select class="form-control" name="status">
                    <option value="">All Status</option>
                    <option value="Pending" <?php echo $status === 'Pending' ? 'selected' : ''; ?>>Pending</option>
                    <option value="Paid" <?php echo $status === 'Paid' ? 'selected' : ''; ?>>Paid</option>
                </select>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search"></i> Filter
                </button>
                <a href="index.php" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Clear
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Data Table -->
<div class="card">
    <div class="card-header">
        <h6 class="m-0 font-weight-bold text-primary">
            <i class="fas fa-table"></i> Debt & Loan Records
        </h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered datatable" id="debtLoanTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Contact</th>
                        <th>Pocket</th>
                        <th>Amount</th>
                        <th>Due Date</th>
                        <th>Status</th>
                        <th>Description</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($debtLoans as $debtLoan): ?>
                    <tr>
                        <td><?php echo $debtLoan['Debt_Loan_ID']; ?></td>
                        <td><?php echo htmlspecialchars($debtLoan['Contact_Name'] ?? 'N/A'); ?></td>
                        <td><?php echo htmlspecialchars($debtLoan['Pocket_Name'] ?? 'N/A'); ?></td>
                        <td><?php echo formatCurrency($debtLoan['Amount']); ?></td>
                        <td><?php echo formatDate($debtLoan['Due_Date']); ?></td>
                        <td>
                            <span class="badge bg-<?php echo $debtLoan['Status'] === 'Paid' ? 'success' : 'warning'; ?>">
                                <?php echo htmlspecialchars($debtLoan['Status']); ?>
                            </span>
                        </td>
                        <td><?php echo htmlspecialchars($debtLoan['Description'] ?? ''); ?></td>
                        <td>
                            <div class="btn-group" role="group">
                                <a href="view.php?id=<?php echo $debtLoan['Debt_Loan_ID']; ?>" class="btn btn-sm btn-info" title="View">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="edit.php?id=<?php echo $debtLoan['Debt_Loan_ID']; ?>" class="btn btn-sm btn-warning" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="delete.php?id=<?php echo $debtLoan['Debt_Loan_ID']; ?>" class="btn btn-sm btn-danger delete-btn" title="Delete">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include '../../components/footer.php'; ?>