<!DOCTYPE html>
<html lang="es">

<? require "components/head.php" ?>

<body>
  <!-- HEADER SIMPLIFICADO -->
  <? require "components/layouts/header.php" ?>

  <!-- ALERTA DE ERROR -->
  <? require "components/ui/alerts/error.php" ?>

  <!-- ALERTA DE ÉXITO -->
  <? require "components/ui/alerts/success.php" ?>

  <!-- MAIN CONTENT -->
  <main class="auth-main">
    <div class="auth-container">
      <!-- Tabs -->
      <div class="auth-tabs">
        <a href="/login" class="tab-button active" data-tab="login">Iniciar Sesión</a>
        <a href="/register" class="tab-button" data-tab="register">Registrarse</a>
      </div>

      <!-- Login Form -->
      <? require "views/auth/components/login-form.php" ?>

    </div>

    <!-- Background decoration -->
    <? require "views/auth/components/background-decoration.php" ?>
  </main>

  <!-- FOOTER -->
  <? require "components/layouts/footer.php" ?>

  <!-- Loading overlay -->
  <? require "components/loading-overlay.php" ?>

  <!-- <script src="../js/auth.js"></script> -->
</body>

</html>
