<?php
/**
 * Auth Module SISE2026 BPS Kabupaten Jember
 * Login, logout, session validation, role checking, CSRF.
 */

require_once __DIR__ . '/../config/config.php';

/**
 * Authenticate user
 */
function auth_login($username, $password) {
    global $pdo;
    if (!$pdo) return false;

    try {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username AND is_active = TRUE");
        $stmt->execute([':username' => $username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password_hash'])) {
            // Regenerate session ID untuk mencegah session fixation
            session_regenerate_id(true);

            $_SESSION['user_id']    = $user['id'];
            $_SESSION['username']   = $user['username'];
            $_SESSION['user_role']  = $user['role'];
            $_SESSION['user_name']  = $user['nama_lengkap'];
            $_SESSION['user_foto']  = $user['foto'];
            $_SESSION['logged_in']  = true;

            // Regenerate CSRF token setelah login
            unset($_SESSION['csrf_token']);
            generate_csrf_token();

            // Update last_login
            $pdo->prepare("UPDATE users SET last_login = NOW() WHERE id = ?")->execute([$user['id']]);

            // Log activity
            log_activity('login', 'auth', 'User logged in');
            return true;
        }
        return false;
    } catch (PDOException $e) {
        return false;
    }
}

/**
 * Logout
 */
function auth_logout() {
    log_activity('logout', 'auth', 'User logged out');
    session_unset();
    session_destroy();
}

/**
 * Check if user is logged in
 */
function is_logged_in() {
    return isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
}

/**
 * Get current user role
 */
function get_user_role() {
    return $_SESSION['user_role'] ?? null;
}

/**
 * Get current user ID
 */
function get_user_id() {
    return $_SESSION['user_id'] ?? null;
}

/**
 * Alias kompatibilitas untuk model lama yang masih memanggil helper lama.
 */
function auth_get_user_id() {
    return get_user_id();
}

/**
 * Get current user display name
 */
function get_user_name() {
    return $_SESSION['user_name'] ?? 'Guest';
}

/**
 * Check if current user has specific role(s)
 */
function has_role($roles) {
    if (!is_logged_in()) return false;
    if (is_string($roles)) $roles = [$roles];
    return in_array(get_user_role(), $roles);
}

/**
 * Require login — redirect to login page if not authenticated
 */
function require_login() {
    if (!is_logged_in()) {
        $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
        header('Location: ?page=login');
        exit;
    }
}

/**
 * Require specific role(s)
 */
function require_role($roles) {
    require_login();
    if (!has_role($roles)) {
        header('Location: ?page=unauthorized');
        exit;
    }
}

/**
 * Generate CSRF token
 */
function generate_csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Validate CSRF token
 */
function validate_csrf($token) {
    if (!isset($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $token)) {
        return false;
    }
    // Rotasi token setelah validasi sukses (per-request token)
    unset($_SESSION['csrf_token']);
    generate_csrf_token();
    return true;
}

/**
 * Get CSRF input field HTML
 */
function csrf_field() {
    return '<input type="hidden" name="csrf_token" value="' . generate_csrf_token() . '">';
}

/**
 * Validate admin session (backward compatibility)
 */
function is_admin_authenticated() {
    return has_role([ROLE_ADMIN]);
}

/**
 * Log user activity
 */
function log_activity($action, $module = '', $detail = '') {
    global $pdo;
    if (!$pdo) return;

    try {
        $stmt = $pdo->prepare("INSERT INTO activity_logs (user_id, action, module, detail, ip_address, user_agent) 
                               VALUES (:uid, :action, :module, :detail, :ip, :ua)");
        $stmt->execute([
            ':uid'    => get_user_id(),
            ':action' => $action,
            ':module' => $module,
            ':detail' => $detail,
            ':ip'     => $_SERVER['REMOTE_ADDR'] ?? '',
            ':ua'     => $_SERVER['HTTP_USER_AGENT'] ?? ''
        ]);
    } catch (PDOException $e) {
        // Silently fail — log should not break the app
    }
}
?>
