<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/csrf.php';

require_role('admin');
include __DIR__ . '/../includes/header.php';

$popupMsg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && csrf_check($_POST['csrf'] ?? '')) {
    $uid = (int) ($_POST['user_id'] ?? 0);
    $action = $_POST['action'] ?? '';
    if ($uid && in_array($action, ['approve', 'reject'], true)) {
        $st = $conn->prepare("UPDATE doctors SET approval_status=? WHERE user_id=?");
        $status = $action === 'approve' ? 'approved' : 'rejected';
        $st->bind_param("si", $status, $uid);
        $st->execute();
        $popupMsg = "Doctor has been " . ($action === 'approve' ? 'approved ✅' : 'rejected ⚠️');
    }
}

$res = $conn->query("
  SELECT u.id,u.full_name,u.email,u.phone,d.specialization,d.license_no,d.approval_status
  FROM users u JOIN doctors d ON u.id=d.user_id
  ORDER BY d.approval_status, u.full_name
");
?>


<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<div class="container my-5">
    <h2 class="table-title text-center">Approve Doctors</h2>
    <div class="table-responsive shadow-sm p-3 bg-white rounded">
        <table class="table table-hover table-bordered align-middle text-center">
            <thead style="background: linear-gradient(90deg, #a8e6cf, #dcedc1); color: #2c6e49;">
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Specialization</th>
                    <th>License</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($r = $res->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($r['full_name']) ?></td>
                        <td><?= htmlspecialchars($r['email']) ?></td>
                        <td><?= htmlspecialchars($r['phone']) ?></td>
                        <td><?= htmlspecialchars($r['specialization']) ?></td>
                        <td><?= htmlspecialchars($r['license_no']) ?></td>
                        <td>
                            <?php
                            $status = strtolower($r['approval_status']);
                            $badgeClass = 'secondary';
                            if ($status === 'approved')
                                $badgeClass = 'success';
                            if ($status === 'pending')
                                $badgeClass = 'warning text-dark';
                            if ($status === 'rejected')
                                $badgeClass = 'danger';
                            ?>
                            <span class="badge bg-<?= $badgeClass ?>"><?= ucfirst($status) ?></span>
                        </td>
                        <td>
                            <form method="post" class="d-inline">
                                <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
                                <input type="hidden" name="user_id" value="<?= $r['id'] ?>">
                                <button name="action" value="approve" class="btn btn-sm btn-success">Approve</button>
                                <button name="action" value="reject" class="btn btn-sm btn-warning">Reject</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>

<?php if ($popupMsg): ?>
    <script>
        Swal.fire({
            title: 'Success!',
            text: <?= json_encode($popupMsg) ?>,
            icon: 'success',
            confirmButtonText: 'OK',
            position: 'center',
            showClass: { popup: 'animate__animated animate__fadeInDown' },
            hideClass: { popup: 'animate__animated animate__fadeOutUp' }
        });
    </script>
<?php endif; ?>


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