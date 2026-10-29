<div class="login-box">
    <div class="auth-header">
        <h2>Reset Credentials</h2>
        <p>Enter your verified business email address to receive password reset instructions.</p>
    </div>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger" role="alert">
            <i class="bi bi-exclamation-triangle-fill"></i>
            <span><?= \App\Core\View::e($error) ?></span>
        </div>
    <?php endif; ?>

    <?php if (!empty($success)): ?>
        <div class="alert alert-success" role="alert">
            <i class="bi bi-check-circle-fill"></i>
            <span><?= \App\Core\View::e($success) ?></span>
        </div>
    <?php endif; ?>

    <form method="POST" action="/forgot-password" class="auth-form">
        <?= \App\Core\Csrf::field() ?>

        <div class="form-group">
            <label for="email">Account Email</label>
            <div class="input-icon-wrapper">
                <i class="bi bi-envelope-fill"></i>
                <input type="email" id="email" name="email" class="form-control" placeholder="jainal.dcitbd@gmail.com" required autofocus>
            </div>
        </div>

        <button type="submit" class="btn btn-primary btn-block btn-lg">
            <span>Send Reset Instructions</span>
            <i class="bi bi-send-fill"></i>
        </button>

        <div class="auth-back-link">
            <a href="/login"><i class="bi bi-arrow-left"></i> Return to login screen</a>
        </div>
    </form>
</div>
