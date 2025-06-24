<?php

require_once 'models/User.php';

class AuthController
{
  private $userModel;

  public function __construct()
  {
    $this->userModel = new User();

    // Start session if not already started
    if (session_status() == PHP_SESSION_NONE) {
      session_start();
    }
  }

  /**
   * Show login form
   */
  public function showLogin()
  {
    // Redirect if already logged in
    if ($this->isLoggedIn()) {
      $this->redirectToDashboard();
      return;
    }

    include 'views/auth/login.php';
  }

  /**
   * Handle login form submission
   */
  public function login()
  {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
      $this->showLogin();
      return;
    }

    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $errors = [];

    // Validation
    if (empty($email)) {
      $errors[] = 'Username is required';
    }

    if (empty($password)) {
      $errors[] = 'Password is required';
    }

    if (!empty($errors)) {
      include 'views/auth/login.php';
      return;
    }

    // Verify credentials
    $user = $this->userModel->verifyCredentials($email, $password);

    if ($user) {
      // Set session data
      $_SESSION['user_id'] = $user['id'];
      $_SESSION['firstname'] = $user['firstname'];
      $_SESSION['lastname'] = $user['lastname'];
      $_SESSION['role'] = $user['role'];
      $_SESSION['email'] = $user['email'];
      $_SESSION['student_id'] = $user['student_id'];
      $_SESSION['logged_in'] = true;

      // Update last login
      $this->userModel->updateLastLogin($user['id']);

      // Redirect to home
      $this->redirectToHome();
    } else {
      $errors[] = 'Invalid email or password';
      include 'views/auth/login.php';
    }
  }

  /**
   * Show registration form
   */
  public function showRegister()
  {
    // Redirect if already logged in
    if ($this->isLoggedIn()) {
      $this->redirectToDashboard();
      return;
    }

    include 'views/auth/register.php';
  }

  /**
   * Handle registration form submission
   */
  public function register()
  {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
      $this->showRegister();
      return;
    }

    $firstname = trim($_POST['firstname'] ?? '');
    $lastname = trim($_POST['lastname'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $student_id = $_POST['student_id'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    $errors = [];

    // Validation
    if (empty($firstname)) {
      $errors[] = 'Firstname is required';
    } elseif (strlen($firstname) < 3) {
      $errors[] = 'Firstname must be at least 3 characters long';
    }

    if (empty($lastname)) {
      $errors[] = 'Lastname is required';
    } elseif (strlen($lastname) < 3) {
      $errors[] = 'Lastname must be at least 3 characters long';
    }

    if (empty($email)) {
      $errors[] = 'Email is required';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
      $errors[] = 'Invalid email format';
    }

    if (empty($password)) {
      $errors[] = 'Password is required';
    } elseif (strlen($password) < 6) {
      $errors[] = 'Password must be at least 6 characters long';
    }

    if ($password !== $confirmPassword) {
      $errors[] = 'Passwords do not match';
    }

    if (!empty($errors)) {
      include 'views/auth/register.php';
      return;
    }

    // Create user
    $userId = $this->userModel->create($firstname, $lastname, $email, $password, $student_id);

    if ($userId) {
      $success = 'Registration successful. Please login.';
      include 'views/auth/login.php';
    } else {
      $errors[] = 'Username or email already exists';
      include 'views/auth/register.php';
    }
  }

  /**
   * Handle logout
   */
  public function logout()
  {
    // Destroy session
    session_destroy();

    // Redirect to login
    header('Location: /login');
    exit;
  }

  /**
   * Check if user is logged in
   */
  public function isLoggedIn()
  {
    return isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
  }

  /**
   * Get current user
   */
  public function getCurrentUser()
  {
    if (!$this->isLoggedIn()) {
      return null;
    }

    return [
      'id' => $_SESSION['user_id'],
      'firstname' => $_SESSION['firstname'],
      'lastname' => $_SESSION['lastname'],
      'role' => $_SESSION['role'],
      'email' => $_SESSION['email'],
      'student_id' => $_SESSION['student_id']
    ];
  }

  /**
   * Check if current user is admin
   */
  public function isAdmin()
  {
    return $this->isLoggedIn() && $_SESSION['role'] === 'admin';
  }

  /**
   * Redirect to home
   */
  private function redirectToHome()
  {
    header('Location: /');
    exit;
  }

  /**
   * Redirect to appropriate dashboard
   */
  private function redirectToDashboard()
  {
    if ($this->isAdmin()) {
      header('Location: /admin/dashboard');
    } else {
      header('Location: /dashboard');
    }
    exit;
  }

  /**
   * Require authentication
   */
  public function requireAuth()
  {
    if (!$this->isLoggedIn()) {
      header('Location: /login');
      exit;
    }
  }

  /**
   * Require admin role
   */
  public function requireAdmin()
  {
    $this->requireAuth();

    if (!$this->isAdmin()) {
      header('HTTP/1.1 403 Forbidden');
      include 'views/errors/403.php';
      exit;
    }
  }
}
