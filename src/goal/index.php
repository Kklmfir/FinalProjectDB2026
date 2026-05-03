<?php
require_once '../../config/app.php';
require_once '../../config/database.php';
require_once '../../helpers/functions.php';
require_once '../../helpers/validation.php';
require_once '../../helpers/security.php';

$pdo = require '../../config/database.php';

// Handle search and filters
$search = sanitize($_GET['search'] ?? '');
$status = sanitize($_GET['status'] ?? '');
$pocket_id = isset($_GET['pocket_id']) ? (int)$_GET['pocket_id'] : null;

// Build query
$query = "SELECT g.*, p.Pocket_Name,
          CASE
              WHEN g.Target_Amount > 0 THEN (g.Current_Amount / g.Target_Amount) * 100
              ELSE 0
          END as Progress_Percentage
          FROM Goal g
          LEFT JOIN Pocket p ON g.Pocket_ID = p.Pocket_ID
          WHERE 1=1";

$params = [];

if ($search) {
    $query .= " AND (g.Goal_Name LIKE ? OR g.Description LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if ($status) {
    $query .= " AND g.Status = ?";
    $params[] = $status;
}

if ($pocket_id) {
    $query .= " AND g.Pocket_ID = ?";
    $params[] = $pocket_id;
}

$query .= " ORDER BY g.Target_Date ASC";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$goals = $stmt->fetchAll();

// Get summary statistics
$totalStmt = $pdo->prepare("SELECT COUNT(*) FROM Goal");
$totalStmt->execute();
$totalCount = $totalStmt->fetchColumn();

$activeStmt = $pdo->prepare("SELECT COUNT(*) FROM Goal WHERE Status = 'Active'");
$activeStmt->execute();
$activeCount = $activeStmt->fetchColumn();

$completedStmt = $pdo->prepare("SELECT COUNT(*) FROM Goal WHERE Status = 'Completed'");
$completedStmt->execute();
$completedCount = $completedStmt->fetchColumn();

$totalTargetStmt = $pdo->prepare("SELECT SUM(Target_Amount) FROM Goal");
$totalTargetStmt->execute();
$totalTarget = $totalTargetStmt->fetchColumn() ?: 0;

$totalCurrentStmt = $pdo->prepare("SELECT SUM(Current_Amount) FROM Goal");
$totalCurrentStmt->execute();
$totalCurrent = $totalCurrentStmt->fetchColumn() ?: 0;

include '../../components/header.php';
?>

<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h3 mb-0 text-gray-800">
                    <i class="fas fa-bullseye text-primary"></i> Financial Goals
                </h1>
                <p class="text-muted">Track your savings goals and progress</p>
            </div>
            <a href="create.php" class="btn btn-primary">
                <i class="fas fa-plus"></i> Add New Goal
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
                            Total Goals</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $totalCount; ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-bullseye fa-2x text-gray-300"></i>
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
                            Active</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $activeCount; ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-play-circle fa-2x text-gray-300"></i>
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
                            Completed</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $completedCount; ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                            Total Saved</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo formatCurrency($totalCurrent); ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-piggy-bank fa-2x text-gray-300"></i>
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
            <div class="col-md-4">
                <input type="text" class="form-control" name="search" placeholder="Search by goal name or description..."
                       value="<?php echo htmlspecialchars($search); ?>">
            </div>
            <div class="col-md-3">
                <select class="form-control" name="status">
                    <option value="">All Status</option>
                    <option value="Active" <?php echo $status === 'Active' ? 'selected' : ''; ?>>Active</option>
                    <option value="Completed" <?php echo $status === 'Completed' ? 'selected' : ''; ?>>Completed</option>
                    <option value="Paused" <?php echo $status === 'Paused' ? 'selected' : ''; ?>>Paused</option>
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
            <i class="fas fa-table"></i> Goals List
        </h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered datatable" id="goalTable">
                <thead>
                    <tr>
                        <th>Goal Name</th>
                        <th>Pocket</th>
                        <th>Target Amount</th>
                        <th>Current Amount</th>
                        <th>Progress</th>
                        <th>Target Date</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($goals as $goal): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($goal['Goal_Name']); ?></td>
                        <td><?php echo htmlspecialchars($goal['Pocket_Name'] ?? 'N/A'); ?></td>
                        <td><?php echo formatCurrency($goal['Target_Amount']); ?></td>
                        <td><?php echo formatCurrency($goal['Current_Amount']); ?></td>
                        <td>
                            <div class="progress" style="width: 100px;">
                                <div class="progress-bar bg-<?php echo $goal['Progress_Percentage'] >= 100 ? 'success' : 'primary'; ?>"
                                     role="progressbar"
                                     style="width: <?php echo min($goal['Progress_Percentage'], 100); ?>%"
                                     aria-valuenow="<?php echo $goal['Progress_Percentage']; ?>"
                                     aria-valuemin="0"
                                     aria-valuemax="100">
                                    <?php echo number_format($goal['Progress_Percentage'], 1); ?>%
                                </div>
                            </div>
                        </td>
                        <td><?php echo formatDate($goal['Target_Date']); ?></td>
                        <td>
                            <span class="badge bg-<?php
                                echo $goal['Status'] === 'Completed' ? 'success' :
                                     ($goal['Status'] === 'Active' ? 'primary' : 'warning');
                            ?>">
                                <?php echo htmlspecialchars($goal['Status']); ?>
                            </span>
                        </td>
                        <td>
                            <div class="btn-group" role="group">
                                <a href="view.php?id=<?php echo $goal['Goal_ID']; ?>" class="btn btn-sm btn-info" title="View">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="edit.php?id=<?php echo $goal['Goal_ID']; ?>" class="btn btn-sm btn-warning" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="delete.php?id=<?php echo $goal['Goal_ID']; ?>" class="btn btn-sm btn-danger delete-btn" title="Delete">
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