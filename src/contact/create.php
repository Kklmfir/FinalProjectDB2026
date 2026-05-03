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
        'phone' => sanitize($_POST['phone'] ?? ''),
        'relation_type' => sanitize($_POST['relation_type'] ?? ''),
    ];

    $validation = sanitizeAndValidate($formData, [
        'name' => 'required',
        'phone' => 'required',
        'relation_type' => 'required',
    ]);

    if ($validation['errors']) {
        $errors = $validation['errors'];
    } else {
        try {
            $stmt = $pdo->prepare("INSERT INTO Contact (Contact_Name, Phone_Number, Relation_Type) VALUES (?, ?, ?)");
            $stmt->execute([$formData['name'], $formData['phone'], $formData['relation_type']]);

            $_SESSION['success'] = 'Contact created successfully!';
            header('Location: index.php');
            exit;
        } catch (PDOException $e) {
            $errors[] = 'Failed to create contact: ' . $e->getMessage();
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
                    <i class="fas fa-plus text-primary"></i> Add New Contact
                </h1>
                <p class="text-muted">Create a new contact entry</p>
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
                    <i class="fas fa-edit"></i> Contact Details
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
                        <label for="name" class="form-label">Contact Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control <?php echo in_array('Name is required', $errors) ? 'is-invalid' : ''; ?>"
                               id="name" name="name" required
                               value="<?php echo htmlspecialchars($formData['name'] ?? ''); ?>"
                               placeholder="Enter contact name">
                        <div class="invalid-feedback">
                            Please provide a valid contact name.
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="phone" class="form-label">Phone Number <span class="text-danger">*</span></label>
                        <input type="tel" class="form-control <?php echo in_array('Phone is required', $errors) ? 'is-invalid' : ''; ?>"
                               id="phone" name="phone" required
                               value="<?php echo htmlspecialchars($formData['phone'] ?? ''); ?>"
                               placeholder="Enter phone number">
                        <div class="invalid-feedback">
                            Please provide a valid phone number.
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="relation_type" class="form-label">Relation Type <span class="text-danger">*</span></label>
                        <select class="form-control <?php echo in_array('Relation type is required', $errors) ? 'is-invalid' : ''; ?>"
                                id="relation_type" name="relation_type" required>
                            <option value="">Select relation type</option>
                            <option value="Family" <?php echo ($formData['relation_type'] ?? '') === 'Family' ? 'selected' : ''; ?>>Family</option>
                            <option value="Friend" <?php echo ($formData['relation_type'] ?? '') === 'Friend' ? 'selected' : ''; ?>>Friend</option>
                            <option value="Colleague" <?php echo ($formData['relation_type'] ?? '') === 'Colleague' ? 'selected' : ''; ?>>Colleague</option>
                            <option value="Business" <?php echo ($formData['relation_type'] ?? '') === 'Business' ? 'selected' : ''; ?>>Business</option>
                            <option value="Other" <?php echo ($formData['relation_type'] ?? '') === 'Other' ? 'selected' : ''; ?>>Other</option>
                        </select>
                        <div class="invalid-feedback">
                            Please select a relation type.
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Create Contact
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
                <h6>Contact Guidelines:</h6>
                <ul class="small">
                    <li>Use full names for better identification</li>
                    <li>Include country code for international numbers</li>
                    <li>Choose appropriate relation types</li>
                    <li>Keep contact information up to date</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<?php include '../../components/footer.php'; ?>