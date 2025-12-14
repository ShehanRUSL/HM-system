<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/csrf.php';
require_any_role(['doctor']);
include __DIR__ . '/../includes/header.php';

$did = $_SESSION['user']['id'];


if ($_SERVER['REQUEST_METHOD'] === 'POST' && csrf_check($_POST['csrf'] ?? '')) {
    if (isset($_POST['complete_id'])) {
        $aid = (int) $_POST['complete_id'];
        $conn->begin_transaction();
        try {
            $u = $conn->prepare("UPDATE appointments SET status='completed' WHERE id=? AND doctor_id=? AND status='booked'");
            $u->bind_param("ii", $aid, $did);
            $u->execute();

            if (!empty($_POST['summary'])) {
                $s = $conn->prepare("INSERT INTO visit_notes(appointment_id,doctor_id,patient_id,summary)
                    SELECT id, doctor_id, patient_id, ? FROM appointments WHERE id=? AND doctor_id=?");
                $s->bind_param("sii", $_POST['summary'], $aid, $did);
                $s->execute();
            }
            $conn->commit();
        } catch (Exception $e) {
            $conn->rollback();
        }
    }
}


$today = date('Y-m-d');
$q = $conn->prepare("
  SELECT a.id, a.appt_date, a.appt_time, a.status, u.full_name as patient
  FROM appointments a 
  JOIN users u ON u.id=a.patient_id
  WHERE a.doctor_id=? AND a.appt_date >= ?
  ORDER BY a.appt_date, a.appt_time
");
$q->bind_param("is", $did, $today);
$q->execute();
$up = $q->get_result();


$past = $conn->prepare("
  SELECT a.id, a.appt_date, a.appt_time, a.status, u.full_name as patient
  FROM appointments a 
  JOIN users u ON u.id=a.patient_id
  WHERE a.doctor_id=? AND a.appt_date < ?
  ORDER BY a.appt_date DESC, a.appt_time DESC
");
$past->bind_param("is", $did, $today);
$past->execute();
$pastRes = $past->get_result();
?>

<div class="container my-5">
    <h4>Upcoming Appointments</h4>
    <table class="table table-striped table-bordered">
        <thead>
            <tr>
                <th>Patient</th>
                <th>Date</th>
                <th>Time</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($r = $up->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($r['patient']) ?></td>
                    <td><?= htmlspecialchars($r['appt_date']) ?></td>
                    <td><?= date("h:i A", strtotime($r['appt_time'])) ?></td>
                    <td><?= htmlspecialchars($r['status']) ?></td>
                    <td>
                        <?php if ($r['status'] === 'booked'): ?>
                            <form method="post" class="d-flex gap-2">
                                <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
                                <input type="hidden" name="complete_id" value="<?= $r['id'] ?>">
                                <input type="text" name="summary" class="form-control form-control-sm"
                                    placeholder="Summary (optional)">
                                <button class="btn btn-success btn-sm">Mark Completed</button>
                            </form>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <h4 class="mt-4">Past Sessions</h4>
    <table class="table table-striped table-bordered">
        <thead>
            <tr>
                <th>Patient</th>
                <th>Date</th>
                <th>Time</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($r = $pastRes->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($r['patient']) ?></td>
                    <td><?= htmlspecialchars($r['appt_date']) ?></td>
                    <td><?= date("h:i A", strtotime($r['appt_time'])) ?></td>
                    <td><?= htmlspecialchars($r['status']) ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>