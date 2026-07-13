<?php

session_start();

if (isset($_SESSION['id_user'])) {
    header("Location: dashboard.php");
    exit;
}

$error = $_SESSION['login_error'] ?? null;
unset($_SESSION['login_error']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Dough & Co Hub</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

    <div class="login-wrapper">
        <div class="page-label">Login</div>

        <div class="login-card">
            <div class="logo-pill"></div>
            <div class="brand-title">Dough & Co HUB</div>

            <?php if ($error): ?>
                <div class="alert-error"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <form class="login-form" action="proses_login.php" method="POST" id="formLogin">
                <div class="input-group-custom">
                    <i class="bi bi-person-fill"></i>
                    <input type="text" name="username" id="username"
                           class="form-control-custom" placeholder="Username" required autocomplete="username">
                </div>

                <div class="input-group-custom">
                    <i class="bi bi-lock-fill"></i>
                    <input type="password" name="password" id="password"
                           class="form-control-custom" placeholder="Password" required autocomplete="current-password">
                </div>

                <button type="submit" class="btn-login">Login</button>
            </form>
        </div>
    </div>

    <script src="assets/js/validasi.js"></script>
</body>
</html>
