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

// Fetch goal with related data
$stmt = $pdo->prepare("SELECT g.*, p.Pocket_Name
                       FROM Goal g
                       LEFT JOIN Pocket p ON g.Pocket_ID = p.Pocket_ID
                       WHERE g.Goal_ID = ?");
$stmt->execute([$id]);
$goal = $stmt->fetch();

if (!$goal) {
    $_SESSION['error'] = 'Goal not found!';
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    validateCSRF();

    try {
        // Delete goal record
        $stmt = $pdo->prepare("DELETE FROM Goal WHERE Goal_ID = ?");
        $stmt->execute([$id]);

        $_SESSION['success'] = 'Goal deleted successfully!';
        header('Location: index.php');
        exit;
    } catch (PDOException $e) {
        $_SESSION['error'] = 'Failed to delete goal: ' . $e->getMessage();
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
                    <i class="fas fa-trash text-danger"></i> Delete Goal
                </h1>
                <p class="text-muted">Confirm deletion of goal</p>
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
                    <strong>Warning!</strong> This action cannot be undone. Are you sure you want to delete this goal?
                </div>

                <div class="card mb-4">
                    <div class="card-header">
                        <h6 class="m-0 font-weight-bold text-primary">Goal Details</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>ID:</strong> <?php echo htmlspecialchars($goal['Goal_ID']); ?></p>
                                <p><strong>Goal Name:</strong> <?php echo htmlspecialchars($goal['Goal_Name']); ?></p>
                                <p><strong>Pocket:</strong> <?php echo htmlspecialchars($goal['Pocket_Name'] ?? 'N/A'); ?></p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Target Amount:</strong> <?php echo formatCurrency($goal['Target_Amount']); ?></p>
                                <p><strong>Current Amount:</strong> <?php echo formatCurrency($goal['Current_Amount']); ?></p>
                                <p><strong>Target Date:</strong> <?php echo formatDate($goal['Target_Date']); ?></p>
                                <p><strong>Status:</strong>
                                    <span class="badge bg-<?php
                                        echo $goal['Status'] === 'Completed' ? 'success' :
                                             ($goal['Status'] === 'Active' ? 'primary' : 'warning');
                                    ?>">
                                        <?php echo htmlspecialchars($goal['Status']); ?>
                                    </span>
                                </p>
                            </div>
                        </div>
                        <?php if ($goal['Description']): ?>
                        <div class="mt-3">
                            <p><strong>Description:</strong></p>
                            <p class="text-muted"><?php echo htmlspecialchars($goal['Description']); ?></p>
                        </div>
                        <?php endif; ?>

                        <!-- Progress Bar -->
                        <div class="mt-3">
                            <label class="form-label fw-bold">Progress</label>
                            <div class="progress">
                                <?php
                                $progress = $goal['Target_Amount'] > 0 ? ($goal['Current_Amount'] / $goal['Target_Amount']) * 100 : 0;
                                ?>
                                <div class="progress-bar bg-<?php echo $progress >= 100 ? 'success' : 'primary'; ?>"
                                     role="progressbar"
                                     style="width: <?php echo min($progress, 100); ?>%"
                                     aria-valuenow="<?php echo $progress; ?>"
                                     aria-valuemin="0"
                                     aria-valuemax="100">
                                    <?php echo number_format($progress, 1); ?>%
                                </div>
                            </div>
                            <small class="text-muted">
                                <?php echo formatCurrency($goal['Current_Amount']); ?> of <?php echo formatCurrency($goal['Target_Amount']); ?> saved
                            </small>
                        </div>
                    </div>
                </div>

                <form method="POST">
                    <input type="hidden" name="<?php echo CSRF_TOKEN_NAME; ?>" value="<?php echo $_SESSION[CSRF_TOKEN_NAME]; ?>">

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-trash"></i> Yes, Delete Goal
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
                    <li>Deleting this goal will permanently remove it</li>
                    <li>All progress tracking will be lost</li>
                    <li>Consider archiving instead of deleting for completed goals</li>
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
                <p><strong>Financial Impact:</strong></p>
                <ul class="small">
                    <li>Target Amount: <?php echo formatCurrency($goal['Target_Amount']); ?></li>
                    <li>Current Savings: <?php echo formatCurrency($goal['Current_Amount']); ?></li>
                    <li>Remaining: <?php echo formatCurrency($goal['Target_Amount'] - $goal['Current_Amount']); ?></li>
                </ul>
                <p class="text-muted small">Deleting this goal will affect savings tracking and progress reports.</p>
            </div>
        </div>
    </div>
</div>

<?php include '../../components/footer.php'; ?>