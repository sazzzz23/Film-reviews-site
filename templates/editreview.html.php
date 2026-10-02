<div class="w3-container" id="contact" style="margin-top:75px">
    <h1 class="w3-xxxlarge w3-text-red"><b><?= empty($film) ? 'Leave a new film review below' : 'Edit your film review' ?></b></h1>
<hr style="width:50px;border:5px solid red" class="w3-round">
<?php if (!empty($errors)) : ?>
    <div class="errors"><ul><?php foreach ($errors as $error) : ?><li><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></li><?php endforeach; ?></ul></div>
<?php endif; ?>
<?php if (empty($film) || $userId == ($film['reviewer_id'] ?? null)): ?>
<form action="" method="post">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken ?? '', ENT_QUOTES, 'UTF-8') ?>">
    <input type="hidden" name="film[id]" value="<?= htmlspecialchars((string) ($film['id'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
<div class="w3-section">
        <label>Film Title</label>
        <input class="w3-input w3-border" type="text" name="film[title]" value="<?= htmlspecialchars($film['title'] ?? '', ENT_QUOTES, 'UTF-8') ?>" maxlength="255" required>
      </div>

    <label>Type your review here</label>
    <textarea class="w3-input w3-border" name="film[review]" rows="3" cols="40" maxlength="10000" required><?= htmlspecialchars($film['review'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
    <label>Rating (1–5)</label>
    <select class="w3-select w3-border" name="film[rating]" required>
        <option value="">Select rating</option>
        <?php for ($i = 1; $i <= 5; $i++): ?>
            <option value="<?=$i?>" <?=((int)($film['rating'] ?? 0) === $i) ? 'selected' : '' ?>><?=$i?></option>
        <?php endfor; ?>
    </select>
     <button type="submit" class="w3-button w3-block w3-padding-large w3-red w3-margin-bottom">Save</button>
</form>
<?php else: ?>
    <p>You may only edit reviews that you posted.</p>
<?php endif; ?>
</div>
