<?php
require_once '../../config/app.php';
require_once '../../config/database.php';
require_once '../../helpers/functions.php';
require_once '../../helpers/validation.php';
require_once '../../helpers/security.php';

$pdo = require '../../config/database.php';

// Handle search and filters
$search = isset($_GET['search']) ? sanitize($_GET['search']) : '';
$relation_type = isset($_GET['relation_type']) ? sanitize($_GET['relation_type']) : '';
$sort = isset($_GET['sort']) ? sanitize($_GET['sort']) : 'Contact_ID';
$order = isset($_GET['order']) && $_GET['order'] === 'desc' ? 'DESC' : 'ASC';

// Build query
$query = "SELECT Contact_ID as id, Contact_Name as name, Phone_Number as phone, Relation_Type as relation_type FROM Contact WHERE 1=1";

$params = [];

if ($search) {
    $query .= " AND (Contact_Name LIKE :search OR Phone_Number LIKE :search)";
    $params[':search'] = '%' . $search . '%';
}

if ($relation_type) {
    $query .= " AND Relation_Type = :relation_type";
    $params[':relation_type'] = $relation_type;
}

$query .= " ORDER BY $sort $order";

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
$contacts = $stmt->fetchAll();

// Get total count for pagination
$countQuery = "SELECT COUNT(*) FROM Contact WHERE 1=1";
$countParams = [];
if ($search) {
    $countQuery .= " AND (Contact_Name LIKE :search OR Phone_Number LIKE :search)";
    $countParams[':search'] = '%' . $search . '%';
}
if ($relation_type) {
    $countQuery .= " AND Relation_Type = :relation_type";
    $countParams[':relation_type'] = $relation_type;
}
$countStmt = $pdo->prepare($countQuery);
$countStmt->execute($countParams);
$totalRecords = $countStmt->fetchColumn();
$totalPages = ceil($totalRecords / $perPage);

// Get unique relation types
$relationStmt = $pdo->query("SELECT DISTINCT Relation_Type FROM Contact ORDER BY Relation_Type");
$relationTypes = $relationStmt->fetchAll(PDO::FETCH_COLUMN);

include '../../components/header.php';
?>

<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h3 mb-0 text-gray-800">
                    <i class="fas fa-address-book text-primary"></i> Contact Management
                </h1>
                <p class="text-muted">Manage your contacts and relationships</p>
            </div>
            <a href="create.php" class="btn btn-primary">
                <i class="fas fa-plus"></i> Add Contact
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
                <input type="text" name="search" class="form-control" placeholder="Search contacts..." value="<?php echo htmlspecialchars($search); ?>">
            </div>
            <div class="col-md-3">
                <select name="relation_type" class="form-select">
                    <option value="">All Relation Types</option>
                    <?php foreach ($relationTypes as $type): ?>
                    <option value="<?php echo $type; ?>" <?php echo $relation_type === $type ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($type); ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <select name="sort" class="form-select">
                    <option value="Contact_ID" <?php echo $sort === 'Contact_ID' ? 'selected' : ''; ?>>ID</option>
                    <option value="Contact_Name" <?php echo $sort === 'Contact_Name' ? 'selected' : ''; ?>>Name</option>
                    <option value="Relation_Type" <?php echo $sort === 'Relation_Type' ? 'selected' : ''; ?>>Relation Type</option>
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

<!-- Contacts Table -->
<div class="card">
    <div class="card-header">
        <h6 class="m-0 font-weight-bold text-primary">
            <i class="fas fa-table"></i> Contacts (<?php echo $totalRecords; ?>)
        </h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered datatable" id="dataTable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Phone</th>
                        <th>Relation Type</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($contacts as $contact): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($contact['id']); ?></td>
                        <td><?php echo htmlspecialchars($contact['name']); ?></td>
                        <td><?php echo htmlspecialchars($contact['phone']); ?></td>
                        <td><?php echo htmlspecialchars($contact['relation_type']); ?></td>
                        <td>
                            <a href="view.php?id=<?php echo $contact['id']; ?>" class="btn btn-sm btn-info" title="View">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="edit.php?id=<?php echo $contact['id']; ?>" class="btn btn-sm btn-warning" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="delete.php?id=<?php echo $contact['id']; ?>" class="btn btn-sm btn-danger delete-btn" title="Delete">
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
                    <a class="page-link" href="?page=<?php echo $page - 1; ?>&search=<?php echo urlencode($search); ?>&relation_type=<?php echo urlencode($relation_type); ?>&sort=<?php echo $sort; ?>&order=<?php echo strtolower($order); ?>">
                        <i class="fas fa-chevron-left"></i>
                    </a>
                </li>
                <?php endif; ?>

                <?php for ($i = max(1, $page - 2); $i <= min($totalPages, $page + 2); $i++): ?>
                <li class="page-item <?php echo $i === $page ? 'active' : ''; ?>">
                    <a class="page-link" href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>&relation_type=<?php echo urlencode($relation_type); ?>&sort=<?php echo $sort; ?>&order=<?php echo strtolower($order); ?>">
                        <?php echo $i; ?>
                    </a>
                </li>
                <?php endfor; ?>

                <?php if ($page < $totalPages): ?>
                <li class="page-item">
                    <a class="page-link" href="?page=<?php echo $page + 1; ?>&search=<?php echo urlencode($search); ?>&relation_type=<?php echo urlencode($relation_type); ?>&sort=<?php echo $sort; ?>&order=<?php echo strtolower($order); ?>">
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