<?php if (session_status() === PHP_SESSION_NONE) session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <title><?= htmlspecialchars($title ?? 'Dashboard') ?></title>

  <!-- CoolAdmin CSS -->
  <link href="public/font-face.css" rel="stylesheet">
  <link href="vendor/fontawesome-7.1.0/css/all.min.css" rel="stylesheet">
  <link href="vendor/bootstrap-5.3.8.min.css" rel="stylesheet">
  <link href="vendor/perfect-scrollbar/perfect-scrollbar-1.5.6.css" rel="stylesheet">
  <link href="css/theme.css" rel="stylesheet">

  <!-- YOUR custom CSS (last) -->
  <link rel="stylesheet" href="public/theme.css">
</head>

<body>
  <div class="page-wrapper">

    <?php require "views/partials/navbar.php"; ?>

    <div class="page-container">
      <div class="main-content">
        <div class="section__content section__content--p30">
          <?= $content ?? '' ?>
        </div>
      </div>
    </div>

  </div>

  <!-- JS (needed for hamburger + sidebar scroll) -->
  <script src="js/vanilla-utils.js"></script>
  <script src="vendor/bootstrap-5.3.8.bundle.min.js"></script>
  <script src="vendor/perfect-scrollbar/perfect-scrollbar-1.5.6.min.js"></script>
  <script src="js/main-vanilla.js"></script>
</body>
</html>
