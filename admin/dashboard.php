<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/auth.php';
require_role('admin');
include __DIR__ . '/../includes/header.php';
?>

<style>
    body.admin-dashboard {
        background: #f0f0f0;

        min-height: 100vh;
        font-family: Arial, sans-serif;
        padding: 20px;
    }


    .dashboard-container {
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 100vh;
    }


    .dashboard-card {
        background: url('../assets/images/a.jpg') no-repeat center center;
        background-size: cover;
        padding: 50px 30px;
        border-radius: 20px;
        max-width: 800px;
        width: 100%;
        text-align: center;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
        color: #fff;
        position: relative;
    }


    .dashboard-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.4);

        border-radius: 20px;
        z-index: 0;
    }


    .dashboard-card .content {
        position: relative;
        z-index: 1;
    }

    .dashboard-card h1 {
        font-size: 3rem;
        margin-bottom: 20px;
    }

    .dashboard-card p.description {
        font-size: 1.2rem;
        margin-bottom: 30px;
    }


    .dashboard-card .btn {
        display: block;
        width: 100%;
        max-width: 250px;
        margin: 10px auto;
        padding: 15px 0;
        font-size: 1.1rem;
        font-weight: 600;
        color: #fff;
        background: #2c6e49;
        border: none;
        border-radius: 12px;
        text-decoration: none;
        transition: all 0.3s ease;
        z-index: 1;
        position: relative;
    }

    .dashboard-card .btn:hover {
        background: #4caf50;
        transform: translateY(-3px);
        box-shadow: 0 6px 15px rgba(0, 0, 0, 0.2);
    }

    @media(max-width:768px) {
        .dashboard-card {
            padding: 40px 20px;
        }

        .dashboard-card h1 {
            font-size: 2.2rem;
        }

        .dashboard-card p.description {
            font-size: 1rem;
        }
    }
</style>

<div class="dashboard-container">
    <div class="dashboard-card">
        <div class="content">
            <h1>Welcome, Admin!</h1>
            <p class="description">
                This is your Admin Dashboard. From here, you can manage users, approve doctors, and oversee the system
                efficiently.
            </p>
            <a href="users.php" class="btn">Manage Users</a>
            <a href="approve_doctors.php" class="btn">Approve Doctors</a>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>