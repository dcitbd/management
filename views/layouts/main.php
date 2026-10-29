<!DOCTYPE html>
<html lang="en" data-theme="<?= \App\Core\View::e($user['theme'] ?? 'dark') ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title><?= \App\Core\View::e($title ?? 'Dream Career IT — AI Business Suite') ?></title>
    <meta name="description" content="Production AI Business Suite for Dream Career IT Bangladesh. Developed by Jainal Abedin.">
    <meta name="theme-color" content="#0b1120">
    <link rel="icon" type="image/svg+xml" href="/assets/images/favicon.svg">
    <link rel="manifest" href="/manifest.json">
    <link rel="stylesheet" href="/assets/css/app.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body class="app-body">
    <!-- Desktop Native Window Titlebar -->
    <?php require __DIR__ . '/../partials/titlebar.php'; ?>

    <div class="app-shell">
        <!-- Sidebar Navigation -->
        <?php require __DIR__ . '/../partials/sidebar.php'; ?>

        <div class="app-main-wrapper">
            <!-- Top App Header -->
            <?php require __DIR__ . '/../partials/header.php'; ?>

            <!-- Main Content Area -->
            <main class="app-content-container">
                <?= $content ?>
            </main>

            <!-- Bottom Status Bar -->
            <footer class="app-footer-bar">
                <div class="footer-left">
                    <span class="status-indicator online"></span>
                    <span>System Operational &bull; MySQL 8.0 &bull; PHP <?= PHP_VERSION ?></span>
                </div>
                <div class="footer-center">
                    <span>Dream Career IT Bangladesh &bull; CEO: <strong>Jainal Abedin</strong> (01581703822)</span>
                </div>
                <div class="footer-right">
                    <a href="https://dreamcartbd.com" target="_blank" rel="noopener">Our Shop: dreamcartbd.com</a>
                    <span class="version-tag">v2.4-enterprise</span>
                </div>
            </footer>
        </div>
    </div>

    <!-- Toast Notification Container -->
    <div id="toast-container" class="toast-container" aria-live="polite"></div>

    <!-- Global Modal Dialog Container -->
    <div id="global-modal" class="modal-backdrop" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-header">
                <h3 id="modal-title" class="modal-title">Notice</h3>
                <button type="button" class="modal-close" onclick="App.closeModal()">&times;</button>
            </div>
            <div id="modal-body" class="modal-body"></div>
            <div id="modal-footer" class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="App.closeModal()">Close</button>
            </div>
        </div>
    </div>

    <script src="/assets/js/app.js"></script>
    <script>
        // Register Service Worker for PWA
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/sw.js').then(reg => {
                    console.log('DCIT PWA ServiceWorker registered with scope:', reg.scope);
                }).catch(err => {
                    console.warn('PWA registration failed:', err);
                });
            });
        }
    </script>
</body>
</html>
