<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/csrf.php';

if (session_status() === PHP_SESSION_NONE)
    session_start();


$adminEmail = 'admin@gmail.com';
$adminPassword = '123456';
$stmt = $conn->prepare("SELECT id FROM users WHERE email=? LIMIT 1");
$stmt->bind_param("s", $adminEmail);
$stmt->execute();
$res = $stmt->get_result();
if (!$res->fetch_assoc()) {
    $hash = password_hash($adminPassword, PASSWORD_BCRYPT);
    $stmt2 = $conn->prepare("INSERT INTO users(role,email,password_hash,full_name,phone,is_active) VALUES('admin', ?, ?, 'Admin User','',1)");
    $stmt2->bind_param("ss", $adminEmail, $hash);
    $stmt2->execute();
}


$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $csrf = $_POST['csrf'] ?? '';
    $email = trim($_POST['email'] ?? '');
    $pass = $_POST['password'] ?? '';

    if (!csrf_check($csrf)) {
        $error = 'Invalid CSRF token';
    } elseif (!$email || !$pass) {
        $error = 'Please enter both email and password';
    } else {
        $stmt = $conn->prepare("SELECT id, role, email, password_hash, full_name, is_active FROM users WHERE email=? LIMIT 1");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $res = $stmt->get_result();

        if ($row = $res->fetch_assoc()) {
            if (!$row['is_active']) {
                $error = 'Account disabled';
            } elseif (password_verify($pass, $row['password_hash'])) {
                $_SESSION['user'] = [
                    'id' => (int) $row['id'],
                    'role' => $row['role'],
                    'email' => $row['email'],
                    'full_name' => $row['full_name'],
                ];

                if ($row['role'] === 'doctor') {
                    $chk = $conn->prepare("SELECT approval_status FROM doctors WHERE user_id=? LIMIT 1");
                    $chk->bind_param("i", $_SESSION['user']['id']);
                    $chk->execute();
                    $statusRow = $chk->get_result()->fetch_assoc();
                    $status = $statusRow['approval_status'] ?? 'pending';
                    if ($status !== 'approved') {
                        unset($_SESSION['user']);
                        $error = 'Doctor account not approved yet.';
                    }
                }

                if (!$error) {
                    $dest = [
                        'admin' => BASE_URL . '/admin/dashboard.php',
                        'doctor' => BASE_URL . '/doctor/dashboard.php',
                        'patient' => BASE_URL . '/patient/dashboard.php'
                    ][$row['role']];

                    header('Location: ' . $dest);
                    exit;
                }
            } else {
                $error = 'Invalid credentials';
            }
        } else {
            $error = 'Invalid credentials';
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - HMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body,
        html {
            height: 100%;
            margin: 0;
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(135deg, #a8e6cf, #dcedc1);
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .login-card {
            background: #ffffffee;
            padding: 50px 60px;
            border-radius: 25px;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.25);
            width: 100%;
            max-width: 650px;

            text-align: center;
            animation: fadeIn 1s ease;
        }

        .login-card h4 {
            margin-bottom: 35px;
            color: #2c6e49;
            font-weight: 700;
            font-size: 2rem;
        }

        .login-card .form-control {
            border-radius: 12px;
            padding: 14px;
            font-size: 1rem;
            border: 1px solid #b2dfdb;
        }

        .login-card .form-control:focus {
            border-color: #2c6e49;
            box-shadow: 0 0 8px rgba(44, 110, 73, 0.3);
        }

        .login-card .btn-primary {
            width: 100%;
            padding: 14px;
            border-radius: 12px;
            font-size: 1.1rem;
            font-weight: 600;
            background: #2c6e49;
            border: none;
            transition: 0.3s;
        }

        .login-card .btn-primary:hover {
            background: #1e5036;
        }

        .btn-outline-secondary {
            border-color: #2c6e49;
            color: #2c6e49;
            font-weight: 600;
        }

        .btn-outline-secondary:hover {
            background: #2c6e49;
            color: #fff;
        }

        .back-btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            margin-top: 20px;
            text-decoration: none;
            color: #2c6e49;
            font-weight: 600;
        }

        .back-btn:hover {
            color: #1e5036;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>

<body>
    <div class="login-card">
        <h4>Login to HMS</h4>
        <?php if ($error): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <form method="post" novalidate>
            <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
            <div class="mb-3">
                <input type="email" name="email" class="form-control" placeholder="Email" required>
            </div>
            <div class="mb-3">
                <input type="password" name="password" class="form-control" placeholder="Password" required
                    minlength="6">
            </div>
            <button class="btn btn-primary mt-3">Login</button>
        </form>
        <div class="d-flex gap-3 justify-content-center mt-4 flex-wrap">
            <a class="btn btn-outline-secondary w-50" href="<?= BASE_URL ?>/auth/register_patient.php">Patient Sign
                Up</a>
            <a class="btn btn-outline-secondary w-50" href="<?= BASE_URL ?>/auth/register_doctor.php">Doctor Sign Up</a>
        </div>
        <a href="<?= BASE_URL ?>/index.php" class="back-btn">
            <i class="bi bi-arrow-left-circle"></i> Go to Home
        </a>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>