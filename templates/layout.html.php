<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= htmlspecialchars($title ?? 'Film Reviews', ENT_QUOTES, 'UTF-8') ?></title>
<link rel="stylesheet" href="https://www.w3schools.com/w3css/5/w3.css">
<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Poppins">
<link rel="stylesheet" href="film.css">
</head>
<body>
<nav class="w3-sidebar w3-red w3-collapse w3-top w3-large w3-padding" style="z-index:3;width:300px;font-weight:bold" id="mySidebar">
  <div class="w3-container"><h1 class="w3-padding-64">Film<br>Reviews</h1></div>
  <div class="w3-bar-block">
    <a href="index.php" class="w3-bar-item w3-button w3-hover-white">Home</a>
    <a href="index.php?controller=film&amp;action=list" class="w3-bar-item w3-button w3-hover-white">Review list</a>
    <?php if ($isLoggedIn) : ?>
      <a href="index.php?controller=film&amp;action=edit" class="w3-bar-item w3-button w3-hover-white">Add a review</a>
      <form method="post" action="index.php?controller=login&amp;action=logout" class="nav-form">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken ?? '', ENT_QUOTES, 'UTF-8') ?>">
        <button type="submit" class="w3-bar-item w3-button w3-hover-white">Log out</button>
      </form>
    <?php else : ?>
      <a href="index.php?controller=reviewer&amp;action=registrationForm" class="w3-bar-item w3-button w3-hover-white">Register</a>
      <a href="index.php?controller=login&amp;action=login" class="w3-bar-item w3-button w3-hover-white">Log in</a>
    <?php endif; ?>
  </div>
</nav>
<main class="w3-main" style="margin-left:340px;margin-right:40px">
  <header class="w3-container" style="margin-top:80px"><h1>Internet film reviews</h1></header>
  <div class="w3-container" style="margin-top:75px"><?= $output ?></div>
</main>
<footer class="w3-light-grey w3-container w3-padding-32" style="margin-top:75px;padding-right:58px">
  <p class="w3-right">Styled with <a href="https://www.w3schools.com/w3css/default.asp" rel="noopener noreferrer" target="_blank">W3.CSS</a>.</p>
</footer>
</body>
</html>
