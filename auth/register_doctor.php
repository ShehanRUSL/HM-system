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
        $err = 'Invalid CSRF token.';
    } else {
        $full = trim($_POST['full_name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $pass = $_POST['password'] ?? '';
        $phone = trim($_POST['phone'] ?? '');
        $spec = trim($_POST['specialization'] ?? '');
        $lic = trim($_POST['license_no'] ?? '');

        if (
            !$full ||
            !filter_var($email, FILTER_VALIDATE_EMAIL) ||
            strlen($pass) < 6 ||
            !$phone || !preg_match('/^[0-9]{10}$/', $phone) ||
            !$spec ||
            !$lic
        ) {
            $err = 'Please fill all fields correctly. Phone must be 10 digits, password at least 6 characters.';
        } else {
            $stmt_check = $conn->prepare("SELECT id FROM users WHERE email=? LIMIT 1");
            $stmt_check->bind_param("s", $email);
            $stmt_check->execute();
            $res_check = $stmt_check->get_result();
            if ($res_check->num_rows > 0) {
                $err = 'Email already exists.';
            } else {
                $stmt_check2 = $conn->prepare("SELECT id FROM doctors WHERE license_no=? LIMIT 1");
                $stmt_check2->bind_param("s", $lic);
                $stmt_check2->execute();
                $res_check2 = $stmt_check2->get_result();
                if ($res_check2->num_rows > 0) {
                    $err = 'License number already exists.';
                } else {
                    $hash = password_hash($pass, PASSWORD_BCRYPT);
                    $conn->begin_transaction();
                    try {
                        $stmt = $conn->prepare("INSERT INTO users(role,email,password_hash,full_name,phone,is_active) VALUES('doctor', ?, ?, ?, ?, 1)");
                        $stmt->bind_param("ssss", $email, $hash, $full, $phone);
                        $stmt->execute();
                        $uid = $stmt->insert_id;

                        $stmt2 = $conn->prepare("INSERT INTO doctors(user_id,specialization,license_no,approval_status) VALUES(?, ?, ?, 'pending')");
                        $stmt2->bind_param("iss", $uid, $spec, $lic);
                        $stmt2->execute();

                        $conn->commit();
                        $msg = 'Registration submitted successfully. Please wait for admin approval.';
                    } catch (Exception $e) {
                        $conn->rollback();
                        $err = 'Database error: ' . $e->getMessage();
                    }
                }
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
                <h4 class="text-center mb-4">Doctor Registration</h4>

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
                        <input type="text" name="full_name" class="form-control" required
                            value="<?= htmlspecialchars($full ?? '') ?>">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" required
                            value="<?= htmlspecialchars($email ?? '') ?>">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" minlength="6" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Phone</label>
                        <input type="text" name="phone" class="form-control" pattern="[0-9]{10}" required
                            value="<?= htmlspecialchars($phone ?? '') ?>">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Specialization</label>
                        <input type="text" name="specialization" class="form-control" required
                            value="<?= htmlspecialchars($spec ?? '') ?>">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">License No</label>
                        <input type="text" name="license_no" class="form-control" required
                            value="<?= htmlspecialchars($lic ?? '') ?>">
                    </div>

                    <button class="btn btn-primary w-100">Register</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>