<?php
require_once '../../config/app.php';
require_once '../../config/database.php';
require_once '../../helpers/functions.php';
require_once '../../helpers/validation.php';
require_once '../../helpers/security.php';

$pdo = require '../../config/database.php';

$errors = [];
$formData = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    validateCSRF();

    $formData = [
        'name' => sanitize($_POST['name'] ?? ''),
        'type' => sanitize($_POST['type'] ?? ''),
        'icon' => sanitize($_POST['icon'] ?? ''),
    ];

    $validation = sanitizeAndValidate($formData, [
        'name' => 'required',
        'type' => 'required',
        'icon' => 'required',
    ]);

    if ($validation['errors']) {
        $errors = $validation['errors'];
    } else {
        try {
            $stmt = $pdo->prepare("INSERT INTO Category (Category_Name, Category_Type, Icon_Code) VALUES (?, ?, ?)");
            $stmt->execute([$formData['name'], $formData['type'], $formData['icon']]);

            $_SESSION['success'] = 'Category created successfully!';
            header('Location: index.php');
            exit;
        } catch (PDOException $e) {
            $errors[] = 'Failed to create category: ' . $e->getMessage();
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
                    <i class="fas fa-plus text-primary"></i> Add New Category
                </h1>
                <p class="text-muted">Create a new transaction category</p>
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
                    <i class="fas fa-edit"></i> Category Details
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
                        <label for="name" class="form-label">Category Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control <?php echo in_array('Name is required', $errors) ? 'is-invalid' : ''; ?>"
                               id="name" name="name" required
                               value="<?php echo htmlspecialchars($formData['name'] ?? ''); ?>"
                               placeholder="Enter category name">
                        <div class="invalid-feedback">
                            Please provide a valid category name.
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="type" class="form-label">Category Type <span class="text-danger">*</span></label>
                        <select class="form-control <?php echo in_array('Type is required', $errors) ? 'is-invalid' : ''; ?>"
                                id="type" name="type" required>
                            <option value="">Select type</option>
                            <option value="income" <?php echo ($formData['type'] ?? '') === 'income' ? 'selected' : ''; ?>>Income</option>
                            <option value="expense" <?php echo ($formData['type'] ?? '') === 'expense' ? 'selected' : ''; ?>>Expense</option>
                        </select>
                        <div class="invalid-feedback">
                            Please select a category type.
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="icon" class="form-label">Icon Code <span class="text-danger">*</span></label>
                        <input type="text" class="form-control <?php echo in_array('Icon is required', $errors) ? 'is-invalid' : ''; ?>"
                               id="icon" name="icon" required
                               value="<?php echo htmlspecialchars($formData['icon'] ?? ''); ?>"
                               placeholder="e.g., fas fa-utensils">
                        <div class="invalid-feedback">
                            Please provide an icon code.
                        </div>
                        <div class="form-text">Use Font Awesome icon classes (e.g., fas fa-shopping-cart)</div>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Create Category
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
                <h6>Category Guidelines:</h6>
                <ul class="small">
                    <li>Use clear, descriptive names</li>
                    <li>Provide detailed descriptions</li>
                    <li>Categories help organize transactions</li>
                    <li>Examples: Food, Transport, Entertainment</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<?php include '../../components/footer.php'; ?>