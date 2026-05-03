<?php
require_once '../../config/app.php';
require_once '../../config/database.php';
require_once '../../helpers/functions.php';
require_once '../../helpers/validation.php';
require_once '../../helpers/security.php';

$pdo = require '../../config/database.php';

// Get dropdown options
$categoriesStmt = $pdo->query("SELECT Category_ID, Category_Name, Category_Type FROM Category ORDER BY Category_Name");
$categories = $categoriesStmt->fetchAll();

$subCategoriesStmt = $pdo->query("SELECT sc.Sub_Category_ID, sc.Sub_Category_Name, c.Category_ID, c.Category_Type
                                  FROM Sub_Category sc
                                  LEFT JOIN Category c ON sc.Category_ID = c.Category_ID
                                  ORDER BY c.Category_Name, sc.Sub_Category_Name");
$subCategories = $subCategoriesStmt->fetchAll();

$pocketsStmt = $pdo->query("SELECT Pocket_ID, Pocket_Name FROM Pocket ORDER BY Pocket_Name");
$pockets = $pocketsStmt->fetchAll();

$errors = [];
$formData = [];

// Pre-fill data if provided
$category_id = isset($_GET['category_id']) ? (int)$_GET['category_id'] : null;
$sub_category_id = isset($_GET['sub_category_id']) ? (int)$_GET['sub_category_id'] : null;
$pocket_id = isset($_GET['pocket_id']) ? (int)$_GET['pocket_id'] : null;

if ($category_id) $formData['category_id'] = $category_id;
if ($sub_category_id) $formData['sub_category_id'] = $sub_category_id;
if ($pocket_id) $formData['pocket_id'] = $pocket_id;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    validateCSRF();

    $formData = [
        'pocket_id' => sanitize($_POST['pocket_id'] ?? ''),
        'category_id' => sanitize($_POST['category_id'] ?? ''),
        'sub_category_id' => sanitize($_POST['sub_category_id'] ?? ''),
        'amount' => sanitize($_POST['amount'] ?? ''),
        'transaction_date' => sanitize($_POST['transaction_date'] ?? ''),
        'type' => sanitize($_POST['type'] ?? ''),
        'description' => sanitize($_POST['description'] ?? ''),
    ];

    $validation = sanitizeAndValidate($formData, [
        'pocket_id' => 'required',
        'category_id' => 'required',
        'amount' => 'required|numeric',
        'transaction_date' => 'required',
        'type' => 'required',
    ]);

    if ($validation['errors']) {
        $errors = $validation['errors'];
    } else {
        try {
            $stmt = $pdo->prepare("INSERT INTO Transactions (Pocket_ID, Category_ID, Sub_Category_ID, Amount, Transaction_Date, Type, Description) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $formData['pocket_id'],
                $formData['category_id'],
                $formData['sub_category_id'] ?: null,
                $formData['amount'],
                $formData['transaction_date'],
                $formData['type'],
                $formData['description']
            ]);

            $_SESSION['success'] = 'Transaction created successfully!';
            header('Location: index.php');
            exit;
        } catch (PDOException $e) {
            $errors[] = 'Failed to create transaction: ' . $e->getMessage();
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
                    <i class="fas fa-plus text-primary"></i> Add New Transaction
                </h1>
                <p class="text-muted">Record a new income or expense transaction</p>
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
                    <i class="fas fa-edit"></i> Transaction Details
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
                                <label for="type" class="form-label">Transaction Type <span class="text-danger">*</span></label>
                                <select class="form-control <?php echo in_array('Type is required', $errors) ? 'is-invalid' : ''; ?>"
                                        id="type" name="type" required>
                                    <option value="">Select type</option>
                                    <option value="Income" <?php echo ($formData['type'] ?? '') === 'Income' ? 'selected' : ''; ?>>Income</option>
                                    <option value="Expense" <?php echo ($formData['type'] ?? '') === 'Expense' ? 'selected' : ''; ?>>Expense</option>
                                </select>
                                <div class="invalid-feedback">
                                    Please select transaction type.
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="amount" class="form-label">Amount <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">IDR</span>
                                    <input type="number" class="form-control <?php echo in_array('Amount is required', $errors) || in_array('Amount must be numeric', $errors) ? 'is-invalid' : ''; ?>"
                                           id="amount" name="amount" required min="0" step="0.01"
                                           value="<?php echo htmlspecialchars($formData['amount'] ?? ''); ?>"
                                           placeholder="0.00">
                                    <div class="invalid-feedback">
                                        Please provide a valid amount.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="transaction_date" class="form-label">Transaction Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control <?php echo in_array('Transaction date is required', $errors) ? 'is-invalid' : ''; ?>"
                                       id="transaction_date" name="transaction_date" required
                                       value="<?php echo htmlspecialchars($formData['transaction_date'] ?? date('Y-m-d')); ?>">
                                <div class="invalid-feedback">
                                    Please provide a transaction date.
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="pocket_id" class="form-label">Pocket <span class="text-danger">*</span></label>
                                <select class="form-control <?php echo in_array('Pocket is required', $errors) ? 'is-invalid' : ''; ?>"
                                        id="pocket_id" name="pocket_id" required>
                                    <option value="">Select pocket</option>
                                    <?php foreach ($pockets as $pocket): ?>
                                    <option value="<?php echo $pocket['Pocket_ID']; ?>"
                                            <?php echo ($formData['pocket_id'] ?? '') == $pocket['Pocket_ID'] ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($pocket['Pocket_Name']); ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="invalid-feedback">
                                    Please select a pocket.
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="category_id" class="form-label">Category <span class="text-danger">*</span></label>
                                <select class="form-control <?php echo in_array('Category is required', $errors) ? 'is-invalid' : ''; ?>"
                                        id="category_id" name="category_id" required>
                                    <option value="">Select category</option>
                                    <?php foreach ($categories as $category): ?>
                                    <option value="<?php echo $category['Category_ID']; ?>"
                                            data-type="<?php echo htmlspecialchars($category['Category_Type']); ?>"
                                            <?php echo ($formData['category_id'] ?? '') == $category['Category_ID'] ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($category['Category_Name']); ?> (<?php echo htmlspecialchars($category['Category_Type']); ?>)
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="invalid-feedback">
                                    Please select a category.
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="sub_category_id" class="form-label">Sub Category</label>
                                <select class="form-control" id="sub_category_id" name="sub_category_id">
                                    <option value="">Select sub category (optional)</option>
                                    <?php foreach ($subCategories as $subCategory): ?>
                                    <option value="<?php echo $subCategory['Sub_Category_ID']; ?>"
                                            data-category-id="<?php echo $subCategory['Category_ID']; ?>"
                                            data-category-type="<?php echo htmlspecialchars($subCategory['Category_Type']); ?>"
                                            <?php echo ($formData['sub_category_id'] ?? '') == $subCategory['Sub_Category_ID'] ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($subCategory['Sub_Category_Name']); ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3"
                                  placeholder="Optional description or notes"><?php echo htmlspecialchars($formData['description'] ?? ''); ?></textarea>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Create Transaction
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
                <h6>Transaction Guidelines:</h6>
                <ul class="small">
                    <li>Select appropriate transaction type (Income/Expense)</li>
                    <li>Choose the correct pocket for the transaction</li>
                    <li>Select relevant category and optional sub category</li>
                    <li>Use today's date or actual transaction date</li>
                    <li>Provide clear descriptions for better tracking</li>
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
                    <li>Income: Salary, business revenue, investments, etc.</li>
                    <li>Expense: Food, transportation, utilities, entertainment, etc.</li>
                    <li>Use sub categories for detailed classification</li>
                    <li>Regular transaction recording improves financial insights</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<script>
// Filter sub categories based on selected category
document.getElementById('category_id').addEventListener('change', function() {
    const categoryId = this.value;
    const subCategorySelect = document.getElementById('sub_category_id');
    const options = subCategorySelect.querySelectorAll('option');

    options.forEach(option => {
        if (option.value === '') {
            option.style.display = 'block';
        } else {
            const optionCategoryId = option.getAttribute('data-category-id');
            option.style.display = (optionCategoryId === categoryId) ? 'block' : 'none';
        }
    });

    // Reset sub category selection if category changes
    if (subCategorySelect.value !== '') {
        const selectedOption = subCategorySelect.querySelector('option[value="' + subCategorySelect.value + '"]');
        if (selectedOption && selectedOption.style.display === 'none') {
            subCategorySelect.value = '';
        }
    }
});

// Trigger filter on page load if category is pre-selected
document.addEventListener('DOMContentLoaded', function() {
    const categorySelect = document.getElementById('category_id');
    if (categorySelect.value) {
        categorySelect.dispatchEvent(new Event('change'));
    }
});
</script>

<?php include '../../components/footer.php'; ?>