<h2>Forgot Password</h2>

<?php if (!empty($errorMessage)) : ?>
    <p class="errors"><?= htmlspecialchars($errorMessage, ENT_QUOTES, 'UTF-8') ?></p>
<?php endif; ?>

<?php if (!empty($submitted)) : ?>
    <p>If that email address exists, a password-reset link has been generated.</p>
    <?php if (!empty($resetLink)) : ?>
        <p class="development-only">Local development link: <a href="<?= htmlspecialchars($resetLink, ENT_QUOTES, 'UTF-8') ?>">Reset password</a></p>
    <?php endif; ?>
<?php endif; ?>

<form method="post" action="index.php?controller=login&amp;action=forgotSubmit">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken ?? '', ENT_QUOTES, 'UTF-8') ?>">
    <label for="email">Your email address</label>
    <input type="email" id="email" name="email" autocomplete="email" required>
    <input type="submit" value="Send reset link">
</form>
