<?php
if (isset($errorMessage)) :
    if ($errorMessage === 'banned') {
        echo '<div class="errors">Your account has been banned.</div>';
    }
    else {
        echo '<div class="errors">Sorry, your username and password could not be found.</div>';
    }
endif;
?>

<form method="post" action="index.php?controller=login&amp;action=loginSubmit">
<input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken ?? '', ENT_QUOTES, 'UTF-8') ?>">
<label for="email">Your email address</label>
<input type="email" id="email" name="email" value="<?= htmlspecialchars($email ?? '', ENT_QUOTES, 'UTF-8') ?>" autocomplete="email" required>
<label for="password">Password</label>
<input type="password" id="password" name="password" autocomplete="current-password" required>
<input type="submit" value="Log in">
</form>
<p><a href="index.php?controller=login&action=forgotForm">Forgot password?</a></p>
