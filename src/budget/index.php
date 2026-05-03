<?php
require_once '../../config/app.php';
require_once '../../config/database.php';
require_once '../../helpers/functions.php';
require_once '../../helpers/validation.php';
require_once '../../helpers/security.php';

$pdo = require '../../config/database.php';

// Handle search and filters
$search = isset($_GET['search']) ? sanitize($_GET['search']) : '';
$category_id = isset($_GET['category_id']) ? (int)$_GET['category_id'] : 0;
$sort = isset($_GET['sort']) ? sanitize($_GET['sort']) : 'Budget_ID';
$order = isset($_GET['order']) && $_GET['order'] === 'desc' ? 'DESC' : 'ASC';

// Build query
$query = "SELECT b.Budget_ID as id, b.Monthly_Limit as monthly_limit, b.Start_Date as start_date, b.End_Date as end_date,
                 c.Category_Name as category_name
          FROM Budget b
          LEFT JOIN Category c ON b.Category_ID = c.Category_ID
          WHERE 1=1";

$params = [];

if ($search) {
    $query .= " AND (c.Category_Name LIKE :search OR b.Monthly_Limit LIKE :search)";
    $params[':search'] = '%' . $search . '%';
}

if ($category_id) {
    $query .= " AND b.Category_ID = :category_id";
    $params[':category_id'] = $category_id;
}

$query .= " ORDER BY b.$sort $order";

// Pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$perPage = 10;
$offset = ($page - 1) * $perPage;

$query .= " LIMIT :limit OFFSET :offset";
$params[':limit'] = $perPage;
$params[':offset'] = $offset;

// Execute query
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$budgets = $stmt->fetchAll();

// Get total count for pagination
$countQuery = "SELECT COUNT(*) FROM Budget b LEFT JOIN Category c ON b.Category_ID = c.Category_ID WHERE 1=1";
$countParams = [];
if ($search) {
    $countQuery .= " AND (c.Category_Name LIKE :search OR b.Monthly_Limit LIKE :search)";
    $countParams[':search'] = '%' . $search . '%';
}
if ($category_id) {
    $countQuery .= " AND b.Category_ID = :category_id";
    $countParams[':category_id'] = $category_id;
}
$countStmt = $pdo->prepare($countQuery);
$countStmt->execute($countParams);
$totalRecords = $countStmt->fetchColumn();
$totalPages = ceil($totalRecords / $perPage);

// Get categories for filter
$categoryStmt = $pdo->query("SELECT Category_ID as id, Category_Name as name FROM Category ORDER BY Category_Name");
$categories = $categoryStmt->fetchAll();

include '../../components/header.php';
?>

<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h3 mb-0 text-gray-800">
                    <i class="fas fa-chart-line text-primary"></i> Budget Management
                </h1>
                <p class="text-muted">Manage your spending budgets</p>
            </div>
            <a href="create.php" class="btn btn-primary">
                <i class="fas fa-plus"></i> Add Budget
            </a>
        </div>
    </div>
</div>

<?php include '../../components/alerts.php'; ?>

<!-- Search and Filter -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-3">
            <div class="col-md-4">
                <input type="text" name="search" class="form-control" placeholder="Search budgets..." value="<?php echo htmlspecialchars($search); ?>">
            </div>
            <div class="col-md-3">
                <select name="category_id" class="form-select">
                    <option value="">All Categories</option>
                    <?php foreach ($categories as $cat): ?>
                    <option value="<?php echo $cat['id']; ?>" <?php echo $category_id === $cat['id'] ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($cat['name']); ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <select name="sort" class="form-select">
                    <option value="Budget_ID" <?php echo $sort === 'Budget_ID' ? 'selected' : ''; ?>>ID</option>
                    <option value="Monthly_Limit" <?php echo $sort === 'Monthly_Limit' ? 'selected' : ''; ?>>Monthly Limit</option>
                    <option value="Start_Date" <?php echo $sort === 'Start_Date' ? 'selected' : ''; ?>>Start Date</option>
                </select>
            </div>
            <div class="col-md-2">
                <select name="order" class="form-select">
                    <option value="asc" <?php echo $order === 'ASC' ? 'selected' : ''; ?>>Ascending</option>
                    <option value="desc" <?php echo $order === 'DESC' ? 'selected' : ''; ?>>Descending</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-outline-primary w-100">
                    <i class="fas fa-search"></i> Filter
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Budgets Table -->
<div class="card">
    <div class="card-header">
        <h6 class="m-0 font-weight-bold text-primary">
            <i class="fas fa-table"></i> Budgets (<?php echo $totalRecords; ?>)
        </h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered datatable" id="dataTable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Category</th>
                        <th>Monthly Limit</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($budgets as $budget): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($budget['id']); ?></td>
                        <td><?php echo htmlspecialchars($budget['category_name']); ?></td>
                        <td><?php echo formatCurrency($budget['monthly_limit']); ?></td>
                        <td><?php echo formatDate($budget['start_date']); ?></td>
                        <td><?php echo formatDate($budget['end_date']); ?></td>
                        <td>
                            <a href="view.php?id=<?php echo $budget['id']; ?>" class="btn btn-sm btn-info" title="View">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="edit.php?id=<?php echo $budget['id']; ?>" class="btn btn-sm btn-warning" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="delete.php?id=<?php echo $budget['id']; ?>" class="btn btn-sm btn-danger delete-btn" title="Delete">
                                <i class="fas fa-trash"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <?php if ($totalPages > 1): ?>
        <nav aria-label="Page navigation" class="mt-4">
            <ul class="pagination justify-content-center">
                <?php if ($page > 1): ?>
                <li class="page-item">
                    <a class="page-link" href="?page=<?php echo $page - 1; ?>&search=<?php echo urlencode($search); ?>&category_id=<?php echo $category_id; ?>&sort=<?php echo $sort; ?>&order=<?php echo strtolower($order); ?>">
                        <i class="fas fa-chevron-left"></i>
                    </a>
                </li>
                <?php endif; ?>

                <?php for ($i = max(1, $page - 2); $i <= min($totalPages, $page + 2); $i++): ?>
                <li class="page-item <?php echo $i === $page ? 'active' : ''; ?>">
                    <a class="page-link" href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>&category_id=<?php echo $category_id; ?>&sort=<?php echo $sort; ?>&order=<?php echo strtolower($order); ?>">
                        <?php echo $i; ?>
                    </a>
                </li>
                <?php endfor; ?>

                <?php if ($page < $totalPages): ?>
                <li class="page-item">
                    <a class="page-link" href="?page=<?php echo $page + 1; ?>&search=<?php echo urlencode($search); ?>&category_id=<?php echo $category_id; ?>&sort=<?php echo $sort; ?>&order=<?php echo strtolower($order); ?>">
                        <i class="fas fa-chevron-right"></i>
                    </a>
                </li>
                <?php endif; ?>
            </ul>
        </nav>
        <?php endif; ?>
    </div>
</div>

<?php include '../../components/footer.php'; ?>