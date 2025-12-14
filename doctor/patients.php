<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_any_role(['doctor']);
include __DIR__ . '/../includes/header.php';

$did = $_SESSION['user']['id'];


$sql = "
SELECT DISTINCT u.id, u.full_name, u.email, u.phone
FROM appointments a
JOIN users u ON u.id=a.patient_id
WHERE a.doctor_id=?
ORDER BY u.full_name
";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $did);
$stmt->execute();
$res = $stmt->get_result();
?>

<div class="container my-5">
    <div class="card shadow-sm">
        <div class="card-body">
            <h3 class="card-title text-center mb-4" style="color:#2c6e49;">My Patients</h3>
            <?php if ($res->num_rows === 0): ?>
                <p class="text-center text-muted">No patients found yet.</p>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover table-bordered align-middle text-center">
                        <thead class="table-light">
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($p = $res->fetch_assoc()): ?>
                                <tr>
                                    <td><?= htmlspecialchars($p['full_name']) ?></td>
                                    <td><?= htmlspecialchars($p['email']) ?></td>
                                    <td><?= htmlspecialchars($p['phone']) ?></td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>