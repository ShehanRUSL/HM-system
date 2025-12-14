<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/csrf.php';

require_role('admin');
include __DIR__ . '/../includes/header.php';

$msg = '';
$err = '';


if ($_SERVER['REQUEST_METHOD'] === 'POST' && csrf_check($_POST['csrf'] ?? '')) {


    if (isset($_POST['create'])) {
        $role = $_POST['role'] ?? 'patient';
        $full = trim($_POST['full_name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $pass = $_POST['password'] ?? '';
        $phone = trim($_POST['phone'] ?? '');

        if ($full && filter_var($email, FILTER_VALIDATE_EMAIL) && strlen($pass) >= 6 && in_array($role, ['patient', 'doctor', 'admin'], true)) {
            $hash = password_hash($pass, PASSWORD_BCRYPT);

            $conn->begin_transaction();
            try {
                $stmt = $conn->prepare("INSERT INTO users(role,email,password_hash,full_name,phone,is_active) VALUES(?,?,?,?,?,1)");
                $stmt->bind_param("sssss", $role, $email, $hash, $full, $phone);
                $stmt->execute();
                $uid = $stmt->insert_id;

                if ($role === 'patient') {
                    $stmt2 = $conn->prepare("INSERT INTO patients(user_id) VALUES(?)");
                    $stmt2->bind_param("i", $uid);
                    $stmt2->execute();
                } elseif ($role === 'doctor') {
                    $stmt2 = $conn->prepare("INSERT INTO doctors(user_id, specialization, license_no, approval_status) VALUES(?, '', '', 'approved')");
                    $stmt2->bind_param("i", $uid);
                    $stmt2->execute();
                }

                $conn->commit();
                $msg = 'User created successfully.';
            } catch (Exception $e) {
                $conn->rollback();
                $err = 'Create failed: ' . $e->getMessage();
            }
        } else {
            $err = 'Please fill all fields correctly. Password must be at least 6 characters.';
        }
    }

    if (isset($_POST['toggle_active'])) {
        $id = (int) $_POST['toggle_active'];
        if ($id !== $_SESSION['user']['id']) {
            $stmt = $conn->prepare("UPDATE users SET is_active = IF(is_active=1,0,1) WHERE id=?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $msg = 'User status updated.';
        } else {
            $err = 'You cannot deactivate your own account.';
        }
    }


    if (isset($_POST['delete_user'])) {
        $id = (int) $_POST['delete_user'];
        if ($id !== $_SESSION['user']['id']) {
            $stmt = $conn->prepare("DELETE FROM users WHERE id=?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $msg = 'User deleted successfully.';
        } else {
            $err = 'You cannot delete your own account.';
        }
    }
}


$users = $conn->query("SELECT id,role,full_name,email,phone,is_active,created_at FROM users ORDER BY created_at DESC");
?>

<div class="container my-5">
    <h2 class="table-title text-center mb-4">Manage Users</h2>


    <?php if ($msg): ?>
        <div class="alert alert-success"><?= htmlspecialchars($msg) ?></div>
    <?php endif; ?>
    <?php if ($err): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($err) ?></div>
    <?php endif; ?>


    <div class="card mb-4 shadow-sm">
        <div class="card-body">
            <h5 class="card-title">Create New User</h5>
            <form method="post" class="row g-2 align-items-end">
                <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
                <div class="col-md-2">
                    <label class="form-label">Role</label>
                    <select name="role" class="form-select">
                        <option value="patient">Patient</option>
                        <option value="doctor">Doctor</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Full Name</label>
                    <input name="full_name" class="form-control" placeholder="Full Name" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" placeholder="Email" required>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Phone</label>
                    <input name="phone" class="form-control" placeholder="Phone">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" placeholder="Min 6 chars" minlength="6"
                        required>
                </div>
                <div class="col-12 mt-2">
                    <button name="create" class="btn btn-primary">Create User</button>
                </div>
            </form>
        </div>
    </div>


    <div class="table-responsive shadow-sm p-3 bg-white rounded">
        <table class="table table-hover table-bordered align-middle text-center">
            <thead style="background: linear-gradient(90deg, #a8e6cf, #dcedc1); color: #2c6e49;">
                <tr>
                    <th>ID</th>
                    <th>Role</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Active</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($u = $users->fetch_assoc()): ?>
                    <tr>
                        <td><?= $u['id'] ?></td>
                        <td><?= htmlspecialchars($u['role']) ?></td>
                        <td><?= htmlspecialchars($u['full_name']) ?></td>
                        <td><?= htmlspecialchars($u['email']) ?></td>
                        <td><?= htmlspecialchars($u['phone']) ?></td>
                        <td>
                            <?php if ($u['is_active']): ?>
                                <span class="badge bg-success">Active</span>
                            <?php else: ?>
                                <span class="badge bg-danger">Inactive</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($u['id'] !== $_SESSION['user']['id']): ?>
                                <form method="post" class="d-inline">
                                    <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
                                    <button name="toggle_active" value="<?= $u['id'] ?>"
                                        class="btn btn-sm btn-warning">Toggle</button>
                                </form>
                                <form method="post" class="d-inline" onsubmit="return confirm('Delete this user?');">
                                    <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
                                    <button name="delete_user" value="<?= $u['id'] ?>"
                                        class="btn btn-sm btn-danger">Delete</button>
                                </form>
                            <?php else: ?>
                                <span class="text-muted">No actions</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>

<style>
    .table-title {
        font-size: 1.8rem;
        font-weight: 600;
        color: #2c6e49;
        border-bottom: 2px solid #2c6e49;
        display: inline-block;
        padding-bottom: 5px;
        margin-bottom: 20px;
    }

    .table-responsive {
        background: #ffffff;
        border-radius: 10px;
        padding: 20px;
    }

    .table th,
    .table td {
        vertical-align: middle;
    }

    .btn-sm {
        font-size: 0.85rem;
        padding: 0.3rem 0.6rem;
    }
</style>