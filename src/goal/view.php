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

// Calculate progress and time remaining
$progress = $goal['Target_Amount'] > 0 ? ($goal['Current_Amount'] / $goal['Target_Amount']) * 100 : 0;
$remaining = $goal['Target_Amount'] - $goal['Current_Amount'];

$targetDate = new DateTime($goal['Target_Date']);
$today = new DateTime();
$daysRemaining = $today->diff($targetDate)->days;
$isOverdue = $today > $targetDate && $goal['Status'] === 'Active';

include '../../components/header.php';
?>

<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h3 mb-0 text-gray-800">
                    <i class="fas fa-eye text-primary"></i> View Goal
                </h1>
                <p class="text-muted">Detailed goal information and progress</p>
            </div>
            <div>
                <a href="edit.php?id=<?php echo $goal['Goal_ID']; ?>" class="btn btn-warning">
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
                    <i class="fas fa-info-circle"></i> Goal Information
                </h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Goal ID</label>
                            <p class="form-control-plaintext"><?php echo htmlspecialchars($goal['Goal_ID']); ?></p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Status</label>
                            <p class="form-control-plaintext">
                                <span class="badge bg-<?php
                                    echo $goal['Status'] === 'Completed' ? 'success' :
                                         ($goal['Status'] === 'Active' ? 'primary' : 'warning');
                                ?> fs-6">
                                    <?php echo htmlspecialchars($goal['Status']); ?>
                                </span>
                            </p>
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Goal Name</label>
                    <p class="form-control-plaintext"><?php echo htmlspecialchars($goal['Goal_Name']); ?></p>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Pocket</label>
                    <p class="form-control-plaintext">
                        <i class="fas fa-wallet text-success"></i> <?php echo htmlspecialchars($goal['Pocket_Name'] ?? 'N/A'); ?>
                    </p>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Target Amount</label>
                            <p class="form-control-plaintext fs-5 text-primary fw-bold">
                                IDR <?php echo number_format($goal['Target_Amount'], 0, ',', '.'); ?>
                            </p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Current Amount</label>
                            <p class="form-control-plaintext fs-5 text-success fw-bold">
                                IDR <?php echo number_format($goal['Current_Amount'], 0, ',', '.'); ?>
                            </p>
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Target Date</label>
                    <p class="form-control-plaintext">
                        <i class="fas fa-calendar text-warning"></i> <?php echo formatDate($goal['Target_Date']); ?>
                        <?php if ($goal['Status'] === 'Active'): ?>
                        <br><small class="<?php echo $isOverdue ? 'text-danger' : 'text-muted'; ?>">
                            <i class="fas fa-<?php echo $isOverdue ? 'exclamation-triangle' : 'clock'; ?>"></i>
                            <?php if ($isOverdue): ?>
                            Overdue by <?php echo $daysRemaining; ?> days
                            <?php else: ?>
                            <?php echo $daysRemaining; ?> days remaining
                            <?php endif; ?>
                        </small>
                        <?php endif; ?>
                    </p>
                </div>

                <?php if ($goal['Description']): ?>
                <div class="mb-3">
                    <label class="form-label fw-bold">Description</label>
                    <p class="form-control-plaintext"><?php echo nl2br(htmlspecialchars($goal['Description'])); ?></p>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Progress Section -->
        <div class="card mt-4">
            <div class="card-header">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-chart-line"></i> Progress Overview
                </h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Progress Percentage</label>
                            <div class="progress" style="height: 25px;">
                                <div class="progress-bar bg-<?php echo $progress >= 100 ? 'success' : 'primary'; ?>"
                                     role="progressbar"
                                     style="width: <?php echo min($progress, 100); ?>%"
                                     aria-valuenow="<?php echo $progress; ?>"
                                     aria-valuemin="0"
                                     aria-valuemax="100">
                                    <span class="fw-bold"><?php echo number_format($progress, 1); ?>%</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Amount Remaining</label>
                            <p class="form-control-plaintext fs-5 <?php echo $remaining > 0 ? 'text-warning' : 'text-success'; ?> fw-bold">
                                IDR <?php echo number_format($remaining, 0, ',', '.'); ?>
                            </p>
                        </div>
                    </div>
                </div>

                <div class="row text-center">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <h4 class="text-primary"><?php echo number_format($progress, 1); ?>%</h4>
                            <p class="text-muted mb-0">Complete</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <h4 class="text-success"><?php echo formatCurrency($goal['Current_Amount']); ?></h4>
                            <p class="text-muted mb-0">Saved</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <h4 class="text-warning"><?php echo formatCurrency($remaining); ?></h4>
                            <p class="text-muted mb-0">Remaining</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
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
                    <a href="edit.php?id=<?php echo $goal['Goal_ID']; ?>" class="btn btn-warning">
                        <i class="fas fa-edit"></i> Edit Goal
                    </a>
                    <a href="delete.php?id=<?php echo $goal['Goal_ID']; ?>" class="btn btn-danger delete-btn">
                        <i class="fas fa-trash"></i> Delete Goal
                    </a>
                    <a href="../pocket/view.php?id=<?php echo $goal['Pocket_ID']; ?>" class="btn btn-outline-success">
                        <i class="fas fa-wallet"></i> View Pocket
                    </a>
                    <?php if ($goal['Status'] === 'Active' && $progress < 100): ?>
                    <button class="btn btn-success" onclick="updateProgress()">
                        <i class="fas fa-plus"></i> Add Contribution
                    </button>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="card mt-4">
            <div class="card-header">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-chart-pie"></i> Goal Summary
                </h6>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-12">
                        <h4 class="text-primary mb-1"><?php echo number_format($progress, 1); ?>%</h4>
                        <p class="text-muted mb-0">Progress Made</p>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-6 text-center">
                        <small class="text-muted">Status</small>
                        <br>
                        <span class="badge bg-<?php
                            echo $goal['Status'] === 'Completed' ? 'success' :
                                 ($goal['Status'] === 'Active' ? 'primary' : 'warning');
                        ?>">
                            <?php echo htmlspecialchars($goal['Status']); ?>
                        </span>
                    </div>
                    <div class="col-6 text-center">
                        <small class="text-muted">Days Left</small>
                        <br>
                        <span class="<?php echo $isOverdue ? 'text-danger' : 'text-muted'; ?>">
                            <?php if ($isOverdue): ?>
                            <?php echo $daysRemaining; ?> ago
                            <?php else: ?>
                            <?php echo $daysRemaining; ?>
                            <?php endif; ?>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <?php if ($isOverdue && $goal['Status'] === 'Active'): ?>
        <div class="card mt-4 border-danger">
            <div class="card-header bg-danger text-white">
                <h6 class="m-0 font-weight-bold">
                    <i class="fas fa-exclamation-triangle"></i> Overdue Notice
                </h6>
            </div>
            <div class="card-body">
                <p class="text-danger mb-2">This goal is overdue by <?php echo $daysRemaining; ?> days.</p>
                <p class="small text-muted">Consider extending the target date or updating the status.</p>
            </div>
        </div>
        <?php endif; ?>

        <?php if ($progress >= 100 && $goal['Status'] !== 'Completed'): ?>
        <div class="card mt-4 border-success">
            <div class="card-header bg-success text-white">
                <h6 class="m-0 font-weight-bold">
                    <i class="fas fa-trophy"></i> Goal Achieved!
                </h6>
            </div>
            <div class="card-body">
                <p class="text-success mb-2">Congratulations! You've reached your target amount.</p>
                <p class="small text-muted">Update the status to Completed to mark this goal as finished.</p>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<script>
function updateProgress() {
    const amount = prompt('Enter contribution amount:');
    if (amount && !isNaN(amount) && amount > 0) {
        // This would typically be an AJAX call to update the current amount
        alert('Feature not implemented yet. Please use the Edit button to update progress.');
    }
}
</script>

<?php include '../../components/footer.php'; ?>