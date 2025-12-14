<?php
if (session_status() === PHP_SESSION_NONE)
    session_start();
require_once __DIR__ . '/db.php';

function require_login()
{
    if (empty($_SESSION['user'])) {
        header('Location: ' . BASE_URL . '/auth/login.php');
        exit;
    }
}

function require_role($role)
{
    require_login();
    if ($_SESSION['user']['role'] !== $role) {
        http_response_code(403);
        echo "Forbidden";
        exit;
    }
}

function require_any_role($roles)
{
    require_login();
    if (!in_array($_SESSION['user']['role'], $roles, true)) {
        http_response_code(403);
        echo "Forbidden";
        exit;
    }
}
