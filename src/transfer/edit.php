<?php
require_once '../../config/app.php';
require_once '../../config/database.php';
require_once '../../helpers/functions.php';
require_once '../../helpers/validation.php';
require_once '../../helpers/security.php';

$pdo = require '../../config/database.php';

// Get dropdown options
$pocketsStmt = $pdo->query("SELECT Pocket_ID, Pocket_Name FROM Pocket ORDER BY Pocket_Name");
$pockets = $pocketsStmt->fetchAll();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$id) {
    header('Location: index.php');
    exit;
}

// Fetch transfer
$stmt = $pdo->prepare("SELECT * FROM Transfer WHERE Transfer_ID = ?");
$stmt->execute([$id]);
$transfer = $stmt->fetch();

if (!$transfer) {
    $_SESSION['error'] = 'Transfer not found!';
    header('Location: index.php');
    exit;
}

$errors = [];
$formData = $transfer;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    validateCSRF();

    $formData = [
        'from_pocket_id' => sanitize($_POST['from_pocket_id'] ?? ''),
        'to_pocket_id' => sanitize($_POST['to_pocket_id'] ?? ''),
        'amount' => sanitize($_POST['amount'] ?? ''),
        'transfer_date' => sanitize($_POST['transfer_date'] ?? ''),
        'description' => sanitize($_POST['description'] ?? ''),
    ];

    $validation = sanitizeAndValidate($formData, [
        'from_pocket_id' => 'required',
        'to_pocket_id' => 'required',
        'amount' => 'required|numeric',
        'transfer_date' => 'required',
    ]);

    if ($validation['errors']) {
        $errors = $validation['errors'];
    } elseif ($formData['from_pocket_id'] === $formData['to_pocket_id']) {
        $errors[] = 'From pocket and to pocket cannot be the same';
    } else {
        try {
            $stmt = $pdo->prepare("UPDATE Transfer SET From_Pocket_ID = ?, To_Pocket_ID = ?, Amount = ?, Transfer_Date = ?, Description = ? WHERE Transfer_ID = ?");
            $stmt->execute([
                $formData['from_pocket_id'],
                $formData['to_pocket_id'],
                $formData['amount'],
                $formData['transfer_date'],
                $formData['description'],
                $id
            ]);

            $_SESSION['success'] = 'Transfer updated successfully!';
            header('Location: index.php');
            exit;
        } catch (PDOException $e) {
            $errors[] = 'Failed to update transfer: ' . $e->getMessage();
        }
    }
}

include '../../components/header.php';
?>

<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h3 mb-0 text-gray-800">
                    <i class="fas fa-edit text-primary"></i> Edit Transfer
                </h1>
                <p class="text-muted">Update transfer information</p>
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
        <div class="card">
            <div class="card-header">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-edit"></i> Transfer Details
                </h6>
            </div>
            <div class="card-body">
                <?php if ($errors): ?>
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        <?php foreach ($errors as $error): ?>
                        <li><?php echo htmlspecialchars($error); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php endif; ?>

                <form method="POST">
                    <input type="hidden" name="<?php echo CSRF_TOKEN_NAME; ?>" value="<?php echo $_SESSION[CSRF_TOKEN_NAME]; ?>">

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="from_pocket_id" class="form-label">From Pocket <span class="text-danger">*</span></label>
                                <select class="form-control <?php echo in_array('From pocket is required', $errors) ? 'is-invalid' : ''; ?>"
                                        id="from_pocket_id" name="from_pocket_id" required>
                                    <option value="">Select source pocket</option>
                                    <?php foreach ($pockets as $pocket): ?>
                                    <option value="<?php echo $pocket['Pocket_ID']; ?>"
                                            <?php echo $formData['From_Pocket_ID'] == $pocket['Pocket_ID'] ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($pocket['Pocket_Name']); ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="invalid-feedback">
                                    Please select source pocket.
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="to_pocket_id" class="form-label">To Pocket <span class="text-danger">*</span></label>
                                <select class="form-control <?php echo in_array('To pocket is required', $errors) ? 'is-invalid' : ''; ?>"
                                        id="to_pocket_id" name="to_pocket_id" required>
                                    <option value="">Select destination pocket</option>
                                    <?php foreach ($pockets as $pocket): ?>
                                    <option value="<?php echo $pocket['Pocket_ID']; ?>"
                                            <?php echo $formData['To_Pocket_ID'] == $pocket['Pocket_ID'] ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($pocket['Pocket_Name']); ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="invalid-feedback">
                                    Please select destination pocket.
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="amount" class="form-label">Amount <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">IDR</span>
                                    <input type="number" class="form-control <?php echo in_array('Amount is required', $errors) || in_array('Amount must be numeric', $errors) ? 'is-invalid' : ''; ?>"
                                           id="amount" name="amount" required min="0.01" step="0.01"
                                           value="<?php echo htmlspecialchars($formData['Amount']); ?>"
                                           placeholder="0.00">
                                    <div class="invalid-feedback">
                                        Please provide a valid amount greater than 0.
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="transfer_date" class="form-label">Transfer Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control <?php echo in_array('Transfer date is required', $errors) ? 'is-invalid' : ''; ?>"
                                       id="transfer_date" name="transfer_date" required
                                       value="<?php echo htmlspecialchars($formData['Transfer_Date']); ?>">
                                <div class="invalid-feedback">
                                    Please provide a transfer date.
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3"
                                  placeholder="Optional description or notes about this transfer"><?php echo htmlspecialchars($formData['Description'] ?? ''); ?></textarea>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Update Transfer
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
                    <i class="fas fa-info-circle"></i> Transfer Info
                </h6>
            </div>
            <div class="card-body">
                <p><strong>ID:</strong> <?php echo $transfer['Transfer_ID']; ?></p>
                <p><strong>Created:</strong> <?php echo formatDate($transfer['Transfer_Date']); ?></p>
                <p><strong>Amount:</strong> <?php echo formatCurrency($transfer['Amount']); ?></p>
            </div>
        </div>

        <div class="card mt-4">
            <div class="card-header">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-lightbulb"></i> Update Tips
                </h6>
            </div>
            <div class="card-body">
                <ul class="small">
                    <li>Changing pockets affects balance calculations</li>
                    <li>Update amounts only if corrections are needed</li>
                    <li>Modify dates to reflect actual transfer timing</li>
                    <li>Add notes in description for tracking changes</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<script>
// Validate that from and to pockets are different
document.querySelector('form').addEventListener('submit', function(e) {
    const fromPocket = document.getElementById('from_pocket_id').value;
    const toPocket = document.getElementById('to_pocket_id').value;

    if (fromPocket === toPocket && fromPocket !== '') {
        e.preventDefault();
        alert('Source and destination pockets cannot be the same.');
        return false;
    }
});
</script>

<?php include '../../components/footer.php'; ?>