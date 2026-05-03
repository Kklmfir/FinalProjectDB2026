<?php
require_once '../../config/app.php';
require_once '../../config/database.php';
require_once '../../helpers/functions.php';
require_once '../../helpers/validation.php';
require_once '../../helpers/security.php';

$pdo = require '../../config/database.php';

// Get pockets for dropdown
$pocketsStmt = $pdo->query("SELECT Pocket_ID, Pocket_Name FROM Pocket ORDER BY Pocket_Name");
$pockets = $pocketsStmt->fetchAll();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$id) {
    header('Location: index.php');
    exit;
}

// Fetch goal
$stmt = $pdo->prepare("SELECT * FROM Goal WHERE Goal_ID = ?");
$stmt->execute([$id]);
$goal = $stmt->fetch();

if (!$goal) {
    $_SESSION['error'] = 'Goal not found!';
    header('Location: index.php');
    exit;
}

$errors = [];
$formData = $goal;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    validateCSRF();

    $formData = [
        'pocket_id' => sanitize($_POST['pocket_id'] ?? ''),
        'goal_name' => sanitize($_POST['goal_name'] ?? ''),
        'target_amount' => sanitize($_POST['target_amount'] ?? ''),
        'current_amount' => sanitize($_POST['current_amount'] ?? ''),
        'target_date' => sanitize($_POST['target_date'] ?? ''),
        'status' => sanitize($_POST['status'] ?? ''),
        'description' => sanitize($_POST['description'] ?? ''),
    ];

    $validation = sanitizeAndValidate($formData, [
        'pocket_id' => 'required',
        'goal_name' => 'required',
        'target_amount' => 'required|numeric',
        'current_amount' => 'numeric',
        'target_date' => 'required',
        'status' => 'required',
    ]);

    if ($validation['errors']) {
        $errors = $validation['errors'];
    } else {
        try {
            $stmt = $pdo->prepare("UPDATE Goal SET Pocket_ID = ?, Goal_Name = ?, Target_Amount = ?, Current_Amount = ?, Target_Date = ?, Status = ?, Description = ? WHERE Goal_ID = ?");
            $stmt->execute([
                $formData['pocket_id'],
                $formData['goal_name'],
                $formData['target_amount'],
                $formData['current_amount'] ?: 0,
                $formData['target_date'],
                $formData['status'],
                $formData['description'],
                $id
            ]);

            $_SESSION['success'] = 'Goal updated successfully!';
            header('Location: index.php');
            exit;
        } catch (PDOException $e) {
            $errors[] = 'Failed to update goal: ' . $e->getMessage();
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
                    <i class="fas fa-edit text-primary"></i> Edit Goal
                </h1>
                <p class="text-muted">Update goal information</p>
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
                    <i class="fas fa-edit"></i> Goal Details
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
                                <label for="pocket_id" class="form-label">Pocket <span class="text-danger">*</span></label>
                                <select class="form-control <?php echo in_array('Pocket is required', $errors) ? 'is-invalid' : ''; ?>"
                                        id="pocket_id" name="pocket_id" required>
                                    <option value="">Select pocket</option>
                                    <?php foreach ($pockets as $pocket): ?>
                                    <option value="<?php echo $pocket['Pocket_ID']; ?>"
                                            <?php echo $formData['Pocket_ID'] == $pocket['Pocket_ID'] ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($pocket['Pocket_Name']); ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="invalid-feedback">
                                    Please select a pocket.
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="goal_name" class="form-label">Goal Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control <?php echo in_array('Goal name is required', $errors) ? 'is-invalid' : ''; ?>"
                                       id="goal_name" name="goal_name" required
                                       value="<?php echo htmlspecialchars($formData['Goal_Name']); ?>"
                                       placeholder="Enter goal name">
                                <div class="invalid-feedback">
                                    Please provide a goal name.
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="target_amount" class="form-label">Target Amount <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">IDR</span>
                                    <input type="number" class="form-control <?php echo in_array('Target amount is required', $errors) || in_array('Target amount must be numeric', $errors) ? 'is-invalid' : ''; ?>"
                                           id="target_amount" name="target_amount" required min="0" step="0.01"
                                           value="<?php echo htmlspecialchars($formData['Target_Amount']); ?>"
                                           placeholder="0.00">
                                    <div class="invalid-feedback">
                                        Please provide a valid target amount.
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="current_amount" class="form-label">Current Amount</label>
                                <div class="input-group">
                                    <span class="input-group-text">IDR</span>
                                    <input type="number" class="form-control <?php echo in_array('Current amount must be numeric', $errors) ? 'is-invalid' : ''; ?>"
                                           id="current_amount" name="current_amount" min="0" step="0.01"
                                           value="<?php echo htmlspecialchars($formData['Current_Amount']); ?>"
                                           placeholder="0.00">
                                    <div class="invalid-feedback">
                                        Please provide a valid current amount.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="target_date" class="form-label">Target Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control <?php echo in_array('Target date is required', $errors) ? 'is-invalid' : ''; ?>"
                                       id="target_date" name="target_date" required
                                       value="<?php echo htmlspecialchars($formData['Target_Date']); ?>">
                                <div class="invalid-feedback">
                                    Please provide a target date.
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                                <select class="form-control <?php echo in_array('Status is required', $errors) ? 'is-invalid' : ''; ?>"
                                        id="status" name="status" required>
                                    <option value="">Select status</option>
                                    <option value="Active" <?php echo $formData['Status'] === 'Active' ? 'selected' : ''; ?>>Active</option>
                                    <option value="Paused" <?php echo $formData['Status'] === 'Paused' ? 'selected' : ''; ?>>Paused</option>
                                    <option value="Completed" <?php echo $formData['Status'] === 'Completed' ? 'selected' : ''; ?>>Completed</option>
                                </select>
                                <div class="invalid-feedback">
                                    Please select a status.
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3"
                                  placeholder="Optional description or notes"><?php echo htmlspecialchars($formData['Description'] ?? ''); ?></textarea>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Update Goal
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
                    <i class="fas fa-info-circle"></i> Goal Info
                </h6>
            </div>
            <div class="card-body">
                <p><strong>ID:</strong> <?php echo $goal['Goal_ID']; ?></p>
                <p><strong>Progress:</strong>
                    <?php
                    $progress = $goal['Target_Amount'] > 0 ? ($goal['Current_Amount'] / $goal['Target_Amount']) * 100 : 0;
                    echo number_format($progress, 1) . '%';
                    ?>
                </p>
                <p><strong>Remaining:</strong> <?php echo formatCurrency($goal['Target_Amount'] - $goal['Current_Amount']); ?></p>
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
                    <li>Update current amount as you save</li>
                    <li>Change status when goal is completed</li>
                    <li>Adjust target date if needed</li>
                    <li>Add notes about progress or changes</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<?php include '../../components/footer.php'; ?>