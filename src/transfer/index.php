<?php
require_once '../../config/app.php';
require_once '../../config/database.php';
require_once '../../helpers/functions.php';
require_once '../../helpers/validation.php';
require_once '../../helpers/security.php';

$pdo = require '../../config/database.php';

// Handle search and filters
$search = sanitize($_GET['search'] ?? '');
$from_pocket_id = isset($_GET['from_pocket_id']) ? (int)$_GET['from_pocket_id'] : null;
$to_pocket_id = isset($_GET['to_pocket_id']) ? (int)$_GET['to_pocket_id'] : null;
$date_from = sanitize($_GET['date_from'] ?? '');
$date_to = sanitize($_GET['date_to'] ?? '');

// Build query
$query = "SELECT t.*, fp.Pocket_Name as From_Pocket_Name, tp.Pocket_Name as To_Pocket_Name
          FROM Transfer t
          LEFT JOIN Pocket fp ON t.From_Pocket_ID = fp.Pocket_ID
          LEFT JOIN Pocket tp ON t.To_Pocket_ID = tp.Pocket_ID
          WHERE 1=1";

$params = [];

if ($search) {
    $query .= " AND (t.Description LIKE ? OR fp.Pocket_Name LIKE ? OR tp.Pocket_Name LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if ($from_pocket_id) {
    $query .= " AND t.From_Pocket_ID = ?";
    $params[] = $from_pocket_id;
}

if ($to_pocket_id) {
    $query .= " AND t.To_Pocket_ID = ?";
    $params[] = $to_pocket_id;
}

if ($date_from) {
    $query .= " AND t.Transfer_Date >= ?";
    $params[] = $date_from;
}

if ($date_to) {
    $query .= " AND t.Transfer_Date <= ?";
    $params[] = $date_to;
}

$query .= " ORDER BY t.Transfer_Date DESC, t.Transfer_ID DESC";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$transfers = $stmt->fetchAll();

// Get summary statistics
$totalStmt = $pdo->query("SELECT COUNT(*) FROM Transfer");
$totalCount = $totalStmt->fetchColumn();

$totalAmountStmt = $pdo->query("SELECT SUM(Amount) FROM Transfer");
$totalAmount = $totalAmountStmt->fetchColumn() ?: 0;

// Get most active pockets for transfers
$activePocketsStmt = $pdo->query("
    SELECT p.Pocket_Name,
           (SELECT COUNT(*) FROM Transfer WHERE From_Pocket_ID = p.Pocket_ID) +
           (SELECT COUNT(*) FROM Transfer WHERE To_Pocket_ID = p.Pocket_ID) as transfer_count
    FROM Pocket p
    ORDER BY transfer_count DESC LIMIT 5
");
$activePockets = $activePocketsStmt->fetchAll();

// Get filter options
$pocketsStmt = $pdo->query("SELECT Pocket_ID, Pocket_Name FROM Pocket ORDER BY Pocket_Name");
$pockets = $pocketsStmt->fetchAll();

include '../../components/header.php';
?>

<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h3 mb-0 text-gray-800">
                    <i class="fas fa-exchange-alt text-primary"></i> Transfer Management
                </h1>
                <p class="text-muted">Track money transfers between pockets</p>
            </div>
            <a href="create.php" class="btn btn-primary">
                <i class="fas fa-plus"></i> Add Transfer
            </a>
        </div>
    </div>
</div>

<?php include '../../components/alerts.php'; ?>

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-xl-4 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            Total Transfers</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $totalCount; ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-exchange-alt fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-4 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            Total Amount Transferred</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo formatCurrency($totalAmount); ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-money-bill-wave fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-4 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                            Average Transfer</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            <?php echo $totalCount > 0 ? formatCurrency($totalAmount / $totalCount) : formatCurrency(0); ?>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-calculator fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Active Pockets -->
<?php if ($activePockets): ?>
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-star"></i> Most Active Pockets
                </h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <?php foreach ($activePockets as $pocket): ?>
                    <div class="col-md-2 mb-3">
                        <div class="text-center">
                            <div class="h6 mb-1"><?php echo htmlspecialchars($pocket['Pocket_Name']); ?></div>
                            <small class="text-muted"><?php echo $pocket['transfer_count']; ?> transfers</small>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

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
                <input type="text" class="form-control" name="search" placeholder="Search description, pockets..."
                       value="<?php echo htmlspecialchars($search); ?>">
            </div>
            <div class="col-md-2">
                <select class="form-control" name="from_pocket_id">
                    <option value="">From Pocket</option>
                    <?php foreach ($pockets as $pocket): ?>
                    <option value="<?php echo $pocket['Pocket_ID']; ?>"
                            <?php echo $from_pocket_id == $pocket['Pocket_ID'] ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($pocket['Pocket_Name']); ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <select class="form-control" name="to_pocket_id">
                    <option value="">To Pocket</option>
                    <?php foreach ($pockets as $pocket): ?>
                    <option value="<?php echo $pocket['Pocket_ID']; ?>"
                            <?php echo $to_pocket_id == $pocket['Pocket_ID'] ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($pocket['Pocket_Name']); ?>
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
            <i class="fas fa-table"></i> Transfers
        </h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered datatable" id="transferTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Date</th>
                        <th>From Pocket</th>
                        <th>To Pocket</th>
                        <th>Amount</th>
                        <th>Description</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($transfers as $transfer): ?>
                    <tr>
                        <td><?php echo $transfer['Transfer_ID']; ?></td>
                        <td><?php echo formatDate($transfer['Transfer_Date']); ?></td>
                        <td>
                            <i class="fas fa-arrow-right text-danger"></i>
                            <?php echo htmlspecialchars($transfer['From_Pocket_Name'] ?? 'N/A'); ?>
                        </td>
                        <td>
                            <i class="fas fa-arrow-left text-success"></i>
                            <?php echo htmlspecialchars($transfer['To_Pocket_Name'] ?? 'N/A'); ?>
                        </td>
                        <td><?php echo formatCurrency($transfer['Amount']); ?></td>
                        <td><?php echo htmlspecialchars($transfer['Description'] ?? ''); ?></td>
                        <td>
                            <div class="btn-group" role="group">
                                <a href="view.php?id=<?php echo $transfer['Transfer_ID']; ?>" class="btn btn-sm btn-info" title="View">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="edit.php?id=<?php echo $transfer['Transfer_ID']; ?>" class="btn btn-sm btn-warning" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="delete.php?id=<?php echo $transfer['Transfer_ID']; ?>" class="btn btn-sm btn-danger delete-btn" title="Delete">
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