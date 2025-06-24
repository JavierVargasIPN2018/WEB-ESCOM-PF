<!DOCTYPE html>
<html lang="es">

<? require "components/head.php" ?>

<body>
  <!-- HEADER SIMPLIFICADO -->
  <? require "components/layouts/header.php" ?>

  <!-- Alerta de Error -->
  <? require "components/ui/alerts/error.php" ?>

  <!-- MAIN CONTENT -->
  <main class="auth-main">
    <div class="auth-container">
      <!-- Tabs -->
      <div class="auth-tabs">
        <a href="/login" class="tab-button" data-tab="login">Iniciar Sesión</a>
        <a href="/register" class="tab-button active" data-tab="register">Registrarse</a>
      </div>


      <!-- Register Form -->
      <? require("components/register-form.php") ?>
    </div>

    <!-- Background decoration -->
    <? require("components/background-decoration.php") ?>
  </main>

  <!-- FOOTER -->
  <? require "components/layouts/footer.php" ?>

  <!-- Loading overlay -->
  <? require "components/loading-overlay.php" ?>

  <script src="../js/auth.js"></script>
</body>

</html>
