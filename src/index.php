<?php
// index.php - Simple Router

session_start();

// Auto-load classes
spl_autoload_register(function ($class) {
  // Convert namespace to file path
  $file = str_replace('\\', '/', $class) . '.php';

  // Check in different directories
  $paths = [
    'controllers/' . $file,
    'models/' . $file,
    'middleware/' . $file,
    $file
  ];

  foreach ($paths as $path) {
    if (file_exists($path)) {
      require_once $path;
      return;
    }
  }
});

// Initialize controllers
$authMiddleware = new AuthMiddleware();

$authController = new AuthController();
$placeTypesController = new PlaceTypesController();
$placeController = new PlaceController();

// Get the current URL path
$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$requestMethod = $_SERVER['REQUEST_METHOD'];

// Function to normalize route by removing extensions
function normalizeRoute($uri)
{
  // Remove trailing slash
  $uri = rtrim($uri, '/');

  // Remove .php and .html extensions
  $uri = preg_replace('/\.(php|html)$/', '', $uri);

  // Ensure we have at least a forward slash
  return $uri ?: '/';
}

// Function to extract route parameters
function extractRouteParams($pattern, $uri)
{
  $pattern = str_replace('*', '([^/]+)', $pattern);
  $pattern = '#^' . $pattern . '$#';

  if (preg_match($pattern, $uri, $matches)) {
    array_shift($matches); // Remove full match
    return $matches;
  }

  return false;
}

// Normalize the current route
$normalizedUri = normalizeRoute($requestUri);

// Apply security headers
$authMiddleware->securityHeaders();

// Simple routing
switch (true) {
  case $normalizedUri === '/':
    $currentPage = "/";
    $favoritesCount = 0;
    $categoryCount = 0;

    $placeTypes = $placeTypesController->getAllPlaceTypes();
    include 'views/home/index.php';
    break;

  case $normalizedUri === '/favoritos':
    $currentPage = "/favoritos";
    $favoritesCount = 0;
    $categoryCount = $placeTypesController->countPlaceTypes();
    $placeTypes = $placeTypesController->getAllPlaceTypes();
    include 'views/favorites/index.php';
    break;

  case $normalizedUri === '/login':
    $authMiddleware->redirectIfAuthenticated();
    $authMiddleware->loginRateLimit();
    $authMiddleware->csrfProtection();

    if ($requestMethod === 'POST') {
      $authController->login();
    } else {
      $authController->showLogin();
    }
    break;

  case $normalizedUri === '/register':
    $authMiddleware->redirectIfAuthenticated();
    $authMiddleware->csrfProtection();

    if ($requestMethod === 'POST') {
      $authController->register();
    } else {
      $authController->showRegister();
    }
    break;

  case $normalizedUri === '/logout':
    $authMiddleware->requireAuth();
    $authController->logout();
    break;

  case $normalizedUri === '/lugares':
    $authMiddleware->csrfProtection();

    if ($requestMethod === 'POST') {
      $placeController->places([
        'currentPage' => "/lugares",
        'authController' => $authController
      ]);
    } else {
      $placeController->showPlaces([
        'currentPage' => "/lugares",
        'authController' => $authController
      ]);
    }
    break;

  case ($params = extractRouteParams('/lugares/*', $normalizedUri)) !== false:
    // Ruta para detalle de lugar: /lugares/{id}
    $placeId = $params[0];

    if ($requestMethod === 'GET') {
      $placeController->showPlaceDetail([
        'placeId' => $placeId,
        'currentPage' => "/lugares",
        'authController' => $authController
      ]);
    } else {
      // Manejar otros métodos HTTP si es necesario (PUT, DELETE, etc.)
      header('HTTP/1.1 405 Method Not Allowed');
      echo json_encode(['error' => 'Método no permitido']);
    }
    break;

  case preg_match('#^/places\?id=(\d+)$#', $requestUri, $matches):
    // Compatibilidad con la URL antigua /places?id=123
    $placeId = $matches[1];
    header("Location: /lugares/$placeId", true, 301); // Redirect permanente
    break;

  case $normalizedUri === '/dashboard':
    $authMiddleware->requireAuth();
    include 'views/dashboard/user.php';
    break;

  case $normalizedUri === '/api/user':
    $authMiddleware->requireAuth();
    header('Content-Type: application/json');
    echo json_encode($authController->getCurrentUser());
    break;

  default:
    // 404 Not Found
    header('HTTP/1.1 404 Not Found');
    include 'views/errors/404.php';
    break;
}
