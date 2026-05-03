<?php
require_once '../../config/app.php';
require_once '../../config/database.php';
require_once '../../helpers/functions.php';
require_once '../../helpers/validation.php';
require_once '../../helpers/security.php';

$pdo = require '../../config/database.php';

// Handle search and filters
$search = sanitize($_GET['search'] ?? '');
$category_id = isset($_GET['category_id']) ? (int)$_GET['category_id'] : null;

// Build query
$query = "SELECT sc.*, c.Category_Name, c.Category_Type
          FROM Sub_Category sc
          LEFT JOIN Category c ON sc.Category_ID = c.Category_ID
          WHERE 1=1";

$params = [];

if ($search) {
    $query .= " AND (sc.Sub_Category_Name LIKE ? OR sc.Description LIKE ? OR c.Category_Name LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if ($category_id) {
    $query .= " AND sc.Category_ID = ?";
    $params[] = $category_id;
}

$query .= " ORDER BY c.Category_Name, sc.Sub_Category_Name";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$subCategories = $stmt->fetchAll();

// Get summary statistics
$totalStmt = $pdo->prepare("SELECT COUNT(*) FROM Sub_Category");
$totalStmt->execute();
$totalCount = $totalStmt->fetchColumn();

// Get categories for filter dropdown
$categoriesStmt = $pdo->query("SELECT Category_ID, Category_Name, Category_Type FROM Category ORDER BY Category_Name");
$categories = $categoriesStmt->fetchAll();

include '../../components/header.php';
?>

<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h3 mb-0 text-gray-800">
                    <i class="fas fa-tags text-primary"></i> Sub Categories
                </h1>
                <p class="text-muted">Manage sub categories for better transaction organization</p>
            </div>
            <a href="create.php" class="btn btn-primary">
                <i class="fas fa-plus"></i> Add Sub Category
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
                            Total Sub Categories</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $totalCount; ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-tags fa-2x text-gray-300"></i>
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
                            Income Categories</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            <?php
                            $incomeCount = array_reduce($subCategories, function($count, $sc) {
                                return $count + ($sc['Category_Type'] === 'Income' ? 1 : 0);
                            }, 0);
                            echo $incomeCount;
                            ?>
                        </div>
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
                            Expense Categories</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            <?php
                            $expenseCount = array_reduce($subCategories, function($count, $sc) {
                                return $count + ($sc['Category_Type'] === 'Expense' ? 1 : 0);
                            }, 0);
                            echo $expenseCount;
                            ?>
                        </div>
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
                            Parent Categories</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo count($categories); ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-folder fa-2x text-gray-300"></i>
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
            <div class="col-md-5">
                <input type="text" class="form-control" name="search" placeholder="Search by sub category name, description, or parent category..."
                       value="<?php echo htmlspecialchars($search); ?>">
            </div>
            <div class="col-md-4">
                <select class="form-control" name="category_id">
                    <option value="">All Parent Categories</option>
                    <?php foreach ($categories as $category): ?>
                    <option value="<?php echo $category['Category_ID']; ?>"
                            <?php echo $category_id == $category['Category_ID'] ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($category['Category_Name']); ?> (<?php echo htmlspecialchars($category['Category_Type']); ?>)
                    </option>
                    <?php endforeach; ?>
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
            <i class="fas fa-table"></i> Sub Categories List
        </h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered datatable" id="subCategoryTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Sub Category Name</th>
                        <th>Parent Category</th>
                        <th>Type</th>
                        <th>Description</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($subCategories as $subCategory): ?>
                    <tr>
                        <td><?php echo $subCategory['Sub_Category_ID']; ?></td>
                        <td><?php echo htmlspecialchars($subCategory['Sub_Category_Name']); ?></td>
                        <td><?php echo htmlspecialchars($subCategory['Category_Name'] ?? 'N/A'); ?></td>
                        <td>
                            <span class="badge bg-<?php echo $subCategory['Category_Type'] === 'Income' ? 'success' : 'danger'; ?>">
                                <?php echo htmlspecialchars($subCategory['Category_Type'] ?? 'N/A'); ?>
                            </span>
                        </td>
                        <td><?php echo htmlspecialchars($subCategory['Description'] ?? ''); ?></td>
                        <td>
                            <div class="btn-group" role="group">
                                <a href="view.php?id=<?php echo $subCategory['Sub_Category_ID']; ?>" class="btn btn-sm btn-info" title="View">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="edit.php?id=<?php echo $subCategory['Sub_Category_ID']; ?>" class="btn btn-sm btn-warning" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="delete.php?id=<?php echo $subCategory['Sub_Category_ID']; ?>" class="btn btn-sm btn-danger delete-btn" title="Delete">
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