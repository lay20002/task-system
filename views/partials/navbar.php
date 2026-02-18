<?php
if (session_status() === PHP_SESSION_NONE) session_start();

$current = $_GET['c'] ?? 'dashboard';
$role = $_SESSION['user']['role'] ?? 'user';
$isAdmin = ($role === 'admin');

$username = $_SESSION['user']['username'] ?? 'User';
$initial  = strtoupper(substr($username, 0, 1));
?>

<!-- HEADER MOBILE-->
<header class="header-mobile d-block d-lg-none">
  <div class="header-mobile__bar">
    <div class="container-fluid">
      <div class="header-mobile-inner">
        <a class="logo" href="index.php?c=dashboard&a=index">
          <img src="public/logo.png" alt="ANXIN-NETWORK" style="height:32px;">
        </a>

        <button class="hamburger hamburger--slider js-hamburger" type="button">
          <span class="hamburger-box"><span class="hamburger-inner"></span></span>
        </button>
      </div>
    </div>
  </div>

  <nav class="navbar-mobile">
    <div class="container-fluid">
      <ul class="navbar-mobile__list list-unstyled">
        <li class="<?= $current === 'dashboard' ? 'active' : '' ?>">
          <a href="index.php?c=dashboard&a=index"><i class="fas fa-tachometer-alt"></i>Dashboard</a>
        </li>

        <li class="<?= $current === 'task' ? 'active' : '' ?>">
          <a href="index.php?c=task&a=list"><i class="fas fa-list-check"></i>Tasks</a>
        </li>

        <?php if ($isAdmin): ?>
          <li class="<?= $current === 'user' ? 'active' : '' ?>">
            <a href="index.php?c=user&a=list"><i class="fas fa-users"></i>Users</a>
          </li>
        <?php endif; ?>

        <li>
          <a href="index.php?c=auth&a=logout"><i class="fas fa-power-off"></i>Logout</a>
        </li>
      </ul>
    </div>
  </nav>
</header>
<!-- END HEADER MOBILE-->

<!-- MENU SIDEBAR-->
<aside class="menu-sidebar d-none d-lg-block">
  <div class="menu-sidebar__content js-scrollbar1">
    <nav class="navbar-sidebar">
      <ul class="list-unstyled navbar__list">
        <li class="<?= $current === 'dashboard' ? 'active' : '' ?>">
          <a href="index.php?c=dashboard&a=index"><i class="fas fa-tachometer-alt"></i>Dashboard</a>
        </li>

        <li class="<?= $current === 'task' ? 'active' : '' ?>">
          <a href="index.php?c=task&a=list"><i class="fas fa-list-check"></i>Tasks</a>
        </li>

        <?php if ($isAdmin): ?>
          <li class="<?= $current === 'user' ? 'active' : '' ?>">
            <a href="index.php?c=user&a=list"><i class="fas fa-users"></i>Users</a>
          </li>
        <?php endif; ?>

        <li>
          <a href="index.php?c=auth&a=logout"><i class="fas fa-power-off"></i>Logout</a>
        </li>
      </ul>
    </nav>
  </div>
</aside>
<!-- END MENU SIDEBAR-->
