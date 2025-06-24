<?php
if (!isset($authController)) {
  $authController = null;
}

$currentUser = $authController ? $authController->getCurrentUser() : null;
$isLoggedIn = $currentUser !== null;

?>

<header class="<?= $isLoggedIn ? 'main-header' : 'auth-header'; ?>">
  <div class="<?= $isLoggedIn ? 'header-left' : 'auth-header-content'; ?>">
    <h1>EScom Explorer</h1>
    <p><?= !$isLoggedIn && 'Sistema de navegación inteligente' ?></p>

    <?php if ($isLoggedIn): ?>
      <nav class="main-nav">
        <ul>
          <li><a href="/" class="<?= $currentPage === "/" ? 'active' : '' ?>">Inicio</a></li>
          <li><a href="/lugares" class="<?= $currentPage === "/lugares" ? 'active' : '' ?>">Lugares</a></li>
          <li><a href="/favoritos" class="<?= $currentPage === "/favoritos" ? 'active' : '' ?>">Favoritos</a></li>
          <li><a href="/dashboard" class="<?= $currentPage === "/dashboard" ? 'active' : '' ?>">Gestión</a></li>
        </ul>
      </nav>
    <?php endif; ?>
  </div>

  <div class="<?php echo $isLoggedIn ? 'auth-section' : 'user-info'; ?>">
    <?php if ($isLoggedIn): ?>
      <span>Bienvenido, <?php echo htmlspecialchars($currentUser['firstname']); ?></span>
      <a href="/logout" class="nav-link">Logout</a>
    <?php else: ?>
      <div id="auth-container">
      </div>
    <?php endif; ?>
  </div>
</header>
