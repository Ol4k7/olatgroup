<?php
// ===== SESSION SECURITY =====
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 1);      // requires https
ini_set('session.use_strict_mode', 1);
session_start();

require_once '../../config.php';

$error = '';

// ===== RATE LIMITING =====
if (!isset($_SESSION['attempts'])) {
    $_SESSION['attempts'] = 0;
}

if ($_SESSION['attempts'] >= 5) {
    $error = "Too many attempts. Try again later.";
} else if ($_POST['password'] ?? '') {

    if (password_verify($_POST['password'], ADMIN_PASSWORD_HASH)) {

        // Regenerate session to prevent fixation
        session_regenerate_id(true);

        $_SESSION['admin'] = true;
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['last_activity'] = time();
        $_SESSION['attempts'] = 0; // reset attempts

        header('Location: upload.php');
        exit;

    } else {
        $_SESSION['attempts']++;
        $error = "Incorrect password.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Admin Login</title>
  <style>
    body { font-family: 'Segoe UI', sans-serif; background: #f4f7fa; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
    .login-box { background: white; padding: 2.5rem; border-radius: 16px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); width: 100%; max-width: 400px; }
    h2 { text-align: center; color: #0066ff; margin-bottom: 1.5rem; font-size: 1.8rem; }
    input { width: 92%; padding: 1rem; margin: 0.8rem 0; border: 1.1px solid #ddd; border-radius: 8px; font-size: 1rem; }
    input:focus { outline: none; border-color: #0066ff; box-shadow: 0 0 0 3px rgba(0,102,255,0.1); }
    button { width: 100%; padding: 1rem; background: #0066ff; color: white; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; }
    button:hover { background: #0052cc; }
    .error { color: #e11d48; text-align: center; margin-top: 1rem; font-weight: 500; }
  </style>
</head>
<body>
  <div class="login-box">
    <h2>Admin Login</h2>
    <form method="post">
      <input type="password" name="password" placeholder="Enter password" required />
      <button type="submit">Login</button>
    </form>
    <p class="error"><?php echo $error; ?></p>
  </div>
</body>
</html>