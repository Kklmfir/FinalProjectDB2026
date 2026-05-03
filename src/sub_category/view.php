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

// Fetch sub category with related data
$stmt = $pdo->prepare("SELECT sc.*, c.Category_Name, c.Category_Type
                       FROM Sub_Category sc
                       LEFT JOIN Category c ON sc.Category_ID = c.Category_ID
                       WHERE sc.Sub_Category_ID = ?");
$stmt->execute([$id]);
$subCategory = $stmt->fetch();

if (!$subCategory) {
    $_SESSION['error'] = 'Sub category not found!';
    header('Location: index.php');
    exit;
}

// Get transaction statistics
$transactionStmt = $pdo->prepare("SELECT COUNT(*) FROM Transactions WHERE Sub_Category_ID = ?");
$transactionStmt->execute([$id]);
$transactionCount = $transactionStmt->fetchColumn();

// Get total amount from transactions
$amountStmt = $pdo->prepare("SELECT SUM(Amount) FROM Transactions WHERE Sub_Category_ID = ?");
$amountStmt->execute([$id]);
$totalAmount = $amountStmt->fetchColumn() ?: 0;

// Get recent transactions
$recentTransactionsStmt = $pdo->prepare("SELECT * FROM Transactions WHERE Sub_Category_ID = ? ORDER BY Transaction_Date DESC LIMIT 5");
$recentTransactionsStmt->execute([$id]);
$recentTransactions = $recentTransactionsStmt->fetchAll();

include '../../components/header.php';
?>

<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h3 mb-0 text-gray-800">
                    <i class="fas fa-eye text-primary"></i> View Sub Category
                </h1>
                <p class="text-muted">Detailed sub category information and usage</p>
            </div>
            <div>
                <a href="edit.php?id=<?php echo $subCategory['Sub_Category_ID']; ?>" class="btn btn-warning">
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
                    <i class="fas fa-info-circle"></i> Sub Category Information
                </h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Sub Category ID</label>
                            <p class="form-control-plaintext"><?php echo htmlspecialchars($subCategory['Sub_Category_ID']); ?></p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Category Type</label>
                            <p class="form-control-plaintext">
                                <span class="badge bg-<?php echo $subCategory['Category_Type'] === 'Income' ? 'success' : 'danger'; ?> fs-6">
                                    <?php echo htmlspecialchars($subCategory['Category_Type'] ?? 'N/A'); ?>
                                </span>
                            </p>
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Sub Category Name</label>
                    <p class="form-control-plaintext"><?php echo htmlspecialchars($subCategory['Sub_Category_Name']); ?></p>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Parent Category</label>
                    <p class="form-control-plaintext">
                        <i class="fas fa-folder text-primary"></i> <?php echo htmlspecialchars($subCategory['Category_Name'] ?? 'N/A'); ?>
                    </p>
                </div>

                <?php if ($subCategory['Description']): ?>
                <div class="mb-3">
                    <label class="form-label fw-bold">Description</label>
                    <p class="form-control-plaintext"><?php echo nl2br(htmlspecialchars($subCategory['Description'])); ?></p>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Statistics Section -->
        <div class="card mt-4">
            <div class="card-header">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-chart-bar"></i> Usage Statistics
                </h6>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <h4 class="text-primary"><?php echo $transactionCount; ?></h4>
                            <p class="text-muted mb-0">Total Transactions</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <h4 class="text-<?php echo $subCategory['Category_Type'] === 'Income' ? 'success' : 'danger'; ?>">
                                <?php echo formatCurrency($totalAmount); ?>
                            </h4>
                            <p class="text-muted mb-0">Total Amount</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Transactions -->
        <?php if ($recentTransactions): ?>
        <div class="card mt-4">
            <div class="card-header">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-history"></i> Recent Transactions
                </h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Amount</th>
                                <th>Description</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recentTransactions as $transaction): ?>
                            <tr>
                                <td><?php echo formatDate($transaction['Transaction_Date']); ?></td>
                                <td><?php echo formatCurrency($transaction['Amount']); ?></td>
                                <td><?php echo htmlspecialchars($transaction['Description'] ?? ''); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <div class="text-center mt-3">
                    <a href="../transactions/index.php?sub_category_id=<?php echo $subCategory['Sub_Category_ID']; ?>" class="btn btn-primary btn-sm">
                        View All Transactions
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
                    <a href="edit.php?id=<?php echo $subCategory['Sub_Category_ID']; ?>" class="btn btn-warning">
                        <i class="fas fa-edit"></i> Edit Sub Category
                    </a>
                    <a href="delete.php?id=<?php echo $subCategory['Sub_Category_ID']; ?>" class="btn btn-danger delete-btn">
                        <i class="fas fa-trash"></i> Delete Sub Category
                    </a>
                    <a href="../transactions/create.php?sub_category_id=<?php echo $subCategory['Sub_Category_ID']; ?>" class="btn btn-success">
                        <i class="fas fa-plus"></i> Add Transaction
                    </a>
                    <a href="../category/view.php?id=<?php echo $subCategory['Category_ID']; ?>" class="btn btn-outline-primary">
                        <i class="fas fa-folder"></i> View Parent Category
                    </a>
                </div>
            </div>
        </div>

        <div class="card mt-4">
            <div class="card-header">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-chart-pie"></i> Category Summary
                </h6>
            </div>
            <div class="card-body">
                <p><strong>Type:</strong> <?php echo htmlspecialchars($subCategory['Category_Type'] ?? 'N/A'); ?></p>
                <p><strong>Parent:</strong> <?php echo htmlspecialchars($subCategory['Category_Name'] ?? 'N/A'); ?></p>
                <p><strong>Usage:</strong> <?php echo $transactionCount > 0 ? 'Active' : 'Unused'; ?></p>
            </div>
        </div>

        <div class="card mt-4">
            <div class="card-header">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-tags"></i> Related Categories
                </h6>
            </div>
            <div class="card-body">
                <p class="small text-muted">This sub category belongs to the parent category and is used for detailed transaction classification.</p>
                <ul class="small">
                    <li>Helps in detailed expense/income tracking</li>
                    <li>Allows for better budget planning</li>
                    <li>Enables comprehensive financial reporting</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<?php include '../../components/footer.php'; ?>