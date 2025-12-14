<?php if (basename($_SERVER['PHP_SELF']) !== 'index.php'): ?>
    </div>


    <footer class="footer-custom mt-auto py-3">
        <div class="container text-center">
            <p class="mb-1">&copy; <?= date('Y') ?> HMS. All Rights Reserved.</p>
            <p class="mb-0">
                <b>Email</b> : <a href="mailto:hms@email.com" class="footer-link">hms@email.com</a> |
                <b>Phone</b> : +94 71 123 4567 |
                <b>Address</b> : Anuradapura road, Mihintale
            </p>
        </div>
    </footer>

    <style>
        html,
        body {
            height: 100%;
            margin: 0;
        }

        body {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        .content-wrapper {
            flex: 1;

        }


        .footer-custom {
            background: linear-gradient(90deg, #c0f0c0, #88d498);
            color: #2c6e49;
        }

        .footer-custom p {
            margin: 0;
            font-size: 0.95rem;
            font-weight: 500;
        }

        .footer-link {
            color: #2c6e49;
            text-decoration: none;
        }

        .footer-link:hover {
            text-decoration: underline;
        }
    </style>
<?php endif; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?= BASE_URL ?>/assets/js/app.js"></script>
</body>

</html>