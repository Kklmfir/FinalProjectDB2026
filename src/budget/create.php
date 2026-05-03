<?php
require_once '../../config/app.php';
require_once '../../config/database.php';
require_once '../../helpers/functions.php';
require_once '../../helpers/validation.php';
require_once '../../helpers/security.php';

$pdo = require '../../config/database.php';

$errors = [];
$formData = [];

// Get categories for dropdown
$categoryStmt = $pdo->query("SELECT Category_ID as id, Category_Name as name FROM Category ORDER BY Category_Name");
$categories = $categoryStmt->fetchAll();

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
            $stmt = $pdo->prepare("INSERT INTO Budget (Category_ID, Monthly_Limit, Start_Date, End_Date) VALUES (?, ?, ?, ?)");
            $stmt->execute([$formData['category_id'], $formData['monthly_limit'], $formData['start_date'], $formData['end_date']]);

            $_SESSION['success'] = 'Budget created successfully!';
            header('Location: index.php');
            exit;
        } catch (PDOException $e) {
            $errors[] = 'Failed to create budget: ' . $e->getMessage();
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
                    <i class="fas fa-plus text-primary"></i> Add New Budget
                </h1>
                <p class="text-muted">Create a new spending budget</p>
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
                            <option value="<?php echo $category['id']; ?>" <?php echo ($formData['category_id'] ?? '') == $category['id'] ? 'selected' : ''; ?>>
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
                                   value="<?php echo htmlspecialchars($formData['monthly_limit'] ?? ''); ?>"
                                   placeholder="Enter monthly limit">
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
                                       value="<?php echo htmlspecialchars($formData['start_date'] ?? ''); ?>">
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
                                       value="<?php echo htmlspecialchars($formData['end_date'] ?? ''); ?>">
                                <div class="invalid-feedback">
                                    Please provide an end date.
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Create Budget
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
                    <i class="fas fa-info-circle"></i> Help
                </h6>
            </div>
            <div class="card-body">
                <h6>Budget Guidelines:</h6>
                <ul class="small">
                    <li>Set realistic monthly spending limits</li>
                    <li>Choose appropriate categories</li>
                    <li>Monitor budget usage regularly</li>
                    <li>Adjust limits based on actual spending</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<?php include '../../components/footer.php'; ?>