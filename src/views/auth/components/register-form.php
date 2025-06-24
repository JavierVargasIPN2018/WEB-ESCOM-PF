<div class="auth-form-container active">
  <div class="auth-form-header">
    <h2>Crear cuenta nueva</h2>
    <p>Únete a la comunidad EScom y explora el campus</p>
  </div>

  <form method="POST" action="/register" id="register-form" class="auth-form">
    <?php
    // Include CSRF token
    require_once 'middleware/AuthMiddleware.php';
    $middleware = new AuthMiddleware();
    $csrfToken = $middleware->getCsrfToken();
    ?>

    <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">


    <div class="form-row">
      <div class="form-group">
        <label for="register-firstname">Nombre</label>
        <input
          id="register-firstname"
          type="text"
          name="firstname"
          autocomplete="firstname"
          placeholder="Tu nombre"
          value="<?= htmlspecialchars($_POST['firstname'] ?? ''); ?>"
          minlength="3"
          required>
        <span class="form-icon">👤</span>
      </div>
      <div class="form-group">
        <label for="register-lastname">Apellidos</label>
        <input
          type="text"
          id="register-lastname"
          name="lastname"
          autocomplete="lastname"
          placeholder="Tus apellidos"
          value="<?= htmlspecialchars($_POST['lastname'] ?? ''); ?>"
          minlength="3"
          required>
        <span class="form-icon">👤</span>
      </div>
    </div>

    <div class="form-group">
      <label for="register-email">Correo electrónico</label>
      <input
        id="register-email"
        type="email"
        name="email"
        autocomplete="email"
        placeholder="tu.correo@alumno.ipn.mx"
        value="<?= htmlspecialchars($_POST['email'] ?? ''); ?>"
        required>
      <span class="form-icon">📧</span>
    </div>

    <div class="form-group">
      <label for="register-student-id">Boleta (opcional)</label>
      <input
        id="register-student-id"
        type="text"
        name="student_id"
        placeholder="2023XXXXXXX"
        pattern="[0-9]{10}">
      <span class="form-icon">🎓</span>
    </div>

    <div class="form-group">
      <label for="register-password">Contraseña</label>
      <input
        id="register-password"
        type="password"
        name="password"
        autocomplete="new-password"
        placeholder="Mínimo 8 caracteres"
        minlength="8"
        required>
      <span class="form-icon">🔒</span>
      <button type="button" class="password-toggle" data-target="register-password">
        <span class="toggle-icon">👁️</span>
      </button>
    </div>

    <div class="form-group">
      <label for="register-confirm-password">Confirmar contraseña</label>
      <input
        id="register-confirm-password"
        type="password"
        name="confirm_password"
        autocomplete="new-password"
        placeholder="Repite tu contraseña"
        required>
      <span class="form-icon">🔒</span>
      <button type="button" class="password-toggle" data-target="register-confirm-password">
        <span class="toggle-icon">👁️</span>
      </button>
    </div>

    <div class="password-strength">
      <div class="strength-bar">
        <div class="strength-fill"></div>
      </div>
      <span class="strength-text">Seguridad de la contraseña</span>
    </div>

    <div class="form-options">
      <label class="checkbox-container">
        <input type="checkbox" id="accept-terms" required>
        <span class="checkmark"></span>
        Acepto los <a href="#" class="terms-link">términos y condiciones</a>
      </label>
    </div>

    <button type="submit" class="auth-submit-btn">
      <span class="btn-text">Crear Cuenta</span>
      <span class="btn-icon">→</span>
    </button>
  </form>
</div>
