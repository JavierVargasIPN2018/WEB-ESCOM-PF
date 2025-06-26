<?php
require_once 'components/icons/favorite-icon.php';

?>

<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Batiz Explorer - Navegación Inteligente</title>
  <link rel="stylesheet" href="styles/globals.css">
  <link rel="stylesheet" href="styles/responsive.css">
  <link rel="stylesheet" href="styles/home.css">
  <link rel="stylesheet" href="styles/auth.css">
  <link rel="stylesheet" href="styles/map.css">
</head>

<body>
  <!-- HEADER -->
  <? require "components/layouts/header.php" ?>

  <!-- MAIN CONTENT -->
  <main class="main-content">
    <!-- Hero Section -->
    <section class="hero-section">
      <div class="hero-content">
        <h2>Explora Batiz de manera inteligente</h2>
        <p>Encuentra fácilmente cualquier lugar dentro de la Escuela Superior de Cómputo</p>
      </div>
    </section>

    <!-- Explorer Container -->
    <div class="explorer-container">
      <!-- Mapa (columna izquierda) -->
      <section class="map-section">
        <div class="map-header">
          <h3>Mapa Interactivo</h3>
          <p>Vista general del campus</p>
        </div>
        <div class="map-container">
          <div id="map-buttons" class="map-buttons">
            <button class="map-btn" data-floor="baja">Planta baja</button>
            <button class="map-btn" data-floor="1">1er piso</button>
            <button class="map-btn" data-floor="2">2º piso</button>
            <button class="map-btn" data-floor="3">3er piso</button>
            <button class="map-btn" data-floor="all">Mostrar todo</button>
          </div>
          <div id="map"></div>
          <!-- <img src="public/images/placeholder.webp" alt="Mapa de ubicaciones Batiz" class="map-image"> -->
          <div class="map-overlay">
            <div class="map-stats">
              <div class="stat">
                <span class="stat-number" id="total-places">
                  <?= $countPlaces ?>
                </span>
                <span class="stat-label">Lugares</span>
              </div>
              <div class="stat">
                <span class="stat-number" id="total-categories">
                  <?= $countPlaceTypes ?>
                </span>
                <span class="stat-label">Categorías</span>
              </div>
              <div class="stat">
                <span class="stat-number" id="total-favorites">
                  <?= $countFavoritePlaces; ?>
                </span>
                <span class="stat-label">Favoritos</span>
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- Panel de búsqueda (columna derecha) -->
      <section class="search-section">
        <div class="search-panel">
          <div class="search-header">
            <h3>Buscar Ubicaciones</h3>
            <p>Encuentra rápidamente cualquier lugar</p>
          </div>

          <!-- Formulario de búsqueda -->
          <form class="search-form" action="/" method="GET">
            <div class="search-filters">
              <select
                id="category-filter"
                name="type"
                class="category-select">
                <option value="">Todas las categorías</option>
                <? foreach ($placeTypes as $type): ?>
                  <option
                    value="<?= $type["id"] ?>"
                    <?= isset($_GET['type']) && $_GET['type'] == $type["id"] ? 'selected' : '' ?>>
                    <?= $type["name"] ?>
                  </option>
                <? endforeach ?>
              </select>
            </div>

            <div class="search-input-container">
              <input
                type="text"
                id="search-input"
                name="q"
                placeholder="Buscar por nombre o categoría..."
                class="search-input"
                value="<?= htmlspecialchars($_GET['q'] ?? '') ?>">
              <button type="submit" class="search-button">
                <span>🔍</span>
              </button>
            </div>


          </form>

          <!-- Resultados de búsqueda -->
          <div id="search-results" class="search-results active">
            <h4>Resultados de búsqueda</h4>
            <div id="results-container" class="results-container">
              <!-- Los resultados se mostrarán aquí -->
              <? if (!empty($places)): ?>
                <? foreach ($places as $place): ?>
                  <a href="/lugares/<?= $place["id"] ?>">
                    <div class="result-item">
                      <div class="result-info">
                        <h5><?= htmlspecialchars($place['name']) ?></h5>
                        <p><?= htmlspecialchars($place['description']) ?></p>
                      </div>
                    </div>
                  </a>
                <? endforeach ?>
              <? else: ?>
                <p>No se encontraron resultados.</p>
              <? endif ?>
            </div>
          </div>

          <!-- Lugares populares -->
          <div class="popular-section">
            <h4>Lugares Populares</h4>
            <div id="popular-places" class="popular-list">
              <!-- Se cargarán dinámicamente -->
            </div>
          </div>

          <!-- Accesos rápidos -->
          <div class="quick-actions">
            <h4>Accesos Rápidos</h4>
            <div class="action-buttons">
              <a href="/lugares" class="action-btn primary">
                <span class="btn-icon">📍</span>
                <span>Ver Todos los Lugares</span>
              </a>
              <a href="/favoritos" class="action-btn secondary">
                <?= favoriteIcon(["class" => 'btn-icon']) ?>
                <span>Mis Favoritos</span>
              </a>
            </div>
          </div>
        </div>
      </section>

    </div>

    <!-- Sección de categorías destacadas -->
    <section class="featured-categories">
      <h2>Categorías Principales</h2>
      <div id="categories-preview" class="categories-preview">
        <? foreach ($placeTypes as $type): ?>
          <div class="category-preview">
            <h3><?= $type["name"]; ?></h3>
            <p><?= $type["description"]; ?></p>

          </div>
        <? endforeach ?>
      </div>
    </section>
  </main>

  <!-- FOOTER -->
  <? require "components/layouts/footer.php" ?>
  <script src="https://unpkg.com/konva@9/konva.min.js"></script>
  <script src="scripts/map.js"></script>

</body>

</html>
