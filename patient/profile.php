<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/csrf.php';
require_any_role(['patient']);
include __DIR__ . '/../includes/header.php';

$uid = $_SESSION['user']['id'];
$msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && csrf_check($_POST['csrf'] ?? '')) {
    $full = trim($_POST['full_name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $gender = $_POST['gender'] ?? null;
    $dob = $_POST['dob'] ?? null;
    $addr = trim($_POST['address'] ?? '');
    if ($full) {
        $u = $conn->prepare("UPDATE users SET full_name=?, phone=? WHERE id=?");
        $u->bind_param("ssi", $full, $phone, $uid);
        $u->execute();
        $p = $conn->prepare("UPDATE patients SET gender=?, dob=?, address=? WHERE user_id=?");
        $p->bind_param("sssi", $gender, $dob, $addr, $uid);
        $p->execute();
        $msg = 'Profile updated successfully.';
        $_SESSION['user']['full_name'] = $full;
    }
}

$info = $conn->query("SELECT u.full_name,u.email,u.phone,p.gender,p.dob,p.address FROM users u JOIN patients p ON u.id=p.user_id WHERE u.id=$uid")->fetch_assoc();
?>

<div class="container my-5">
    <div class="profile-card mx-auto p-4 rounded" style="max-width:900px; background: rgba(255,255,255,0.95);">
        <h2 class="text-center mb-4" style="color:#2c6e49; font-weight:700;">My Profile</h2>

        <?php if ($msg): ?>
            <div class="alert alert-success text-center"><?= htmlspecialchars($msg) ?></div>
        <?php endif; ?>

        <form method="post" class="row g-3">
            <input type="hidden" name="csrf" value="<?= csrf_token() ?>">

            <div class="col-md-6">
                <label class="form-label">Full Name</label>
                <input name="full_name" class="form-control" value="<?= htmlspecialchars($info['full_name']) ?>"
                    required>
            </div>

            <div class="col-md-6">
                <label class="form-label">Email</label>
                <input class="form-control" value="<?= htmlspecialchars($info['email']) ?>" disabled>
            </div>

            <div class="col-md-6">
                <label class="form-label">Phone</label>
                <input name="phone" class="form-control" value="<?= htmlspecialchars($info['phone']) ?>">
            </div>

            <div class="col-md-3">
                <label class="form-label">Gender</label>
                <select name="gender" class="form-select">
                    <?php foreach (['Male', 'Female'] as $g): ?>
                        <option <?= $info['gender'] === $g ? 'selected' : '' ?>><?= $g ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-md-3">
                <label class="form-label">DOB</label>
                <input type="date" name="dob" class="form-control" value="<?= htmlspecialchars($info['dob']) ?>">
            </div>

            <div class="col-12">
                <label class="form-label">Address</label>
                <textarea name="address" class="form-control"
                    rows="3"><?= htmlspecialchars($info['address']) ?></textarea>
            </div>

            <div class="col-12 text-center">
                <button class="btn btn-primary btn-lg">Save Changes</button>
            </div>
        </form>
    </div>
</div>

<style>
    .profile-card {
        border-radius: 12px;
        background: rgba(255, 255, 255, 0.95);
    }

    .form-label {
        font-weight: 500;
        color: #2c6e49;
    }

    .btn-primary {
        background: #2c6e49;
        border-color: #2c6e49;
        transition: background 0.3s, transform 0.2s;
    }

    .btn-primary:hover {
        background: #1f5236;
        transform: scale(1.03);
    }

    @media (max-width: 768px) {
        .profile-card {
            padding: 2rem 1rem;
        }

        .btn-lg {
            width: 100%;
        }
    }
</style>

<?php include __DIR__ . '/../includes/footer.php'; ?>