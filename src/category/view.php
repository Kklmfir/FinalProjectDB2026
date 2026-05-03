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

// Fetch category
$stmt = $pdo->prepare("SELECT * FROM category WHERE id = ?");
$stmt->execute([$id]);
$category = $stmt->fetch();

if (!$category) {
    $_SESSION['error'] = 'Category not found!';
    header('Location: index.php');
    exit;
}

// Get related transactions count
$transactionStmt = $pdo->prepare("SELECT COUNT(*) FROM transactions WHERE category_id = ?");
$transactionStmt->execute([$id]);
$transactionCount = $transactionStmt->fetchColumn();

// Get recent transactions in this category
$recentStmt = $pdo->prepare("SELECT * FROM transactions WHERE category_id = ? ORDER BY created_at DESC LIMIT 5");
$recentStmt->execute([$id]);
$recentTransactions = $recentStmt->fetchAll();

include '../../components/header.php';
?>

<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h3 mb-0 text-gray-800">
                    <i class="fas fa-eye text-primary"></i> View Category
                </h1>
                <p class="text-muted">Category details and related information</p>
            </div>
            <div>
                <a href="edit.php?id=<?php echo $category['id']; ?>" class="btn btn-warning">
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
                    <i class="fas fa-info-circle"></i> Category Information
                </h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Category ID</label>
                            <p class="form-control-plaintext"><?php echo htmlspecialchars($category['id']); ?></p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Category Name</label>
                            <p class="form-control-plaintext"><?php echo htmlspecialchars($category['name']); ?></p>
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Description</label>
                    <p class="form-control-plaintext"><?php echo htmlspecialchars($category['description']); ?></p>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Created Date</label>
                            <p class="form-control-plaintext"><?php echo formatDate($category['created_at']); ?></p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Last Updated</label>
                            <p class="form-control-plaintext"><?php echo formatDate($category['updated_at'] ?? $category['created_at']); ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics Card -->
        <div class="card mt-4">
            <div class="card-header">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-chart-bar"></i> Category Statistics
                </h6>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <h4 class="text-primary"><?php echo $transactionCount; ?></h4>
                            <p class="text-muted mb-0">Total Transactions</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <h4 class="text-success">IDR 0</h4>
                            <p class="text-muted mb-0">Total Income</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <h4 class="text-danger">IDR 0</h4>
                            <p class="text-muted mb-0">Total Expense</p>
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
                                <th>Description</th>
                                <th>Amount</th>
                                <th>Type</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recentTransactions as $transaction): ?>
                            <tr>
                                <td><?php echo formatDate($transaction['date']); ?></td>
                                <td><?php echo htmlspecialchars($transaction['description']); ?></td>
                                <td><?php echo formatCurrency($transaction['amount']); ?></td>
                                <td>
                                    <span class="badge bg-<?php echo $transaction['type'] === 'income' ? 'success' : 'danger'; ?>">
                                        <?php echo ucfirst($transaction['type']); ?>
                                    </span>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <div class="text-center mt-3">
                    <a href="../transactions/index.php?category_id=<?php echo $category['id']; ?>" class="btn btn-primary btn-sm">
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
                    <a href="edit.php?id=<?php echo $category['id']; ?>" class="btn btn-warning">
                        <i class="fas fa-edit"></i> Edit Category
                    </a>
                    <a href="delete.php?id=<?php echo $category['id']; ?>" class="btn btn-danger delete-btn">
                        <i class="fas fa-trash"></i> Delete Category
                    </a>
                    <a href="../transactions/create.php?category_id=<?php echo $category['id']; ?>" class="btn btn-success">
                        <i class="fas fa-plus"></i> Add Transaction
                    </a>
                </div>
            </div>
        </div>

        <div class="card mt-4">
            <div class="card-header">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-tags"></i> Related Categories
                </h6>
            </div>
            <div class="card-body">
                <p class="text-muted small">No related categories found.</p>
                <!-- Could show subcategories or similar categories here -->
            </div>
        </div>
    </div>
</div>

<?php include '../../components/footer.php'; ?>