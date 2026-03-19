<?php
namespace App\Controllers;

class AuthController {
    public static function handleLogin() {
        $csrf_token = $_POST['csrf_token'] ?? '';
        if (!validate_csrf($csrf_token)) {
            set_flash('error', 'Sesi tidak valid atau telah kedaluwarsa. Silakan coba lagi.');
            header('Location: ?page=login');
            exit;
        }

        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';

        if (auth_login($username, $password)) {
            $redirect = $_SESSION['redirect_after_login'] ?? '?page=beranda';
            unset($_SESSION['redirect_after_login']);
            header('Location: ' . $redirect);
            exit;
        } else {
            set_flash('error', 'Username atau password salah.');
            header('Location: ?page=login');
            exit;
        }
    }

    public static function handleLogout() {
        auth_logout();
        header('Location: ?page=beranda');
        exit;
    }
}
