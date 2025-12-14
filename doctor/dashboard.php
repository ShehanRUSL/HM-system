<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/auth.php';
require_any_role(['doctor']);
include __DIR__ . '/../includes/header.php';
?>

<style>
    .dashboard-wrapper {
        padding: 100px 15px 50px;
        display: flex;
        justify-content: center;
        align-items: flex-start;
        min-height: calc(100vh - 120px);
        background: #f5f5f5;

    }


    .dashboard-container {
        position: relative;
        text-align: center;
        background: url('../assets/images/d.jpg') no-repeat center center;
        background-size: cover;
        padding: 30px 20px;
        border-radius: 20px;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
        max-width: 500px;
        width: 100%;
        color: #fff;
        overflow: hidden;
    }


    .dashboard-container::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.4);
        z-index: 0;
        border-radius: 20px;
    }


    .dashboard-container .content {
        position: relative;
        z-index: 1;
    }

    .dashboard-container h3 {
        margin-bottom: 15px;
        font-size: 1.8rem;
    }

    .dashboard-container p {
        font-size: 1.1rem;
        margin-bottom: 30px;
    }

    .dashboard-container .btn {
        width: 100%;
        padding: 15px;
        border-radius: 12px;
        font-size: 1.1rem;
        font-weight: 600;
        transition: 0.3s;
        margin-bottom: 10px;
        border: none;
        color: #fff;
        z-index: 1;
        position: relative;
    }

    .btn-appointments {
        background: #4caf50;
    }

    .btn-patients {
        background: #43a047;
    }

    .btn-profile {
        background: #388e3c;
    }

    .dashboard-container .btn:hover {
        transform: scale(1);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
    }

    @media(max-width:768px) {
        .dashboard-container {
            padding: 40px 20px;
        }
    }
</style>

<div class="dashboard-wrapper">
    <div class="dashboard-container">
        <div class="content">
            <h3>Doctor Dashboard</h3>
            <p>Manage your appointments, patients, and profile efficiently from one place.</p>

            <a href="appointments.php" class="btn btn-appointments">Today / Upcoming Appointments</a>
            <a href="patients.php" class="btn btn-patients">My Patients</a>
            <a href="profile.php" class="btn btn-profile">Edit Profile</a>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>