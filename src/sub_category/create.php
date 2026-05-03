<?php
require_once '../../config/app.php';
require_once '../../config/database.php';
require_once '../../helpers/functions.php';
require_once '../../helpers/validation.php';
require_once '../../helpers/security.php';

$pdo = require '../../config/database.php';

// Get categories for dropdown
$categoriesStmt = $pdo->query("SELECT Category_ID, Category_Name, Category_Type FROM Category ORDER BY Category_Name");
$categories = $categoriesStmt->fetchAll();

$errors = [];
$formData = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    validateCSRF();

    $formData = [
        'category_id' => sanitize($_POST['category_id'] ?? ''),
        'sub_category_name' => sanitize($_POST['sub_category_name'] ?? ''),
        'description' => sanitize($_POST['description'] ?? ''),
    ];

    $validation = sanitizeAndValidate($formData, [
        'category_id' => 'required',
        'sub_category_name' => 'required',
    ]);

    if ($validation['errors']) {
        $errors = $validation['errors'];
    } else {
        try {
            $stmt = $pdo->prepare("INSERT INTO Sub_Category (Category_ID, Sub_Category_Name, Description) VALUES (?, ?, ?)");
            $stmt->execute([
                $formData['category_id'],
                $formData['sub_category_name'],
                $formData['description']
            ]);

            $_SESSION['success'] = 'Sub category created successfully!';
            header('Location: index.php');
            exit;
        } catch (PDOException $e) {
            $errors[] = 'Failed to create sub category: ' . $e->getMessage();
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
                    <i class="fas fa-plus text-primary"></i> Add New Sub Category
                </h1>
                <p class="text-muted">Create a new sub category for better transaction organization</p>
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
                    <i class="fas fa-edit"></i> Sub Category Details
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
                        <label for="category_id" class="form-label">Parent Category <span class="text-danger">*</span></label>
                        <select class="form-control <?php echo in_array('Parent category is required', $errors) ? 'is-invalid' : ''; ?>"
                                id="category_id" name="category_id" required>
                            <option value="">Select parent category</option>
                            <?php foreach ($categories as $category): ?>
                            <option value="<?php echo $category['Category_ID']; ?>"
                                    data-type="<?php echo htmlspecialchars($category['Category_Type']); ?>"
                                    <?php echo ($formData['category_id'] ?? '') == $category['Category_ID'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($category['Category_Name']); ?> (<?php echo htmlspecialchars($category['Category_Type']); ?>)
                            </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="invalid-feedback">
                            Please select a parent category.
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="sub_category_name" class="form-label">Sub Category Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control <?php echo in_array('Sub category name is required', $errors) ? 'is-invalid' : ''; ?>"
                               id="sub_category_name" name="sub_category_name" required
                               value="<?php echo htmlspecialchars($formData['sub_category_name'] ?? ''); ?>"
                               placeholder="Enter sub category name">
                        <div class="invalid-feedback">
                            Please provide a sub category name.
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3"
                                  placeholder="Optional description or notes"><?php echo htmlspecialchars($formData['description'] ?? ''); ?></textarea>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Create Sub Category
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
                <h6>Sub Category Guidelines:</h6>
                <ul class="small">
                    <li>Choose the appropriate parent category</li>
                    <li>Use descriptive names for better organization</li>
                    <li>Sub categories help in detailed transaction tracking</li>
                    <li>Keep names consistent and clear</li>
                </ul>
            </div>
        </div>

        <div class="card mt-4">
            <div class="card-header">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-lightbulb"></i> Tips
                </h6>
            </div>
            <div class="card-body">
                <ul class="small">
                    <li>Income sub categories: Salary, Business, Investment, etc.</li>
                    <li>Expense sub categories: Food, Transport, Utilities, Entertainment, etc.</li>
                    <li>Use consistent naming conventions</li>
                    <li>Review and update categories periodically</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<?php include '../../components/footer.php'; ?>