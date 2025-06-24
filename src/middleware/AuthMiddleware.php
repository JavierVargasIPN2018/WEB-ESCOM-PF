<?php

require_once 'controllers/AuthController.php';

class AuthMiddleware
{
  private $auth;

  public function __construct()
  {
    $this->auth = new AuthController();
  }

  /**
   * Middleware to check if user is authenticated
   */
  public function requireAuth($next = null)
  {
    if (!$this->auth->isLoggedIn()) {
      // Store intended URL for redirect after login
      if (!isset($_SESSION)) {
        session_start();
      }
      $_SESSION['intended_url'] = $_SERVER['REQUEST_URI'];

      header('Location: /login');
      exit;
    }

    // Continue to next middleware or controller
    if ($next && is_callable($next)) {
      return $next();
    }

    return true;
  }

  /**
   * Middleware to check if user is admin
   */
  public function requireAdmin($next = null)
  {
    // First check if authenticated
    $this->requireAuth();

    if (!$this->auth->isAdmin()) {
      header('HTTP/1.1 403 Forbidden');
      include 'views/errors/403.php';
      exit;
    }

    // Continue to next middleware or controller
    if ($next && is_callable($next)) {
      return $next();
    }

    return true;
  }

  /**
   * Middleware to redirect authenticated users
   */
  public function redirectIfAuthenticated($redirectUrl = '/dashboard')
  {
    if ($this->auth->isLoggedIn()) {
      header("Location: $redirectUrl");
      exit;
    }

    return true;
  }

  /**
   * CSRF Protection
   */
  public function csrfProtection()
  {
    if (!isset($_SESSION)) {
      session_start();
    }

    // Generate CSRF token if not exists
    if (!isset($_SESSION['csrf_token'])) {
      $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    // Check CSRF token on POST requests
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $token = $_POST['csrf_token'] ?? '';

      if (!hash_equals($_SESSION['csrf_token'], $token)) {
        header('HTTP/1.1 403 Forbidden');
        die('CSRF token mismatch');
      }
    }

    return true;
  }

  /**
   * Rate limiting for login attempts
   */
  public function loginRateLimit($maxAttempts = 5, $timeWindow = 300)
  {
    if (!isset($_SESSION)) {
      session_start();
    }

    $ip = $_SERVER['REMOTE_ADDR'];
    $key = "login_attempts_$ip";

    if (!isset($_SESSION[$key])) {
      $_SESSION[$key] = [];
    }

    $now = time();
    $attempts = $_SESSION[$key];

    // Remove old attempts outside time window
    $attempts = array_filter($attempts, function ($timestamp) use ($now, $timeWindow) {
      return ($now - $timestamp) < $timeWindow;
    });

    // Check if exceeded max attempts
    if (count($attempts) >= $maxAttempts) {
      header('HTTP/1.1 429 Too Many Requests');
      die('Too many login attempts. Please try again later.');
    }

    // Add current attempt if this is a POST request
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $attempts[] = $now;
      $_SESSION[$key] = $attempts;
    }

    return true;
  }

  /**
   * Security headers
   */
  public function securityHeaders()
  {
    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: DENY');
    header('X-XSS-Protection: 1; mode=block');
    header('Strict-Transport-Security: max-age=31536000; includeSubDomains');

    return true;
  }

  /**
   * Get CSRF token for forms
   */
  public function getCsrfToken()
  {
    if (!isset($_SESSION)) {
      session_start();
    }

    if (!isset($_SESSION['csrf_token'])) {
      $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
  }

  /**
   * Check user role
   */
  public function hasRole($role)
  {
    if (!$this->auth->isLoggedIn()) {
      return false;
    }

    $currentUser = $this->auth->getCurrentUser();
    return $currentUser['role'] === $role;
  }

  /**
   * Check multiple roles (OR condition)
   */
  public function hasAnyRole($roles)
  {
    if (!$this->auth->isLoggedIn()) {
      return false;
    }

    $currentUser = $this->auth->getCurrentUser();
    return in_array($currentUser['role'], $roles);
  }
}
