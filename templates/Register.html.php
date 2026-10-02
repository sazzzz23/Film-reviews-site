<?php if (!empty($errors)) : ?>
<div class="errors">
    <p>Your account could not be created, please check the following:</p>
    <ul>
        <?php foreach ($errors as $error) : ?>
            <li><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></li>
        <?php endforeach; ?>
    </ul>
</div>
<?php endif; ?>

<form class="registration-form" action="index.php?controller=reviewer&amp;action=registrationFormSubmit" method="post">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken ?? '', ENT_QUOTES, 'UTF-8') ?>">

    <div class="registration-field">
        <label for="name">Your name</label>
        <input class="registration-input" name="reviewer[name]" id="name" type="text" value="<?= htmlspecialchars($reviewer['name'] ?? '', ENT_QUOTES, 'UTF-8') ?>" autocomplete="name" maxlength="255" required>
    </div>

    <div class="registration-field">
        <label for="email">Your email address</label>
        <input class="registration-input" name="reviewer[email]" id="email" type="email" value="<?= htmlspecialchars($reviewer['email'] ?? '', ENT_QUOTES, 'UTF-8') ?>" autocomplete="email" maxlength="255" required>
    </div>

    <div class="registration-field">
        <label for="password">Password</label>
        <input class="registration-input" name="reviewer[password]" id="password" type="password" autocomplete="new-password" minlength="12" required>
    </div>

    <input class="registration-submit" type="submit" name="submit" value="Register account">
</form>
