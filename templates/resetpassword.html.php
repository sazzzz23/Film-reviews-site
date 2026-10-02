<h2>Reset Password</h2>

<?php if (!empty($success)) : ?>
    <p>Your password has been updated. You can now log in.</p>
<?php elseif (empty($isValid)) : ?>
    <p>That reset link is invalid or has expired.</p>
<?php else : ?>
    <?php if (!empty($errorMessage)) : ?><p class="errors"><?= htmlspecialchars($errorMessage, ENT_QUOTES, 'UTF-8') ?></p><?php endif; ?>
    <form method="post" action="index.php?controller=login&amp;action=resetSubmit">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken ?? '', ENT_QUOTES, 'UTF-8') ?>">
        <input type="hidden" name="token" value="<?= htmlspecialchars($token ?? '', ENT_QUOTES, 'UTF-8') ?>">
        <label for="password">New password</label>
        <input type="password" id="password" name="password" autocomplete="new-password" minlength="12" required>
        <input type="submit" value="Reset password">
    </form>
<?php endif; ?>
