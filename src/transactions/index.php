<?php
require_once '../../config/app.php';
require_once '../../config/database.php';
require_once '../../helpers/functions.php';
require_once '../../helpers/validation.php';
require_once '../../helpers/security.php';

$pdo = require '../../config/database.php';

// Handle search and filters
$search = sanitize($_GET['search'] ?? '');
$type = sanitize($_GET['type'] ?? '');
$category_id = isset($_GET['category_id']) ? (int)$_GET['category_id'] : null;
$sub_category_id = isset($_GET['sub_category_id']) ? (int)$_GET['sub_category_id'] : null;
$pocket_id = isset($_GET['pocket_id']) ? (int)$_GET['pocket_id'] : null;
$date_from = sanitize($_GET['date_from'] ?? '');
$date_to = sanitize($_GET['date_to'] ?? '');

// Build query
$query = "SELECT t.*, p.Pocket_Name, c.Category_Name, sc.Sub_Category_Name
          FROM Transactions t
          LEFT JOIN Pocket p ON t.Pocket_ID = p.Pocket_ID
          LEFT JOIN Category c ON t.Category_ID = c.Category_ID
          LEFT JOIN Sub_Category sc ON t.Sub_Category_ID = sc.Sub_Category_ID
          WHERE 1=1";

$params = [];

if ($search) {
    $query .= " AND (t.Description LIKE ? OR c.Category_Name LIKE ? OR sc.Sub_Category_Name LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if ($type) {
    $query .= " AND t.Type = ?";
    $params[] = $type;
}

if ($category_id) {
    $query .= " AND t.Category_ID = ?";
    $params[] = $category_id;
}

if ($sub_category_id) {
    $query .= " AND t.Sub_Category_ID = ?";
    $params[] = $sub_category_id;
}

if ($pocket_id) {
    $query .= " AND t.Pocket_ID = ?";
    $params[] = $pocket_id;
}

if ($date_from) {
    $query .= " AND t.Transaction_Date >= ?";
    $params[] = $date_from;
}

if ($date_to) {
    $query .= " AND t.Transaction_Date <= ?";
    $params[] = $date_to;
}

$query .= " ORDER BY t.Transaction_Date DESC, t.Transaction_ID DESC";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$transactions = $stmt->fetchAll();

// Get summary statistics
$totalStmt = $pdo->prepare("SELECT COUNT(*) FROM Transactions");
$totalStmt->execute();
$totalCount = $totalStmt->fetchColumn();

$incomeStmt = $pdo->prepare("SELECT SUM(Amount) FROM Transactions WHERE Type = 'Income'");
$incomeStmt->execute();
$totalIncome = $incomeStmt->fetchColumn() ?: 0;

$expenseStmt = $pdo->prepare("SELECT SUM(Amount) FROM Transactions WHERE Type = 'Expense'");
$expenseStmt->execute();
$totalExpense = $expenseStmt->fetchColumn() ?: 0;

// Get filter options
$categoriesStmt = $pdo->query("SELECT Category_ID, Category_Name, Category_Type FROM Category ORDER BY Category_Name");
$categories = $categoriesStmt->fetchAll();

$subCategoriesStmt = $pdo->query("SELECT sc.Sub_Category_ID, sc.Sub_Category_Name, c.Category_Name
                                  FROM Sub_Category sc
                                  LEFT JOIN Category c ON sc.Category_ID = c.Category_ID
                                  ORDER BY c.Category_Name, sc.Sub_Category_Name");
$subCategories = $subCategoriesStmt->fetchAll();

$pocketsStmt = $pdo->query("SELECT Pocket_ID, Pocket_Name FROM Pocket ORDER BY Pocket_Name");
$pockets = $pocketsStmt->fetchAll();

include '../../components/header.php';
?>

<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h3 mb-0 text-gray-800">
                    <i class="fas fa-exchange-alt text-primary"></i> Transaction Management
                </h1>
                <p class="text-muted">Track all your income and expense transactions</p>
            </div>
            <a href="create.php" class="btn btn-primary">
                <i class="fas fa-plus"></i> Add Transaction
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
                            Total Transactions</div>
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
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            Total Income</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo formatCurrency($totalIncome); ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-arrow-up fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-danger shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                            Total Expenses</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo formatCurrency($totalExpense); ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-arrow-down fa-2x text-gray-300"></i>
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
                            Net Balance</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo formatCurrency($totalIncome - $totalExpense); ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-balance-scale fa-2x text-gray-300"></i>
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
            <div class="col-md-3">
                <input type="text" class="form-control" name="search" placeholder="Search description, category..."
                       value="<?php echo htmlspecialchars($search); ?>">
            </div>
            <div class="col-md-2">
                <select class="form-control" name="type">
                    <option value="">All Types</option>
                    <option value="Income" <?php echo $type === 'Income' ? 'selected' : ''; ?>>Income</option>
                    <option value="Expense" <?php echo $type === 'Expense' ? 'selected' : ''; ?>>Expense</option>
                </select>
            </div>
            <div class="col-md-2">
                <select class="form-control" name="category_id">
                    <option value="">All Categories</option>
                    <?php foreach ($categories as $category): ?>
                    <option value="<?php echo $category['Category_ID']; ?>"
                            <?php echo $category_id == $category['Category_ID'] ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($category['Category_Name']); ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <input type="date" class="form-control" name="date_from" value="<?php echo htmlspecialchars($date_from); ?>">
            </div>
            <div class="col-md-2">
                <input type="date" class="form-control" name="date_to" value="<?php echo htmlspecialchars($date_to); ?>">
            </div>
            <div class="col-md-1">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search"></i>
                </button>
                <a href="index.php" class="btn btn-secondary">
                    <i class="fas fa-times"></i>
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Data Table -->
<div class="card">
    <div class="card-header">
        <h6 class="m-0 font-weight-bold text-primary">
            <i class="fas fa-table"></i> Transactions
        </h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered datatable" id="transactionTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Date</th>
                        <th>Type</th>
                        <th>Amount</th>
                        <th>Category</th>
                        <th>Sub Category</th>
                        <th>Pocket</th>
                        <th>Description</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($transactions as $transaction): ?>
                    <tr>
                        <td><?php echo $transaction['Transaction_ID']; ?></td>
                        <td><?php echo formatDate($transaction['Transaction_Date']); ?></td>
                        <td>
                            <span class="badge bg-<?php echo $transaction['Type'] === 'Income' ? 'success' : 'danger'; ?>">
                                <?php echo htmlspecialchars($transaction['Type']); ?>
                            </span>
                        </td>
                        <td><?php echo formatCurrency($transaction['Amount']); ?></td>
                        <td><?php echo htmlspecialchars($transaction['Category_Name'] ?? 'N/A'); ?></td>
                        <td><?php echo htmlspecialchars($transaction['Sub_Category_Name'] ?? 'N/A'); ?></td>
                        <td><?php echo htmlspecialchars($transaction['Pocket_Name'] ?? 'N/A'); ?></td>
                        <td><?php echo htmlspecialchars($transaction['Description'] ?? ''); ?></td>
                        <td>
                            <div class="btn-group" role="group">
                                <a href="view.php?id=<?php echo $transaction['Transaction_ID']; ?>" class="btn btn-sm btn-info" title="View">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="edit.php?id=<?php echo $transaction['Transaction_ID']; ?>" class="btn btn-sm btn-warning" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="delete.php?id=<?php echo $transaction['Transaction_ID']; ?>" class="btn btn-sm btn-danger delete-btn" title="Delete">
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