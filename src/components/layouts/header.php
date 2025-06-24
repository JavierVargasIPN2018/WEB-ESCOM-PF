<?php
if (!isset($authController)) {
  $authController = null;
}

$currentUser = $authController ? $authController->getCurrentUser() : null;
$isLoggedIn = $currentUser !== null;

?>

<header class="main-header">
  <div class="header-left">
    <h1>Batiz Explorer</h1>

    <nav class="main-nav">
      <ul>
        <li><a href="/" class="<?= $currentPage === "/" ? 'active' : '' ?>">Inicio</a></li>
        <li><a href="/lugares" class="<?= $currentPage === "/lugares" ? 'active' : '' ?>">Lugares</a></li>
        <li><a href="/favoritos" class="<?= $currentPage === "/favoritos" ? 'active' : '' ?>">Favoritos</a></li>
        <li><a href="/dashboard" class="<?= $currentPage === "/dashboard" ? 'active' : '' ?>">Gestión</a></li>
      </ul>
    </nav>
  </div>

  <div class="<?= $isLoggedIn ? 'auth-section' : 'user-info'; ?>">
    <?php if ($isLoggedIn): ?>
      <span>Bienvenido, <?= htmlspecialchars($currentUser['firstname']); ?></span>
      <a href="/logout" class="nav-link">Logout</a>
    <?php else: ?>
      <div id="auth-container">
        <a href="/login" class="auth-button">Iniciar Sesión</a>
        <a href="/register" class="auth-button">Registrarse</a>
      </div>
    <?php endif; ?>
  </div>
</header>
