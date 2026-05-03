<?php
/**
 * CRUD Template for Financial Management Dashboard
 *
 * This template shows how to create CRUD operations for any table.
 * Copy this structure and modify for each table in /src/ folder.
 *
 * Tables to implement:
 * - budget
 * - contact
 * - debt_loan
 * - goal
 * - sub_category
 * - transactions
 * - transfer
 *
 * For each table, create:
 * - index.php (list with search/filter/pagination)
 * - create.php (add new record)
 * - edit.php (update record)
 * - delete.php (delete with confirmation)
 * - view.php (detailed view)
 */

// Example for 'transactions' table - modify accordingly

// index.php structure:
/*
<?php
require_once '../../config/app.php';
require_once '../../config/database.php';
require_once '../../helpers/functions.php';
require_once '../../helpers/validation.php';
require_once '../../helpers/security.php';

$pdo = require '../../config/database.php';

// Handle search and filters
$search = isset($_GET['search']) ? sanitize($_GET['search']) : '';
$type = isset($_GET['type']) ? sanitize($_GET['type']) : '';
$category_id = isset($_GET['category_id']) ? (int)$_GET['category_id'] : 0;
$sort = isset($_GET['sort']) ? sanitize($_GET['sort']) : 'date';
$order = isset($_GET['order']) && $_GET['order'] === 'desc' ? 'DESC' : 'ASC';

// Build query
$query = "SELECT t.*, c.name as category_name, p.name as pocket_name
          FROM transactions t
          LEFT JOIN category c ON t.category_id = c.id
          LEFT JOIN pocket p ON t.pocket_id = p.id
          WHERE 1=1";

$params = [];

if ($search) {
    $query .= " AND (t.description LIKE :search OR t.amount LIKE :search)";
    $params[':search'] = '%' . $search . '%';
}

if ($type) {
    $query .= " AND t.type = :type";
    $params[':type'] = $type;
}

if ($category_id) {
    $query .= " AND t.category_id = :category_id";
    $params[':category_id'] = $category_id;
}

$query .= " ORDER BY t.$sort $order";

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
$transactions = $stmt->fetchAll();

// Get total count for pagination
$countQuery = str_replace("SELECT t.*, c.name as category_name, p.name as pocket_name", "SELECT COUNT(*)", $query);
$countQuery = preg_replace('/ORDER BY.*$/', '', $countQuery);
$countQuery = preg_replace('/LIMIT.*$/', '', $countQuery);

$countStmt = $pdo->prepare($countQuery);
$countStmt->execute($params);
$totalRecords = $countStmt->fetchColumn();
$totalPages = ceil($totalRecords / $perPage);

// Get categories for filter
$categoryStmt = $pdo->query("SELECT id, name FROM category ORDER BY name");
$categories = $categoryStmt->fetchAll();

include '../../components/header.php';
?>

<!-- HTML content similar to category/index.php but adapted for transactions -->
<!-- Include search filters, table, pagination -->

<?php include '../../components/footer.php'; ?>
*/

// create.php structure:
/*
<?php
// Similar structure to category/create.php
// Form fields based on table columns
// Validation rules
// Insert query
?>
*/

// edit.php structure:
/*
<?php
// Similar to category/edit.php
// Fetch existing record
// Pre-populate form
// Update query
?>
*/

// delete.php structure:
/*
<?php
// Similar to category/delete.php
// Check dependencies before deletion
// Safe delete with confirmation
?>
*/

// view.php structure:
/*
<?php
// Similar to category/view.php
// Show detailed information
// Related data/statistics
// Action buttons
?>
*/

echo "This is a template file. Copy the structures above to create CRUD for other tables.";
?>