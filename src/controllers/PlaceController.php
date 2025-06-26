<?php

require_once 'models/Place.php';

require_once 'lib/utils.php';

const PLACES_ROUTE = 'views/places/index.php';

class PlaceController
{
  private $placeModel;

  public function __construct()
  {
    $this->placeModel = new Place();
  }

  public function showPlaces($data = [])
  {
    extract($data);

    $places = $this->placeModel->getAllPlaces();
    $success = $_GET['success'] ?? '';

    $userFavoritePlaces = $favoritePlacesController->getFavorites();

    $favPlaceIds = array_column($userFavoritePlaces, 'place_id');

    $errors = $errors ?? [];
    $old = $old ?? [];

    include PLACES_ROUTE;
  }

  public function places($data = [])
  {
    extract($data);

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
      $this->showPlaces();
      return;
    }

    $name = trim($_POST['name'] ?? '');
    $place_type_id = intval($_POST['place_type_id'] ?? 0);
    $short_code = trim($_POST['short_code'] ?? '');
    $building = $_POST['building'] ?? '';
    $floor_level = intval($_POST['floor_level'] ?? 0);
    $position_x = intval($_POST['position_x'] ?? 0);
    $position_y = intval($_POST['position_y'] ?? 0);
    $capacity = intval($_POST['capacity'] ?? 0);
    $description = trim($_POST['description'] ?? '');
    $is_accessible = trim($_POST['is_accessible'] ?? '');
    $is_active = trim($_POST['is_active'] ?? '');

    $old = compact('name', 'description', 'icon', 'color');
    $errors = $this->validatePlace($name);

    if (!empty($errors)) {
      $places = $this->placeModel->getAllPlaces();
      include PLACES_ROUTE;
      return;
    }

    $placeId = $this->placeModel->create(
      $name,
      $place_type_id,
      $short_code,
      $building,
      $floor_level,
      $position_x,
      $position_y,
      $capacity,
      $description,
      $is_accessible,
      $is_active
    );

    if ($placeId) {
      header('Location: /lugares?success=1');
      exit;
    } else {
      $errors[] = 'Hubo un error al registrar el lugar.';
      $places = $this->placeModel->getAllPlaces();
      include PLACES_ROUTE;
    }
  }

  public function showPlaceDetail($params)
  {
    $placeId = $params['placeId'];
    $currentPage = $params['currentPage'];
    $connectionController = $params['connectionController'];
    $favoritePlacesController = $params['favoritePlacesController'];

    if (!is_numeric($placeId)) {
      header('HTTP/1.1 404 Not Found');
      include 'views/errors/404.php';
      return;
    }

    try {
      // Obtener el lugar con su tipo
      $place = $this->placeModel->getPlaceWithType($placeId);

      if (!$place) {
        header('HTTP/1.1 404 Not Found');
        include 'views/errors/404.php';
        return;
      }

      $path = $connectionController->getPath($placeId);
      $connections = $connectionController->getConnectionsForPath($path);

      $relatedPlaces = $this->getRelatedPlaces($place["id"]);

      $userFavoritePlaces = $favoritePlacesController->getFavorites();
      $favPlaceIds = array_column($userFavoritePlaces, 'place_id');

      $placeId = $place['id'];
      $isFavorite = in_array($placeId, $favPlaceIds);

      include 'views/places/detail.php';
    } catch (Exception $e) {
      error_log("Error getting place detail: " . $e->getMessage());
      header('HTTP/1.1 500 Internal Server Error');
      include 'views/errors/500.php';
    }
  }

  public function getRelatedPlaces($placeId, $limit = 3)
  {
    $relatedPlaces = $this->placeModel->getRelatedPlaces($placeId, $limit);

    return $relatedPlaces;
  }


  private function validatePlace($name)
  {
    $errors = [];

    if (empty($name)) {
      $errors[] = 'El nombre es obligatorio.';
    } elseif (strlen($name) < 3) {
      $errors[] = 'El nombre debe tener al menos 3 caracteres.';
    }

    return $errors;
  }
}
