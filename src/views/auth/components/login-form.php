 <div id="login-tab" class="auth-form-container active">
   <div class="auth-form-header">
     <h2>¡Bienvenido de vuelta!</h2>
     <p>Inicia sesión para acceder a todas las funcionalidades</p>
   </div>

   <form id="login-form" class="auth-form" method="POST" action="/login">
     <?php
      // Include CSRF token
      require_once 'middleware/AuthMiddleware.php';
      $middleware = new AuthMiddleware();
      $csrfToken = $middleware->getCsrfToken();
      ?>

     <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">

     <div class="form-group">
       <label for="login-email">Correo electrónico</label>
       <input
         id="login-email"
         name="email"
         type="email"
         placeholder="tu.correo@alumno.ipn.mx"
         autocomplete="email"
         value="<?= htmlspecialchars($_POST['email'] ?? ''); ?>"
         required>
       <span class="form-icon">📧</span>
     </div>

     <div class="form-group">
       <label for="login-password">Contraseña</label>
       <input
         id="login-password"
         type="password"
         name="password"
         placeholder="Tu contraseña"
         autocomplete="current-password"
         required>
       <span class="form-icon">🔒</span>
       <button type="button" class="password-toggle" data-target="login-password">
         <span class="toggle-icon">👁️</span>
       </button>
     </div>

     <button type="submit" class="auth-submit-btn">
       <span class="btn-text">Iniciar Sesión</span>
       <span class="btn-icon">→</span>
     </button>
   </form>
 </div>
