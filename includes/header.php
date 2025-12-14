<?php
if (session_status() === PHP_SESSION_NONE)
    session_start();
require_once __DIR__ . '/../includes/config.php';
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>HMS</title>
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css">
    <style>
        .navbar-custom {
            background: linear-gradient(90deg, #c0f0c0, #88d498);
        }

        .navbar-brand {
            font-size: 2rem;
            font-weight: bold;
            color: #2c6e49 !important;
        }

        .nav-link {
            font-size: 1.1rem;
            color: #2c6e49 !important;
        }

        .navbar-text {
            font-size: 1rem;
            font-weight: 500;
        }

        .nav-link.btn-logout {
            border: 1px solid #2c6e49;
            color: #2c6e49 !important;
            border-radius: 5px;
            padding: 5px 10px;
        }

        .nav-link.btn-logout:hover {
            background-color: #2c6e49;
            color: #ffffff !important;
        }

        .navbar-nav.d-flex.align-items-center .navbar-text {
            margin-right: 15px;
        }
    </style>
</head>

<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-custom">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="<?= BASE_URL ?>/index.php">
                <img src="<?= BASE_URL ?>/assets/images/log.png" alt="Logo" style="height:40px; margin-right:0px;">
            </a>

            <a class="navbar-brand" href="<?= BASE_URL ?>/index.php">HMS</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#nav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="nav">
                <ul class="navbar-nav me-auto">
                    <?php if (!empty($_SESSION['user'])): ?>
                        <?php if ($_SESSION['user']['role'] === 'patient'): ?>
                            <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>/patient/dashboard.php">Dashboard</a>
                            </li>
                            <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>/patient/book.php">Book</a></li>
                            <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>/patient/my_appointments.php">My
                                    Appointments</a></li>
                            <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>/patient/profile.php">Profile</a></li>
                        <?php elseif ($_SESSION['user']['role'] === 'doctor'): ?>
                            <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>/doctor/dashboard.php">Dashboard</a>
                            </li>
                            <li class="nav-item"><a class="nav-link"
                                    href="<?= BASE_URL ?>/doctor/appointments.php">Appointments</a></li>
                            <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>/doctor/patients.php">Patients</a>
                            </li>
                            <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>/doctor/profile.php">Profile</a></li>
                        <?php elseif ($_SESSION['user']['role'] === 'admin'): ?>
                            <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>/admin/dashboard.php">Dashboard</a>
                            </li>
                            <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>/admin/users.php">Users</a></li>
                            <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>/admin/approve_doctors.php">Approve
                                    Doctors</a></li>
                        <?php endif; ?>
                    <?php endif; ?>
                </ul>

                <ul class="navbar-nav ms-auto d-flex align-items-center">
                    <?php if (empty($_SESSION['user'])): ?>
                        <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>/auth/login.php">Login</a></li>
                    <?php else: ?>
                        <li class="nav-item">
                            <span class="navbar-text">
                                <?= htmlspecialchars($_SESSION['user']['full_name']) ?> (<?= $_SESSION['user']['role'] ?>)
                            </span>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link btn-logout" href="<?= BASE_URL ?>/auth/logout.php">Logout</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>




    <div class="container py-4">
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>