<?php //include __DIR__ . '/includes/header.php'; ?>

<style>
    body,
    html {
        height: 100%;
        margin: 0;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    .content-wrapper {
        margin-top: 0 !important;
        padding-top: 0 !important;
    }

    .home-bg {
        background: url('b.jpg') no-repeat center center;
        background-size: cover;
        min-height: calc(100vh - 0px);
        display: flex;
        justify-content: center;
        align-items: center;
        position: relative;
        color: #fff;
    }


    .overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(to bottom right, rgba(144, 238, 144, 0.4), rgba(204, 255, 204, 0.4));
        border-radius: 0;
    }

    .home-content {
        position: relative;
        z-index: 2;
        text-align: center;
        max-width: 700px;
        padding: 2.5rem;
        border-radius: 15px;
        background: rgba(255, 255, 255, 0.85);
        color: #2c6e49;
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
    }

    .home-content h1 {
        font-size: 3rem;
        font-weight: 700;
        margin-bottom: 1rem;
        color: #2c6e49;
    }

    .home-content p {
        font-size: 1.25rem;
        margin-bottom: 2rem;
        line-height: 1.6;
        color: #2c6e49;
    }

    .home-content .btn {
        font-size: 1.2rem;
        padding: 0.75rem 2rem;
        border-radius: 50px;
        transition: all 0.3s ease;
        font-weight: 500;
    }

    .home-content .btn-primary {
        background: #4CAF50;
        border: none;
        color: #fff;
    }

    .home-content .btn-primary:hover {
        background: #388E3C;
        transform: scale(1.05);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
    }

    .home-content .btn-outline-primary {
        color: #4CAF50;
        border: 2px solid #4CAF50;
        background: transparent;
    }

    .home-content .btn-outline-primary:hover {
        background: #4CAF50;
        color: #fff;
        transform: scale(1.05);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
    }

    .home-content .btn {
        text-decoration: none !important;
    }


    @media(max-width:768px) {
        .home-content h1 {
            font-size: 2.2rem;
        }

        .home-content p {
            font-size: 1.1rem;
        }
    }
</style>

<div class="home-bg">
    <div class="overlay"></div>
    <div class="home-content">
        <h1>Welcome to Our Hospital System</h1>
        <p>Book appointments easily, consult certified doctors, and manage your health records in one place. Simplifying
            healthcare for you and your family.</p>
        <a class="btn btn-primary" href="auth/login.php">Login</a>
        <!--<a class="btn btn-outline-primary ms-3" href="auth/register_patient.php">Sign Up</a>-->
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>