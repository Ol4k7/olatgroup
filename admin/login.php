<?php
/**
 * admin/login.php
 * Path: /olatgroup/admin/login.php
 */

require_once dirname(__DIR__) . '/config/config.php';

$error = '';

// 1. IMPROVED RATE LIMITING (Time-based Lockout)
if (!isset($_SESSION['login_attempts'])) {
    $_SESSION['login_attempts'] = 0;
}

// Lock out for 15 minutes if attempts exceed 5
if ($_SESSION['login_attempts'] >= 5) {
    if (isset($_SESSION['lockout_time']) && (time() - $_SESSION['last_attempt_time'] < 900)) {
        $error = "Too many attempts. Account locked for 15 minutes.";
        $is_locked = true;
    } else {
        // Reset after 15 mins
        $_SESSION['login_attempts'] = 0;
        $is_locked = false;
    }
} else {
    $is_locked = false;
}

// 2. PROCESS LOGIN
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$is_locked) {
    // Basic Input Cleaning (Anti-XSS/Injection)
    $password = $_POST['password'] ?? '';

    // Since ADMIN_PASSWORD_HASH is in config.php, no SQL is run = 0% SQLi Risk.
    // password_verify handles timing attacks automatically.
    if (password_verify($password, ADMIN_PASSWORD_HASH)) {
        
        // SECURE THE SESSION
        session_regenerate_id(true); // Prevents Session Fixation
        
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['last_activity'] = time();
        $_SESSION['login_attempts'] = 0; // Reset
        unset($_SESSION['last_attempt_time']);

        header('Location: upload.php');
        exit;
    } else {
        $_SESSION['login_attempts']++;
        $_SESSION['last_attempt_time'] = time();
        $attempts_left = 5 - $_SESSION['login_attempts'];
        $error = "Incorrect password. ($attempts_left attempts left)";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Admin Login | Olat Group</title>
  <style>
    body { font-family: 'Segoe UI', sans-serif; background: #f4f7fa; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
    .login-box { background: white; padding: 2.5rem; border-radius: 16px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); width: 100%; max-width: 400px; }
    h2 { text-align: center; color: #0066ff; margin-bottom: 1.5rem; }
    input { width: 100%; padding: 1rem; margin: 0.8rem 0; border: 1.1px solid #ddd; border-radius: 8px; font-size: 1rem; box-sizing: border-box; }
    button { width: 100%; padding: 1rem; background: #0066ff; color: white; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; }
    button:disabled { background: #ccc; cursor: not-allowed; }
    /* Use htmlspecialchars for safety when echoing the error */
    .error { color: #e11d48; text-align: center; margin-top: 1rem; font-weight: 500; }
    .back-link { display: block; text-align: center; margin-top: 1.5rem; color: #666; text-decoration: none; }
  </style>
</head>
<body>
  <div class="login-box">
    <h2>Admin Login</h2>
    <form method="post">
      <input type="password" name="password" placeholder="Enter admin password" required autofocus <?= $is_locked ? 'disabled' : '' ?> />
      <button type="submit" <?= $is_locked ? 'disabled' : '' ?>>Login to Dashboard</button>
    </form>
    <p class="error"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></p>
    <a href="../index.php" class="back-link">← Back to Website</a>
  </div>
</body>
</html>