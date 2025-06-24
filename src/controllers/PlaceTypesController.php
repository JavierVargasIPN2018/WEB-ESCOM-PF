<?php

require_once 'models/PlaceTypes.php';

const PLACES_ROUTE = 'views/places/index.php';

class PlaceTypesController
{
  private $placeTypeModel;

  public function __construct()
  {
    $this->placeTypeModel = new PlaceTypes();
  }

  public function showPlaces($data = [])
  {
    extract($data);

    $places = $this->placeTypeModel->getAllPlaceTypes();
    $success = $_GET['success'] ?? '';

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
    $description = trim($_POST['description'] ?? '');
    $icon = trim($_POST['icon'] ?? '');
    $color = $_POST['color'] ?? '';

    $old = compact('name', 'description', 'icon', 'color');
    $errors = $this->validatePlaceType($name);

    if (!empty($errors)) {
      $places = $this->placeTypeModel->getAllPlaceTypes();
      include PLACES_ROUTE;
      return;
    }

    $placeId = $this->placeTypeModel->create($name, $description, $icon, $color);

    if ($placeId) {
      header('Location: /lugares?success=1');
      exit;
    } else {
      $errors[] = 'Hubo un error al registrar el lugar.';
      $places = $this->placeTypeModel->getAllPlaceTypes();
      include PLACES_ROUTE;
    }
  }

  public function getAllPlaceTypes()
  {
    return $this->placeTypeModel->getAllPlaceTypes();
  }

  public function countPlaceTypes()
  {
    return $this->placeTypeModel->countPlaceTypes();
  }

  private function validatePlaceType($name)
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
