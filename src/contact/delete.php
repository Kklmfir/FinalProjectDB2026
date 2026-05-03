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

// Fetch contact
$stmt = $pdo->prepare("SELECT * FROM Contact WHERE Contact_ID = ?");
$stmt->execute([$id]);
$contact = $stmt->fetch();

if (!$contact) {
    $_SESSION['error'] = 'Contact not found!';
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    validateCSRF();

    try {
        // Check if contact is being used in debt_loan
        $checkStmt = $pdo->prepare("SELECT COUNT(*) FROM Debt_Loan WHERE Contact_ID = ?");
        $checkStmt->execute([$id]);
        $usageCount = $checkStmt->fetchColumn();

        if ($usageCount > 0) {
            $_SESSION['error'] = 'Cannot delete contact. It is being used in ' . $usageCount . ' debt/loan record(s).';
            header('Location: index.php');
            exit;
        }

        // Delete contact
        $stmt = $pdo->prepare("DELETE FROM Contact WHERE Contact_ID = ?");
        $stmt->execute([$id]);

        $_SESSION['success'] = 'Contact deleted successfully!';
        header('Location: index.php');
        exit;
    } catch (PDOException $e) {
        $_SESSION['error'] = 'Failed to delete contact: ' . $e->getMessage();
        header('Location: index.php');
        exit;
    }
}

include '../../components/header.php';
?>

<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h3 mb-0 text-gray-800">
                    <i class="fas fa-trash text-danger"></i> Delete Contact
                </h1>
                <p class="text-muted">Confirm deletion of contact</p>
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
        <div class="card border-danger">
            <div class="card-header bg-danger text-white">
                <h6 class="m-0 font-weight-bold">
                    <i class="fas fa-exclamation-triangle"></i> Confirm Deletion
                </h6>
            </div>
            <div class="card-body">
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle"></i>
                    <strong>Warning!</strong> This action cannot be undone. Are you sure you want to delete this contact?
                </div>

                <div class="card mb-4">
                    <div class="card-header">
                        <h6 class="m-0 font-weight-bold text-primary">Contact Details</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>ID:</strong> <?php echo htmlspecialchars($contact['Contact_ID']); ?></p>
                                <p><strong>Name:</strong> <?php echo htmlspecialchars($contact['Contact_Name']); ?></p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Phone:</strong> <?php echo htmlspecialchars($contact['Phone_Number']); ?></p>
                                <p><strong>Relation Type:</strong> <?php echo htmlspecialchars($contact['Relation_Type']); ?></p>
                            </div>
                        </div>
                    </div>
                </div>

                <form method="POST">
                    <input type="hidden" name="<?php echo CSRF_TOKEN_NAME; ?>" value="<?php echo $_SESSION[CSRF_TOKEN_NAME]; ?>">

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-trash"></i> Yes, Delete Contact
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
                    <i class="fas fa-info-circle"></i> Important Notes
                </h6>
            </div>
            <div class="card-body">
                <ul class="small text-muted">
                    <li>Deleting this contact will permanently remove it</li>
                    <li>If the contact is used in debt/loan records, deletion will be blocked</li>
                    <li>Consider archiving instead of deleting if data integrity is important</li>
                    <li>This action cannot be reversed</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<?php include '../../components/footer.php'; ?>