<?php
session_start();
if (!empty($_SESSION['user_id'])) { header('Location: index.php'); exit; }
require __DIR__ . '/db.php';
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim(strtolower($_POST['email'] ?? ''));
    $password = $_POST['password'] ?? '';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || $password === '') {
        $error = 'Enter a valid email and password.';
    } else {
        $stmt = $pdo->prepare('SELECT id, full_name, email, password_hash FROM users WHERE email = ? LIMIT 1');
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        if ($user && password_verify($password, $user['password_hash'])) {
            session_regenerate_id(true);
            $_SESSION['user_id'] = (int)$user['id'];
            $_SESSION['user_name'] = $user['full_name'];
            $_SESSION['user_email'] = $user['email'];
            header('Location: index.php'); exit;
        }
        $error = 'Incorrect email or password.';
    }
}
?>
<!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Login | PortfolioG</title><link rel="stylesheet" href="login.css"></head><body>
<main class="auth-wrap"><section class="auth-card"><a class="brand" href="login.php"><span class="brand-mark">P</span> PortfolioG</a><p class="eyebrow">WELCOME BACK</p><h1>Log in to your account</h1><p class="sub">Access your portfolio builder and saved work.</p>
<?php if ($error): ?><div class="message error"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div><?php endif; ?>
<form method="post" action="login.php"><label>Email address<input type="email" name="email" autocomplete="email" required maxlength="180" value="<?= htmlspecialchars($_POST['email'] ?? '', ENT_QUOTES, 'UTF-8') ?>"></label><label>Password<input type="password" name="password" autocomplete="current-password" required></label><button type="submit">Log in <span>→</span></button></form>
<p class="switch">New here? <a href="register.php">Create an account</a></p></section></main></body></html>
