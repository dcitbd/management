<!DOCTYPE html>
<html lang="en" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= \App\Core\View::e($title ?? 'Sign In — Dream Career IT') ?></title>
    <meta name="theme-color" content="#0b1120">
    <link rel="icon" type="image/svg+xml" href="/assets/images/favicon.svg">
    <link rel="stylesheet" href="/assets/css/auth.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body class="auth-body">
    <div class="auth-container">
        <div class="auth-branding">
            <img src="/assets/images/logo.svg" alt="Dream Career IT" class="auth-logo">
            <div class="badge-ceo">Designed & Directed by Jainal Abedin (CEO)</div>
        </div>
        <div class="auth-card">
            <?= $content ?>
        </div>
        <div class="auth-footer">
            <p>&copy; <?= date('Y') ?> Dream Career IT Bangladesh. All rights reserved.</p>
            <p>Support: 01581703822 &bull; 01818273838 &bull; <a href="https://dreamcartbd.com" target="_blank" rel="noopener">dreamcartbd.com</a></p>
        </div>
    </div>
</body>
</html>
