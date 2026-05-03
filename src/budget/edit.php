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

// Fetch budget
$stmt = $pdo->prepare("SELECT * FROM Budget WHERE Budget_ID = ?");
$stmt->execute([$id]);
$budget = $stmt->fetch();

if (!$budget) {
    $_SESSION['error'] = 'Budget not found!';
    header('Location: index.php');
    exit;
}

// Get categories for dropdown
$categoryStmt = $pdo->query("SELECT Category_ID as id, Category_Name as name FROM Category ORDER BY Category_Name");
$categories = $categoryStmt->fetchAll();

$errors = [];
$formData = $budget;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    validateCSRF();

    $formData = [
        'category_id' => sanitize($_POST['category_id'] ?? ''),
        'monthly_limit' => sanitize($_POST['monthly_limit'] ?? ''),
        'start_date' => sanitize($_POST['start_date'] ?? ''),
        'end_date' => sanitize($_POST['end_date'] ?? ''),
    ];

    $validation = sanitizeAndValidate($formData, [
        'category_id' => 'required',
        'monthly_limit' => 'required',
        'start_date' => 'required',
        'end_date' => 'required',
    ]);

    if ($validation['errors']) {
        $errors = $validation['errors'];
    } else {
        try {
            $stmt = $pdo->prepare("UPDATE Budget SET Category_ID = ?, Monthly_Limit = ?, Start_Date = ?, End_Date = ? WHERE Budget_ID = ?");
            $stmt->execute([$formData['category_id'], $formData['monthly_limit'], $formData['start_date'], $formData['end_date'], $id]);

            $_SESSION['success'] = 'Budget updated successfully!';
            header('Location: index.php');
            exit;
        } catch (PDOException $e) {
            $errors[] = 'Failed to update budget: ' . $e->getMessage();
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
                    <i class="fas fa-edit text-primary"></i> Edit Budget
                </h1>
                <p class="text-muted">Update budget information</p>
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
                    <i class="fas fa-edit"></i> Budget Details
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

                    <div class="mb-3">
                        <label for="category_id" class="form-label">Category <span class="text-danger">*</span></label>
                        <select class="form-control <?php echo in_array('Category is required', $errors) ? 'is-invalid' : ''; ?>"
                                id="category_id" name="category_id" required>
                            <option value="">Select Category</option>
                            <?php foreach ($categories as $category): ?>
                            <option value="<?php echo $category['id']; ?>" <?php echo $formData['Category_ID'] == $category['id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($category['name']); ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="invalid-feedback">
                            Please select a category.
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="monthly_limit" class="form-label">Monthly Limit <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text">IDR</span>
                            <input type="number" class="form-control <?php echo in_array('Monthly limit is required', $errors) ? 'is-invalid' : ''; ?>"
                                   id="monthly_limit" name="monthly_limit" required step="0.01" min="0"
                                   value="<?php echo htmlspecialchars($formData['Monthly_Limit']); ?>">
                            <div class="invalid-feedback">
                                Please provide a valid monthly limit.
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="start_date" class="form-label">Start Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control <?php echo in_array('Start date is required', $errors) ? 'is-invalid' : ''; ?>"
                                       id="start_date" name="start_date" required
                                       value="<?php echo htmlspecialchars($formData['Start_Date']); ?>">
                                <div class="invalid-feedback">
                                    Please provide a start date.
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="end_date" class="form-label">End Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control <?php echo in_array('End date is required', $errors) ? 'is-invalid' : ''; ?>"
                                       id="end_date" name="end_date" required
                                       value="<?php echo htmlspecialchars($formData['End_Date']); ?>">
                                <div class="invalid-feedback">
                                    Please provide an end date.
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Update Budget
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
                    <i class="fas fa-info-circle"></i> Budget Info
                </h6>
            </div>
            <div class="card-body">
                <p><strong>ID:</strong> <?php echo $budget['Budget_ID']; ?></p>
                <p><strong>Current Usage:</strong> IDR 0 (0%)</p>
                <p><strong>Remaining:</strong> IDR <?php echo formatCurrency($budget['Monthly_Limit']); ?></p>
            </div>
        </div>
    </div>
</div>

<?php include '../../components/footer.php'; ?>