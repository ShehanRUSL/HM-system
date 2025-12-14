<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/csrf.php';
require_any_role(['patient']);
include __DIR__ . '/../includes/header.php';

$uid = $_SESSION['user']['id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cancel_id'])) {
    if (csrf_check($_POST['csrf'] ?? '')) {
        $id = (int) $_POST['cancel_id'];
        $stmt = $conn->prepare("UPDATE appointments SET status='cancelled' WHERE id=? AND patient_id=? AND status='booked'");
        $stmt->bind_param("ii", $id, $uid);
        $stmt->execute();
    }
}

$q = $conn->prepare("
  SELECT a.id, a.appt_date, a.appt_time, a.status, u.full_name AS doctor
  FROM appointments a
  JOIN users u ON u.id=a.doctor_id
  WHERE a.patient_id=?
  ORDER BY a.appt_date DESC, a.appt_time DESC
");
$q->bind_param("i", $uid);
$q->execute();
$res = $q->get_result();
?>

<div class="container my-5">
    <div class="appointments-card mx-auto p-4 rounded" style="max-width:1100px; background: rgba(255,255,255,0.95);">
        <h2 class="text-center mb-4" style="color:#2c6e49; font-weight:700;">My Appointments</h2>
        <div class="table-responsive">
            <table class="table align-middle text-center border rounded">
                <thead style="background: linear-gradient(90deg, #a8e6cf, #dcedc1); color: #2c6e49;">
                    <tr>
                        <th>Doctor</th>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($r = $res->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($r['doctor']) ?></td>
                            <td><?= htmlspecialchars($r['appt_date']) ?></td>
                            <td>
                                <?= date("h:i A", strtotime($r['appt_time'])) ?>
                            </td>
                            <td>
                                <?php
                                $status = $r['status'];
                                $badge = match ($status) {
                                    'booked' => 'bg-primary',
                                    'completed' => 'bg-success',
                                    'cancelled' => 'bg-danger',
                                    default => 'bg-secondary'
                                };
                                ?>
                                <span class="badge <?= $badge ?>"><?= ucfirst($status) ?></span>
                            </td>
                            <td>
                                <?php if ($r['status'] === 'booked'): ?>
                                    <form method="post" class="d-inline">
                                        <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
                                        <input type="hidden" name="cancel_id" value="<?= $r['id'] ?>">
                                        <button class="btn btn-sm btn-danger"
                                            onclick="return confirm('Cancel this appointment?')">Cancel</button>
                                    </form>
                                <?php else: ?>
                                    <span class="text-muted">—</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
    .appointments-card {}

    .table {
        border-collapse: separate;
        border-spacing: 0;
        border-radius: 12px;
        overflow: hidden;
        font-size: 1rem;
    }

    .table th,
    .table td {
        vertical-align: middle;
        padding: 1rem 0.8rem;
    }

    .badge {
        padding: 0.5em 0.85em;
        font-size: 0.9rem;
        border-radius: 0.6rem;
    }

    @media (max-width: 768px) {
        .appointments-card {
            padding: 2rem 1rem;
        }

        .table th,
        .table td {
            padding: 0.8rem 0.5rem;
            font-size: 0.9rem;
        }

        .btn-sm {
            padding: 0.4rem 0.7rem;
            font-size: 0.85rem;
        }
    }
</style>

<?php include __DIR__ . '/../includes/footer.php'; ?>