<div class="login-box">
    <div class="auth-header">
        <h2>Enterprise Login</h2>
        <p>Access the unified business control suite</p>
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

    <form method="POST" action="/login" class="auth-form" id="loginForm">
        <?= \App\Core\Csrf::field() ?>

        <div class="form-group">
            <label for="email">Work Email</label>
            <div class="input-icon-wrapper">
                <i class="bi bi-envelope-fill"></i>
                <input type="email" id="email" name="email" class="form-control" placeholder="name@dreamcareerit.com" required autofocus autocomplete="email">
            </div>
        </div>

        <div class="form-group">
            <div class="label-row">
                <label for="password">Password</label>
                <a href="/forgot-password" class="forgot-link">Forgot password?</a>
            </div>
            <div class="input-icon-wrapper">
                <i class="bi bi-lock-fill"></i>
                <input type="password" id="password" name="password" class="form-control" placeholder="••••••••••••" required autocomplete="current-password">
                <button type="button" class="btn-toggle-pw" onclick="App.togglePasswordVisibility('password')">
                    <i class="bi bi-eye-fill"></i>
                </button>
            </div>
        </div>

        <div class="form-check-row">
            <label class="custom-checkbox">
                <input type="checkbox" name="remember" value="1">
                <span class="checkmark"></span>
                <span class="label-text">Remember device for 30 days</span>
            </label>
        </div>

        <button type="submit" class="btn btn-primary btn-block btn-lg" id="loginSubmitBtn">
            <span>Sign In to Instrument Panel</span>
            <i class="bi bi-arrow-right-short"></i>
        </button>
    </form>
</div>
