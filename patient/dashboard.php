<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/auth.php';
require_any_role(['patient']);
include __DIR__ . '/../includes/header.php';
?>

<body class="patient-dashboard">

    <style>
        body.patient-dashboard {
            background: #f5f5f5;
            min-height: 100vh;
            position: relative;
        }

        .container {
            position: relative;
            z-index: 1;
        }

        .dashboard-card {
            position: relative;
            overflow: hidden;
            transition: transform 0.3s;
            background: url('<?= BASE_URL ?>/assets/images/p.jpg') no-repeat center center;
            background-size: cover;
            color: #fff;
        }

        .dashboard-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.4);
            z-index: 0;
            border-radius: 12px;
        }

        .dashboard-card>* {
            position: relative;
            z-index: 1;
        }

        .dashboard-card:hover {
            transform: translateY(-3px);
        }

        .btn-dashboard {
            display: block;
            width: 100%;
            max-width: 350px;
            margin: 0 auto;
            padding: 15px 30px;
            font-size: 1.1rem;
            font-weight: 600;
            color: #2c6e49;
            background: #a8e6cf;
            border-radius: 12px;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .btn-dashboard:hover {
            background: #2c6e49;
            color: #fff;
            transform: scale(1.05);
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.2);
        }
    </style>

    <div class="container my-5">
        <div class="dashboard-card text-center mx-auto shadow p-5 rounded" style="max-width: 800px;">
            <h2 class="mb-3" style="font-weight:700;">Welcome,
                <?= htmlspecialchars($_SESSION['user']['full_name']) ?>!
            </h2>
            <p class="mb-4" style="font-size:1.1rem;">
                Manage your appointments quickly, book new consultations, and update your profile easily.
            </p>
            <div class="d-flex flex-column align-items-center gap-3">
                <a href="book.php" class="btn-dashboard">Book Appointment</a>
                <a href="my_appointments.php" class="btn-dashboard">My Appointments</a>
                <a href="profile.php" class="btn-dashboard">Edit Profile</a>
            </div>
        </div>
    </div>

    <?php include __DIR__ . '/../includes/footer.php'; ?>