<p><?= (int) $totalFilms ?> film reviews have been submitted.</p>
<?php
foreach($films as $film): ?>
        <blockquote>
        <?=htmlspecialchars($film['title'], ENT_QUOTES,'UTF-8')?><br />
        <?=htmlspecialchars($film['review'], ENT_QUOTES,'UTF-8')?>

        (by <a href="mailto:<?=htmlspecialchars($film['email'], ENT_QUOTES, 'UTF-8' );?>">
        <?=htmlspecialchars($film['name'], ENT_QUOTES, 'UTF-8'); ?></a>)
        <?php if (!empty($film['reviewdate'])): ?>
        on <?=htmlspecialchars($film['reviewdate'], ENT_QUOTES, 'UTF-8'); ?>
        <?php endif; ?>
        <?php
            $rating = (int)($film['rating'] ?? 0);
            if ($rating < 1 || $rating > 5) {
                $rating = 0;
            }
        ?>
        <?php if ($rating > 0): ?>
            <span class="stars">
                <?php for ($i = 1; $i <= 5; $i++): ?>
                    <?php if ($i <= $rating): ?>
                        <span class="star filled">★</span>
                    <?php else: ?>
                        <span class="star">☆</span>
                    <?php endif; ?>
                <?php endfor; ?>
            </span>
        <?php endif; ?>

<?php if (!empty($film) && $userId == $film['reviewerId']): ?>
        <a href="index.php?controller=film&amp;action=edit&amp;id=<?= (int) $film['id'] ?>">Edit</a>

        <form action="index.php?controller=film&amp;action=delete" method="post">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken ?? '', ENT_QUOTES, 'UTF-8') ?>">
                <input type="hidden" name="id" value="<?= (int) $film['id'] ?>">
                <input type="submit" value="Delete">
        </form>
<?php endif; ?>
        </blockquote>
<?php endforeach;?>

<?php if (!empty($totalPages) && $totalPages > 1): ?>
<div class="pagination w3-bar">
<?php if ($page > 1): ?>
    <a class="page-prev w3-bar-item w3-left" href="index.php?controller=film&amp;action=list&amp;page=<?= (int) $page - 1 ?>">Previous</a>
<?php endif; ?>
<?php if ($page < $totalPages): ?>
    <a class="page-next w3-bar-item w3-right" href="index.php?controller=film&amp;action=list&amp;page=<?= (int) $page + 1 ?>">Next</a>
<?php endif; ?>
</div>
<?php endif; ?>
