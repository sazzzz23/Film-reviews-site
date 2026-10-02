<?php
$id = '';
if (isset($_GET['id'])) {
    $id = '&id=' . urlencode($_GET['id']);
}
header('Location: index.php?controller=film&action=edit' . $id);
exit;
