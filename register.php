<?php
session_start();
if (!empty($_SESSION['user_id'])) { header('Location: index.php'); exit; }
require __DIR__ . '/db.php';
$error = ''; $name = ''; $email = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['full_name'] ?? '');
    $email = trim(strtolower($_POST['email'] ?? ''));
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';
    if ($name === '' || mb_strlen($name) > 120 || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Enter your name and a valid email address.';
    } elseif (strlen($password) < 8) {
        $error = 'Your password must be at least 8 characters.';
    } elseif ($password !== $confirm) {
        $error = 'The passwords do not match.';
    } else {
        try {
            $stmt = $pdo->prepare('INSERT INTO users (full_name, email, password_hash) VALUES (?, ?, ?)');
            $stmt->execute([$name, $email, password_hash($password, PASSWORD_DEFAULT)]);
            session_regenerate_id(true);
            $_SESSION['user_id'] = (int)$pdo->lastInsertId();
            $_SESSION['user_name'] = $name;
            $_SESSION['user_email'] = $email;
            header('Location: index.php'); exit;
        } catch (PDOException $e) {
            if ($e->getCode() === '23000') $error = 'That email is already registered. Please log in.';
            else $error = 'Could not create the account. Check your database setup and try again.';
        }
    }
}
?>
<!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Create account | PortfolioG</title><link rel="stylesheet" href="login.css"></head><body>
<main class="auth-wrap"><section class="auth-card"><a class="brand" href="login.php"><span class="brand-mark">P</span> PortfolioG</a><p class="eyebrow">GET STARTED</p><h1>Create your account</h1><p class="sub">Sign up to open your portfolio workspace.</p>
<?php if ($error): ?><div class="message error"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div><?php endif; ?>
<form method="post" action="register.php"><label>Full name<input type="text" name="full_name" required maxlength="120" autocomplete="name" value="<?= htmlspecialchars($name, ENT_QUOTES, 'UTF-8') ?>"></label><label>Email address<input type="email" name="email" required maxlength="180" autocomplete="email" value="<?= htmlspecialchars($email, ENT_QUOTES, 'UTF-8') ?>"></label><label>Password <small>(at least 8 characters)</small><input type="password" name="password" required minlength="8" autocomplete="new-password"></label><label>Confirm password<input type="password" name="confirm_password" required minlength="8" autocomplete="new-password"></label><button type="submit">Create account <span>→</span></button></form>
<p class="switch">Already registered? <a href="login.php">Log in</a></p></section></main></body></html>
