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

// Check if sub category is being used in transactions
$usageStmt = $pdo->prepare("SELECT COUNT(*) FROM Transactions WHERE Sub_Category_ID = ?");
$usageStmt->execute([$id]);
$usageCount = $usageStmt->fetchColumn();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    validateCSRF();

    try {
        if ($usageCount > 0) {
            $_SESSION['error'] = 'Cannot delete sub category. It is being used in ' . $usageCount . ' transaction(s).';
            header('Location: index.php');
            exit;
        }

        // Delete sub category record
        $stmt = $pdo->prepare("DELETE FROM Sub_Category WHERE Sub_Category_ID = ?");
        $stmt->execute([$id]);

        $_SESSION['success'] = 'Sub category deleted successfully!';
        header('Location: index.php');
        exit;
    } catch (PDOException $e) {
        $_SESSION['error'] = 'Failed to delete sub category: ' . $e->getMessage();
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
                    <i class="fas fa-trash text-danger"></i> Delete Sub Category
                </h1>
                <p class="text-muted">Confirm deletion of sub category</p>
            </div>
            <a href="index.php" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to List
            </a>
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
                    <strong>Warning!</strong> This action cannot be undone. Are you sure you want to delete this sub category?
                </div>

                <div class="card mb-4">
                    <div class="card-header">
                        <h6 class="m-0 font-weight-bold text-primary">Sub Category Details</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>ID:</strong> <?php echo htmlspecialchars($subCategory['Sub_Category_ID']); ?></p>
                                <p><strong>Sub Category Name:</strong> <?php echo htmlspecialchars($subCategory['Sub_Category_Name']); ?></p>
                                <p><strong>Parent Category:</strong> <?php echo htmlspecialchars($subCategory['Category_Name'] ?? 'N/A'); ?></p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Category Type:</strong>
                                    <span class="badge bg-<?php echo $subCategory['Category_Type'] === 'Income' ? 'success' : 'danger'; ?>">
                                        <?php echo htmlspecialchars($subCategory['Category_Type'] ?? 'N/A'); ?>
                                    </span>
                                </p>
                                <p><strong>Related Transactions:</strong> <?php echo $usageCount; ?></p>
                            </div>
                        </div>
                        <?php if ($subCategory['Description']): ?>
                        <div class="mt-3">
                            <p><strong>Description:</strong></p>
                            <p class="text-muted"><?php echo htmlspecialchars($subCategory['Description']); ?></p>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>

                <?php if ($usageCount > 0): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle"></i>
                    <strong>Cannot Delete:</strong> This sub category is currently being used in <?php echo $usageCount; ?> transaction(s).
                    You must reassign or delete these transactions before deleting this sub category.
                </div>
                <?php endif; ?>

                <form method="POST">
                    <input type="hidden" name="<?php echo CSRF_TOKEN_NAME; ?>" value="<?php echo $_SESSION[CSRF_TOKEN_NAME]; ?>">

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-danger" <?php echo $usageCount > 0 ? 'disabled' : ''; ?>>
                            <i class="fas fa-trash"></i> Yes, Delete Sub Category
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
                    <li>Deleting this sub category will permanently remove it</li>
                    <li>If used in transactions, deletion will be blocked</li>
                    <li>Consider archiving instead of deleting for data integrity</li>
                    <li>This action cannot be reversed</li>
                </ul>
            </div>
        </div>

        <div class="card mt-4">
            <div class="card-header">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-chart-line"></i> Impact Analysis
                </h6>
            </div>
            <div class="card-body">
                <p><strong>Usage Impact:</strong></p>
                <ul class="small">
                    <li>Transactions affected: <?php echo $usageCount; ?></li>
                    <li>Parent category: <?php echo htmlspecialchars($subCategory['Category_Name'] ?? 'N/A'); ?></li>
                    <li>Category type: <?php echo htmlspecialchars($subCategory['Category_Type'] ?? 'N/A'); ?></li>
                </ul>
                <p class="text-muted small">Deleting this sub category will affect transaction categorization and reporting.</p>
            </div>
        </div>
    </div>
</div>

<?php include '../../components/footer.php'; ?>