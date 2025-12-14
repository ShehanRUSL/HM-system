<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/csrf.php';
require_any_role(['doctor']);
include __DIR__ . '/../includes/header.php';

$uid = $_SESSION['user']['id'];
$msg = '';


if ($_SERVER['REQUEST_METHOD'] === 'POST' && csrf_check($_POST['csrf'] ?? '')) {
    $full = trim($_POST['full_name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $spec = trim($_POST['specialization'] ?? '');
    if ($full && $spec) {
        $u = $conn->prepare("UPDATE users SET full_name=?, phone=? WHERE id=?");
        $u->bind_param("ssi", $full, $phone, $uid);
        $u->execute();

        $d = $conn->prepare("UPDATE doctors SET specialization=? WHERE user_id=?");
        $d->bind_param("si", $spec, $uid);
        $d->execute();

        $_SESSION['user']['full_name'] = $full;
        $msg = 'Profile updated successfully.';
    }
}

$info = $conn->query("
    SELECT u.full_name, u.email, u.phone, d.specialization, d.approval_status
    FROM users u
    JOIN doctors d ON u.id=d.user_id
    WHERE u.id=$uid
")->fetch_assoc();
?>

<div class="container my-5">
    <h4 class="mb-4">My Profile</h4>

    <?php if ($msg): ?>
        <div class="alert alert-success"><?= htmlspecialchars($msg) ?></div>
    <?php endif; ?>

    <div class="alert alert-info">
        Approval status: <strong><?= htmlspecialchars($info['approval_status']) ?></strong>
    </div>

    <form method="post" class="bg-white p-4 rounded shadow-sm">
        <input type="hidden" name="csrf" value="<?= csrf_token() ?>">

        <div class="mb-3">
            <label class="form-label">Full Name</label>
            <input type="text" name="full_name" class="form-control" value="<?= htmlspecialchars($info['full_name']) ?>"
                required>
        </div>

        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" class="form-control" value="<?= htmlspecialchars($info['email']) ?>" disabled>
        </div>

        <div class="mb-3">
            <label class="form-label">Phone</label>
            <input type="text" name="phone" class="form-control" value="<?= htmlspecialchars($info['phone']) ?>">
        </div>

        <div class="mb-3">
            <label class="form-label">Specialization</label>
            <input type="text" name="specialization" class="form-control"
                value="<?= htmlspecialchars($info['specialization']) ?>" required>
        </div>

        <button type="submit" class="btn btn-primary">Save Changes</button>
    </form>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>