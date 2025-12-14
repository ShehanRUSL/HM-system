<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/csrf.php';
if (session_status() === PHP_SESSION_NONE)
    session_start();

$msg = '';
$err = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_check($_POST['csrf'] ?? '')) {
        $err = 'Invalid CSRF';
    } else {
        $full = trim($_POST['full_name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $pass = $_POST['password'] ?? '';
        $phone = trim($_POST['phone'] ?? '');
        $gender = $_POST['gender'] ?? null;
        $dob = $_POST['dob'] ?? null;
        $addr = trim($_POST['address'] ?? '');

        if (!$full || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($pass) < 6) {
            $err = 'Please fill all fields correctly.';
        } else {
            $hash = password_hash($pass, PASSWORD_BCRYPT);
            $conn->begin_transaction();
            try {
                $stmt = $conn->prepare("INSERT INTO users(role,email,password_hash,full_name,phone) VALUES('patient', ?, ?, ?, ?)");
                $stmt->bind_param("ssss", $email, $hash, $full, $phone);
                $stmt->execute();
                $uid = $stmt->insert_id;

                $stmt2 = $conn->prepare("INSERT INTO patients(user_id,gender,dob,address) VALUES(?,?,?,?)");
                $stmt2->bind_param("isss", $uid, $gender, $dob, $addr);
                $stmt2->execute();

                $conn->commit();
                $msg = 'Registration successful. You can login now.';
            } catch (Exception $e) {
                $conn->rollback();
                $err = 'Email may already exist or data invalid.';
            }
        }
    }
}

include __DIR__ . '/../includes/header.php';
?>

<div class="d-flex justify-content-center align-items-center" style="min-height:100vh;">
    <div class="col-md-6 col-lg-5">
        <div class="card shadow">
            <div class="card-body p-4">
                <h4 class="text-center mb-4">Patient Registration</h4>

                <?php if ($msg): ?>
                    <div class="alert alert-success text-center"><?= htmlspecialchars($msg) ?></div>
                <?php endif; ?>
                <?php if ($err): ?>
                    <div class="alert alert-danger text-center"><?= htmlspecialchars($err) ?></div>
                <?php endif; ?>

                <form method="post" novalidate>
                    <input type="hidden" name="csrf" value="<?= csrf_token() ?>">

                    <div class="mb-3">
                        <label class="form-label">Full Name</label>
                        <input name="full_name" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" minlength="6" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Phone</label>
                        <input name="phone" class="form-control" pattern="[0-9]{10}" required>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Gender</label>
                            <select name="gender" class="form-select" required>
                                <option value="">Select</option>
                                <option>Male</option>
                                <option>Female</option>

                            </select>
                        </div>
                        <div class="col-md-8 mb-3">
                            <label class="form-label">Date of Birth</label>
                            <input type="date" name="dob" class="form-control" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Address</label>
                        <textarea name="address" class="form-control" rows="3" required></textarea>
                    </div>

                    <button class="btn btn-primary w-100">Register</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>