<?php

require_once 'models/FavoritePlace.php';

const FAVORITES_ROUTE = 'views/favorites/index.php';

class FavoritePlacesController
{
  private $favoritePlaceModel;

  public function __construct()
  {
    $this->favoritePlaceModel = new FavoritePlace();
  }

  public function showFavorites($data = [])
  {
    extract($data);

    $userId = $_SESSION['user_id'];
    $searchName = $_GET['search'] ?? '';
    $placeType = $_GET['type'] ?? '';

    $favorites = $this->favoritePlaceModel->searchFavorites($userId, $searchName, $placeType);
    $favoritesCount = count($favorites);

    $success = $_GET['success'] ?? '';
    $error = $_GET['error'] ?? '';

    include FAVORITES_ROUTE;
  }

  public function checkFavorite()
  {

    $userId = $_SESSION['user_id'];
    $placeId = $_GET['place_id'] ?? '';

    if (empty($placeId)) {
      $this->sendJsonResponse(['success' => false, 'message' => 'ID de lugar requerido']);
      return;
    }

    $favorite = $this->favoritePlaceModel->findByPlaceId($userId, $placeId);

    $this->sendJsonResponse([
      'success' => true,
      'is_favorite' => !empty($favorite)
    ]);
  }

  public function getFavorites()
  {
    $userId = $_SESSION['user_id'];

    $searchName = $_GET['search'] ?? '';
    $placeType = $_GET['type'] ?? '';

    $favorites = $this->favoritePlaceModel->getFavoritePlaces($userId) ?? [];

    return $favorites;
  }


  public function toggleFavorite()
  {

    $userId = $_SESSION['user_id'];
    $placeId = $_POST['place_id'] ?? '';

    $errors = $this->validateFavorite($placeId);

    if (!empty($errors)) {
      return;
    }

    $existingFavorite = $this->favoritePlaceModel->findByPlaceId($userId, $placeId);

    if ($existingFavorite) {
      $this->favoritePlaceModel->delete($userId, $placeId);
    } else {
      $this->favoritePlaceModel->create($userId, $placeId);
    }
  }

  private function validateFavorite($placeId)
  {
    $errors = [];

    if (empty($placeId)) {
      $errors[] = 'El ID del lugar es obligatorio';
    } elseif (!is_numeric($placeId)) {
      $errors[] = 'El ID del lugar debe ser un número válido';
    }

    return $errors;
  }

  private function sendJsonResponse($data)
  {
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
  }
}
