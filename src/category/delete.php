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

// Fetch category to confirm
$stmt = $pdo->prepare("SELECT * FROM category WHERE id = ?");
$stmt->execute([$id]);
$category = $stmt->fetch();

if (!$category) {
    $_SESSION['error'] = 'Category not found!';
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    validateCSRF();

    try {
        // Check if category is being used in transactions
        $checkStmt = $pdo->prepare("SELECT COUNT(*) FROM transactions WHERE category_id = ?");
        $checkStmt->execute([$id]);
        $usageCount = $checkStmt->fetchColumn();

        if ($usageCount > 0) {
            $_SESSION['error'] = 'Cannot delete category. It is being used in ' . $usageCount . ' transaction(s).';
            header('Location: index.php');
            exit;
        }

        // Delete category
        $stmt = $pdo->prepare("DELETE FROM category WHERE id = ?");
        $stmt->execute([$id]);

        $_SESSION['success'] = 'Category deleted successfully!';
        header('Location: index.php');
        exit;
    } catch (PDOException $e) {
        $_SESSION['error'] = 'Failed to delete category: ' . $e->getMessage();
        header('Location: index.php');
        exit;
    }
}

include '../../components/header.php';
?>

<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h3 mb-0 text-gray-800">
                    <i class="fas fa-trash text-danger"></i> Delete Category
                </h1>
                <p class="text-muted">Confirm deletion of category</p>
            </div>
            <a href="index.php" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to List
            </a>
        </div>
    </div>
</div>

<?php include '../../components/alerts.php'; ?>

<div class="row">
    <div class="col-lg-8">
        <div class="card border-danger">
            <div class="card-header bg-danger text-white">
                <h6 class="m-0 font-weight-bold">
                    <i class="fas fa-exclamation-triangle"></i> Confirm Deletion
                </h6>
            </div>
            <div class="card-body">
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle"></i>
                    <strong>Warning!</strong> This action cannot be undone. Are you sure you want to delete this category?
                </div>

                <div class="card mb-4">
                    <div class="card-header">
                        <h6 class="m-0 font-weight-bold text-primary">Category Details</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>ID:</strong> <?php echo htmlspecialchars($category['id']); ?></p>
                                <p><strong>Name:</strong> <?php echo htmlspecialchars($category['name']); ?></p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Description:</strong> <?php echo htmlspecialchars($category['description']); ?></p>
                                <p><strong>Created:</strong> <?php echo formatDate($category['created_at']); ?></p>
                            </div>
                        </div>
                    </div>
                </div>

                <form method="POST">
                    <input type="hidden" name="<?php echo CSRF_TOKEN_NAME; ?>" value="<?php echo $_SESSION[CSRF_TOKEN_NAME]; ?>">

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-trash"></i> Yes, Delete Category
                        </button>
                        <a href="index.php" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-info-circle"></i> Important Notes
                </h6>
            </div>
            <div class="card-body">
                <ul class="small text-muted">
                    <li>Deleting this category will permanently remove it</li>
                    <li>If the category is used in transactions, deletion will be blocked</li>
                    <li>Consider archiving instead of deleting if data integrity is important</li>
                    <li>This action cannot be reversed</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<?php include '../../components/footer.php'; ?>