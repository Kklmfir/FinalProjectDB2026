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

// Fetch transaction with related data
$stmt = $pdo->prepare("SELECT t.*, p.Pocket_Name, c.Category_Name, c.Category_Type, sc.Sub_Category_Name
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

// Get related transactions from same category
$relatedStmt = $pdo->prepare("SELECT t.*, p.Pocket_Name
                              FROM Transactions t
                              LEFT JOIN Pocket p ON t.Pocket_ID = p.Pocket_ID
                              WHERE t.Category_ID = ? AND t.Transaction_ID != ?
                              ORDER BY t.Transaction_Date DESC LIMIT 5");
$relatedStmt->execute([$transaction['Category_ID'], $id]);
$relatedTransactions = $relatedStmt->fetchAll();

// Get pocket balance
$pocketBalanceStmt = $pdo->prepare("SELECT
    (SELECT COALESCE(SUM(Amount), 0) FROM Transactions WHERE Pocket_ID = ? AND Type = 'Income') -
    (SELECT COALESCE(SUM(Amount), 0) FROM Transactions WHERE Pocket_ID = ? AND Type = 'Expense') as balance");
$pocketBalanceStmt->execute([$transaction['Pocket_ID'], $transaction['Pocket_ID']]);
$pocketBalance = $pocketBalanceStmt->fetchColumn() ?: 0;

include '../../components/header.php';
?>

<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h3 mb-0 text-gray-800">
                    <i class="fas fa-eye text-primary"></i> View Transaction
                </h1>
                <p class="text-muted">Detailed transaction information</p>
            </div>
            <div>
                <a href="edit.php?id=<?php echo $transaction['Transaction_ID']; ?>" class="btn btn-warning">
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
                    <i class="fas fa-info-circle"></i> Transaction Details
                </h6>
            </div>
            <div class="card-body">
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
                            <p class="form-control-plaintext fw-bold text-<?php echo $transaction['Type'] === 'Income' ? 'success' : 'danger'; ?> fs-5">
                                <?php echo formatCurrency($transaction['Amount']); ?>
                            </p>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Category</label>
                            <p class="form-control-plaintext">
                                <i class="fas fa-tag text-primary"></i> <?php echo htmlspecialchars($transaction['Category_Name'] ?? 'N/A'); ?>
                                <span class="badge bg-secondary ms-1"><?php echo htmlspecialchars($transaction['Category_Type'] ?? ''); ?></span>
                            </p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Sub Category</label>
                            <p class="form-control-plaintext">
                                <i class="fas fa-tags text-info"></i> <?php echo htmlspecialchars($transaction['Sub_Category_Name'] ?? 'N/A'); ?>
                            </p>
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Pocket</label>
                    <p class="form-control-plaintext">
                        <i class="fas fa-wallet text-warning"></i> <?php echo htmlspecialchars($transaction['Pocket_Name'] ?? 'N/A'); ?>
                    </p>
                </div>

                <?php if ($transaction['Description']): ?>
                <div class="mb-3">
                    <label class="form-label fw-bold">Description</label>
                    <p class="form-control-plaintext"><?php echo nl2br(htmlspecialchars($transaction['Description'])); ?></p>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Pocket Balance Section -->
        <div class="card mt-4">
            <div class="card-header">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-balance-scale"></i> Pocket Balance
                </h6>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <h4 class="text-<?php echo $pocketBalance >= 0 ? 'success' : 'danger'; ?>">
                                <?php echo formatCurrency($pocketBalance); ?>
                            </h4>
                            <p class="text-muted mb-0">Current Balance</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <h4 class="text-primary">
                                <?php echo htmlspecialchars($transaction['Pocket_Name'] ?? 'N/A'); ?>
                            </h4>
                            <p class="text-muted mb-0">Pocket Name</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Related Transactions -->
        <?php if ($relatedTransactions): ?>
        <div class="card mt-4">
            <div class="card-header">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-link"></i> Related Transactions
                </h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Type</th>
                                <th>Amount</th>
                                <th>Pocket</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($relatedTransactions as $related): ?>
                            <tr>
                                <td><?php echo formatDate($related['Transaction_Date']); ?></td>
                                <td>
                                    <span class="badge bg-<?php echo $related['Type'] === 'Income' ? 'success' : 'danger'; ?>">
                                        <?php echo htmlspecialchars($related['Type']); ?>
                                    </span>
                                </td>
                                <td><?php echo formatCurrency($related['Amount']); ?></td>
                                <td><?php echo htmlspecialchars($related['Pocket_Name'] ?? 'N/A'); ?></td>
                                <td>
                                    <a href="view.php?id=<?php echo $related['Transaction_ID']; ?>" class="btn btn-sm btn-outline-primary">
                                        View
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <div class="text-center mt-3">
                    <a href="index.php?category_id=<?php echo $transaction['Category_ID']; ?>" class="btn btn-primary btn-sm">
                        View All <?php echo htmlspecialchars($transaction['Category_Name'] ?? 'Category'); ?> Transactions
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
                    <a href="edit.php?id=<?php echo $transaction['Transaction_ID']; ?>" class="btn btn-warning">
                        <i class="fas fa-edit"></i> Edit Transaction
                    </a>
                    <a href="delete.php?id=<?php echo $transaction['Transaction_ID']; ?>" class="btn btn-danger delete-btn">
                        <i class="fas fa-trash"></i> Delete Transaction
                    </a>
                    <a href="create.php?category_id=<?php echo $transaction['Category_ID']; ?>&pocket_id=<?php echo $transaction['Pocket_ID']; ?>" class="btn btn-success">
                        <i class="fas fa-plus"></i> Add Similar Transaction
                    </a>
                    <a href="../pocket/view.php?id=<?php echo $transaction['Pocket_ID']; ?>" class="btn btn-outline-primary">
                        <i class="fas fa-wallet"></i> View Pocket
                    </a>
                    <a href="../category/view.php?id=<?php echo $transaction['Category_ID']; ?>" class="btn btn-outline-primary">
                        <i class="fas fa-tag"></i> View Category
                    </a>
                </div>
            </div>
        </div>

        <div class="card mt-4">
            <div class="card-header">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-chart-pie"></i> Transaction Summary
                </h6>
            </div>
            <div class="card-body">
                <p><strong>Type:</strong> <?php echo htmlspecialchars($transaction['Type']); ?></p>
                <p><strong>Amount:</strong> <?php echo formatCurrency($transaction['Amount']); ?></p>
                <p><strong>Category:</strong> <?php echo htmlspecialchars($transaction['Category_Name'] ?? 'N/A'); ?></p>
                <p><strong>Pocket:</strong> <?php echo htmlspecialchars($transaction['Pocket_Name'] ?? 'N/A'); ?></p>
                <p><strong>Date:</strong> <?php echo formatDate($transaction['Transaction_Date']); ?></p>
            </div>
        </div>

        <div class="card mt-4">
            <div class="card-header">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-tags"></i> Classification
                </h6>
            </div>
            <div class="card-body">
                <p class="small text-muted">This transaction is classified under the selected category and sub category for better financial tracking and reporting.</p>
                <ul class="small">
                    <li>Categories help organize similar transactions</li>
                    <li>Sub categories provide detailed classification</li>
                    <li>Pockets represent different money sources</li>
                    <li>Proper classification enables accurate budgeting</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<?php include '../../components/footer.php'; ?>