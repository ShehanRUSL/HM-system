<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/csrf.php';
require_any_role(['patient']);
include __DIR__ . '/../includes/header.php';

$uid = $_SESSION['user']['id'];
$msg = '';
$err = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_check($_POST['csrf'] ?? '')) {
        $err = 'Invalid CSRF';
    } else {
        $doctor_id = (int) ($_POST['doctor_id'] ?? 0);
        $date = $_POST['appt_date'] ?? '';
        $time = $_POST['appt_time'] ?? '';
        if ($doctor_id && $date && $time) {
            $st = $conn->prepare("SELECT d.approval_status FROM doctors d WHERE d.user_id=?");
            $st->bind_param("i", $doctor_id);
            $st->execute();
            $status = $st->get_result()->fetch_assoc()['approval_status'] ?? 'pending';
            if ($status !== 'approved')
                $err = 'Doctor not available.';
            else {
                $ins = $conn->prepare("INSERT INTO appointments(patient_id,doctor_id,appt_date,appt_time) VALUES(?,?,?,?)");
                $ins->bind_param("iiss", $uid, $doctor_id, $date, $time);
                if ($ins->execute())
                    $msg = 'Appointment booked!';
                else
                    $err = 'Could not book.';
            }
        } else
            $err = 'Please select doctor, date and time.';
    }
}

$doctors = $conn->query("SELECT u.id,u.full_name,d.specialization FROM users u JOIN doctors d ON u.id=d.user_id WHERE u.role='doctor' AND d.approval_status='approved' ORDER BY u.full_name");
?>

<div class="container my-5">
    <div class="appointment-card mx-auto shadow p-5 rounded"
        style="max-width: 700px; background: rgba(255,255,255,0.95);">
        <h2 class="text-center mb-4" style="color:#2c6e49; font-weight:700;">Book Appointment</h2>

        <?php if ($msg): ?>
            <div class="alert alert-success"><?= htmlspecialchars($msg) ?></div>
        <?php endif; ?>
        <?php if ($err): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($err) ?></div>
        <?php endif; ?>

        <form method="post" class="row g-3">
            <input type="hidden" name="csrf" value="<?= csrf_token() ?>">

            <div class="col-12">
                <label class="form-label">Doctor</label>
                <select name="doctor_id" class="form-select" required>
                    <option value="">Select Doctor</option>
                    <?php while ($d = $doctors->fetch_assoc()): ?>
                        <option value="<?= $d['id'] ?>">
                            <?= htmlspecialchars($d['full_name'] . ' — ' . $d['specialization']) ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="col-md-6">
                <label class="form-label">Date</label>
                <input type="date" name="appt_date" class="form-control" required>
            </div>

            <div class="col-md-6">
                <label class="form-label">Time</label>
                <input type="time" name="appt_time" class="form-control" required>
            </div>

            <div class="col-12 text-center mt-3">
                <button class="btn btn-book">Book Appointment</button>
            </div>
        </form>
    </div>
</div>

<style>
    .appointment-card {
        transition: transform 0.3s;
    }

    .appointment-card:hover {
        transform: translateY(-3px);
    }

    .btn-book {
        background: #a8e6cf;
        color: #2c6e49;
        font-weight: 600;
        padding: 12px 35px;
        font-size: 1.1rem;
        border-radius: 10px;
        transition: all 0.3s ease;
        border: none;
    }

    .btn-book:hover {
        background: #2c6e49;
        color: #fff;
        transform: scale(1.05);
        box-shadow: 0 6px 15px rgba(0, 0, 0, 0.2);
    }
</style>

<?php include __DIR__ . '/../includes/footer.php'; ?>