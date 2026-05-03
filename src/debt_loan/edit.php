<?php
require_once '../../config/app.php';
require_once '../../config/database.php';
require_once '../../helpers/functions.php';
require_once '../../helpers/validation.php';
require_once '../../helpers/security.php';

$pdo = require '../../config/database.php';

// Get contacts and pockets for dropdowns
$contactsStmt = $pdo->query("SELECT Contact_ID, Contact_Name FROM Contact ORDER BY Contact_Name");
$contacts = $contactsStmt->fetchAll();

$pocketsStmt = $pdo->query("SELECT Pocket_ID, Pocket_Name FROM Pocket ORDER BY Pocket_Name");
$pockets = $pocketsStmt->fetchAll();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$id) {
    header('Location: index.php');
    exit;
}

// Fetch debt/loan
$stmt = $pdo->prepare("SELECT * FROM Debt_Loan WHERE Debt_Loan_ID = ?");
$stmt->execute([$id]);
$debtLoan = $stmt->fetch();

if (!$debtLoan) {
    $_SESSION['error'] = 'Debt/Loan record not found!';
    header('Location: index.php');
    exit;
}

$errors = [];
$formData = $debtLoan;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    validateCSRF();

    $formData = [
        'contact_id' => sanitize($_POST['contact_id'] ?? ''),
        'pocket_id' => sanitize($_POST['pocket_id'] ?? ''),
        'amount' => sanitize($_POST['amount'] ?? ''),
        'due_date' => sanitize($_POST['due_date'] ?? ''),
        'status' => sanitize($_POST['status'] ?? ''),
        'description' => sanitize($_POST['description'] ?? ''),
    ];

    $validation = sanitizeAndValidate($formData, [
        'contact_id' => 'required',
        'pocket_id' => 'required',
        'amount' => 'required|numeric',
        'due_date' => 'required',
        'status' => 'required',
    ]);

    if ($validation['errors']) {
        $errors = $validation['errors'];
    } else {
        try {
            $stmt = $pdo->prepare("UPDATE Debt_Loan SET Contact_ID = ?, Pocket_ID = ?, Amount = ?, Due_Date = ?, Status = ?, Description = ? WHERE Debt_Loan_ID = ?");
            $stmt->execute([
                $formData['contact_id'],
                $formData['pocket_id'],
                $formData['amount'],
                $formData['due_date'],
                $formData['status'],
                $formData['description'],
                $id
            ]);

            $_SESSION['success'] = 'Debt/Loan record updated successfully!';
            header('Location: index.php');
            exit;
        } catch (PDOException $e) {
            $errors[] = 'Failed to update debt/loan record: ' . $e->getMessage();
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
                    <i class="fas fa-edit text-primary"></i> Edit Debt/Loan Record
                </h1>
                <p class="text-muted">Update debt/loan information</p>
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
                    <i class="fas fa-edit"></i> Debt/Loan Details
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
                                <label for="contact_id" class="form-label">Contact <span class="text-danger">*</span></label>
                                <select class="form-control <?php echo in_array('Contact is required', $errors) ? 'is-invalid' : ''; ?>"
                                        id="contact_id" name="contact_id" required>
                                    <option value="">Select contact</option>
                                    <?php foreach ($contacts as $contact): ?>
                                    <option value="<?php echo $contact['Contact_ID']; ?>"
                                            <?php echo $formData['Contact_ID'] == $contact['Contact_ID'] ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($contact['Contact_Name']); ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="invalid-feedback">
                                    Please select a contact.
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
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="amount" class="form-label">Amount <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">IDR</span>
                                    <input type="number" class="form-control <?php echo in_array('Amount is required', $errors) || in_array('Amount must be numeric', $errors) ? 'is-invalid' : ''; ?>"
                                           id="amount" name="amount" required min="0" step="0.01"
                                           value="<?php echo htmlspecialchars($formData['Amount']); ?>"
                                           placeholder="0.00">
                                    <div class="invalid-feedback">
                                        Please provide a valid amount.
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="due_date" class="form-label">Due Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control <?php echo in_array('Due date is required', $errors) ? 'is-invalid' : ''; ?>"
                                       id="due_date" name="due_date" required
                                       value="<?php echo htmlspecialchars($formData['Due_Date']); ?>">
                                <div class="invalid-feedback">
                                    Please provide a due date.
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                        <select class="form-control <?php echo in_array('Status is required', $errors) ? 'is-invalid' : ''; ?>"
                                id="status" name="status" required>
                            <option value="">Select status</option>
                            <option value="Pending" <?php echo $formData['Status'] === 'Pending' ? 'selected' : ''; ?>>Pending</option>
                            <option value="Paid" <?php echo $formData['Status'] === 'Paid' ? 'selected' : ''; ?>>Paid</option>
                        </select>
                        <div class="invalid-feedback">
                            Please select a status.
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3"
                                  placeholder="Optional description or notes"><?php echo htmlspecialchars($formData['Description'] ?? ''); ?></textarea>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Update Debt/Loan
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
                    <i class="fas fa-info-circle"></i> Record Info
                </h6>
            </div>
            <div class="card-body">
                <p><strong>ID:</strong> <?php echo $debtLoan['Debt_Loan_ID']; ?></p>
                <p><strong>Created:</strong> <?php echo formatDate($debtLoan['Due_Date']); ?></p>
                <p><strong>Status:</strong>
                    <span class="badge bg-<?php echo $debtLoan['Status'] === 'Paid' ? 'success' : 'warning'; ?>">
                        <?php echo htmlspecialchars($debtLoan['Status']); ?>
                    </span>
                </p>
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
                    <li>Change status to Paid when transaction is complete</li>
                    <li>Update due dates if terms change</li>
                    <li>Modify amounts only if agreed upon</li>
                    <li>Add notes in description for tracking changes</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<?php include '../../components/footer.php'; ?>