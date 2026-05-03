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

// Fetch budget
$stmt = $pdo->prepare("SELECT b.*, c.Category_Name as category_name FROM Budget b LEFT JOIN Category c ON b.Category_ID = c.Category_ID WHERE b.Budget_ID = ?");
$stmt->execute([$id]);
$budget = $stmt->fetch();

if (!$budget) {
    $_SESSION['error'] = 'Budget not found!';
    header('Location: index.php');
    exit;
}

// Get related transactions count
$transactionStmt = $pdo->prepare("SELECT COUNT(*) FROM Transactions WHERE Category_ID = ? AND System_Log BETWEEN ? AND ?");
$transactionStmt->execute([$budget['Category_ID'], $budget['Start_Date'], $budget['End_Date']]);
$transactionCount = $transactionStmt->fetchColumn();

// Calculate budget usage (mock data)
$budgetUsage = 0; // In real app, calculate from transactions
$remainingBudget = $budget['Monthly_Limit'] - $budgetUsage;

include '../../components/header.php';
?>

<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h3 mb-0 text-gray-800">
                    <i class="fas fa-eye text-primary"></i> View Budget
                </h1>
                <p class="text-muted">Budget details and usage information</p>
            </div>
            <div>
                <a href="edit.php?id=<?php echo $budget['Budget_ID']; ?>" class="btn btn-warning">
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
                    <i class="fas fa-info-circle"></i> Budget Information
                </h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Budget ID</label>
                            <p class="form-control-plaintext"><?php echo htmlspecialchars($budget['Budget_ID']); ?></p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Category</label>
                            <p class="form-control-plaintext"><?php echo htmlspecialchars($budget['category_name']); ?></p>
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Monthly Limit</label>
                    <p class="form-control-plaintext"><?php echo formatCurrency($budget['Monthly_Limit']); ?></p>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Start Date</label>
                            <p class="form-control-plaintext"><?php echo formatDate($budget['Start_Date']); ?></p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">End Date</label>
                            <p class="form-control-plaintext"><?php echo formatDate($budget['End_Date']); ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Budget Usage Card -->
        <div class="card mt-4">
            <div class="card-header">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-chart-bar"></i> Budget Usage
                </h6>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <h4 class="text-primary"><?php echo formatCurrency($budget['Monthly_Limit']); ?></h4>
                            <p class="text-muted mb-0">Total Budget</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <h4 class="text-success"><?php echo formatCurrency($budgetUsage); ?></h4>
                            <p class="text-muted mb-0">Used</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <h4 class="text-info"><?php echo formatCurrency($remainingBudget); ?></h4>
                            <p class="text-muted mb-0">Remaining</p>
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <div class="d-flex justify-content-between">
                        <span>Budget Usage</span>
                        <span><?php echo $budget['Monthly_Limit'] > 0 ? round(($budgetUsage / $budget['Monthly_Limit']) * 100, 1) : 0; ?>%</span>
                    </div>
                    <div class="progress">
                        <div class="progress-bar bg-<?php echo $budgetUsage > $budget['Monthly_Limit'] * 0.8 ? 'danger' : 'success'; ?>" role="progressbar"
                             style="width: <?php echo $budget['Monthly_Limit'] > 0 ? min(($budgetUsage / $budget['Monthly_Limit']) * 100, 100) : 0; ?>%"></div>
                    </div>
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
                    <a href="edit.php?id=<?php echo $budget['Budget_ID']; ?>" class="btn btn-warning">
                        <i class="fas fa-edit"></i> Edit Budget
                    </a>
                    <a href="delete.php?id=<?php echo $budget['Budget_ID']; ?>" class="btn btn-danger delete-btn">
                        <i class="fas fa-trash"></i> Delete Budget
                    </a>
                    <a href="../transactions/create.php?category_id=<?php echo $budget['Category_ID']; ?>" class="btn btn-success">
                        <i class="fas fa-plus"></i> Add Transaction
                    </a>
                </div>
            </div>
        </div>

        <div class="card mt-4">
            <div class="card-header">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-calendar"></i> Budget Period
                </h6>
            </div>
            <div class="card-body">
                <p><strong>Duration:</strong> <?php echo date_diff(date_create($budget['Start_Date']), date_create($budget['End_Date']))->days; ?> days</p>
                <p><strong>Status:</strong>
                    <?php
                    $today = date('Y-m-d');
                    if ($today < $budget['Start_Date']) {
                        echo '<span class="badge bg-secondary">Not Started</span>';
                    } elseif ($today > $budget['End_Date']) {
                        echo '<span class="badge bg-dark">Expired</span>';
                    } else {
                        echo '<span class="badge bg-success">Active</span>';
                    }
                    ?>
                </p>
            </div>
        </div>
    </div>
</div>

<?php include '../../components/footer.php'; ?>