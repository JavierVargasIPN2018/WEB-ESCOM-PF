<?php

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

$placeTypesController = new PlaceTypes();
$placeController = new PlaceController();
$connectionController = new ConnectionController();
$favoritePlacesController = new FavoritePlacesController();

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

// Normalize the current route
$normalizedUri = normalizeRoute($requestUri);


// Apply security headers
$authMiddleware->securityHeaders();

if (preg_match('#^/lugares/(\d+)$#', $normalizedUri, $matches)) {
  $authMiddleware->csrfProtection();

  $id = (int)$matches[1]; // Capturamos el ID

  $placeController->showPlaceDetail([
    'placeId' => $id,
    'connectionController' => $connectionController,
    'favoritePlacesController' => $favoritePlacesController,
    'currentPage' => "/lugares"
  ]);
  return;
}

// Simple routing
switch ($normalizedUri) {
  // views
  case '/':
    // Redirect to appropriate dashboard
    // if ($authController->isLoggedIn()) {
    //   if ($authController->isAdmin()) {
    //     header('Location: /admin/dashboard');
    //   } else {
    //     header('Location: /dashboard');
    //   }
    // } else {
    //   header('Location: /login');
    // }


    $currentPage = "/";

    $favoritesCount = 0;
    $categoryCount = 0;

    $placeTypes = $placeTypesController->getAllPlaceTypes();

    include 'views/home/index.php';
    break;

  case '/login':
    $authMiddleware->redirectIfAuthenticated();
    $authMiddleware->loginRateLimit();
    $authMiddleware->csrfProtection();

    if ($requestMethod === 'POST') {
      $authController->login();
    } else {

      $authController->showLogin();
    }
    break;

  case '/register':
    $authMiddleware->redirectIfAuthenticated();
    $authMiddleware->csrfProtection();

    if ($requestMethod === 'POST') {
      $authController->register();
    } else {
      $authController->showRegister();
    }
    break;

  case '/logout':
    $authMiddleware->requireAuth();
    $authController->logout();
    break;

  case '/lugares':
    $placeController->showPlaces([
      'currentPage' => "/lugares",
      'favoritePlacesController' => $favoritePlacesController,
      'connectionController' => $connectionController
    ]);

    break;

  case '/favoritos':
    $authMiddleware->requireAuth();
    $authMiddleware->csrfProtection();

    $currentPage = "/favoritos";

    $categoryCount = $placeTypesController->countPlaceTypes();

    $placeTypes = $placeTypesController->getAllPlaceTypes();

    $favoritePlacesController->showFavorites([
      'currentPage' => "/favoritos",
      'categoryCount' => $categoryCount,
      'placeTypes' => $placeTypes
    ]);

    break;

  case '/dashboard':
    $authMiddleware->requireAuth();
    include 'views/dashboard/user.php';
    break;

  // actions
  case '/toggle-favorite':
    $authMiddleware->requireAuth();
    $authMiddleware->csrfProtection();

    if ($requestMethod === 'POST') {
      $favoritePlacesController->toggleFavorite();
      $redirectUrl = $_POST['redirect_url'] ?? '/lugares';

      header('Location:' . $redirectUrl);
    } else {
      header('Location: /lugares');
      exit;
    }
    break;

  // api

  case '/api/user':
    $authMiddleware->requireAuth();
    header('Content-Type: application/json');
    echo json_encode($authController->getCurrentUser());
    break;

  case '/api/places':
    $authMiddleware->csrfProtection();
    $authMiddleware->requireAdmin();

    if ($requestMethod === 'POST') {
      $placeController->places([
        'authController' => $authController,
        'connectionController' => $connectionController
      ]);
    }

    break;

  case '/api/toggle-favorite':
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
