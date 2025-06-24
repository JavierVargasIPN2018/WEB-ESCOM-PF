<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>EScom Explorer - Navegación Inteligente</title>
  <link rel="stylesheet" href="styles/globals.css">
  <link rel="stylesheet" href="styles/responsive.css">
  <link rel="stylesheet" href="styles/home.css">
</head>

<body>
  <!-- HEADER -->
  <? require "components/layouts/header.php" ?>

  <!-- MAIN CONTENT -->
  <main class="main-content">
    <!-- Hero Section -->
    <section class="hero-section">
      <div class="hero-content">
        <h2>Explora EScom de manera inteligente</h2>
        <p>Encuentra fácilmente cualquier lugar dentro de la Escuela Superior de Cómputo</p>
      </div>
    </section>

    <!-- Explorer Grid -->
    <div class="explorer-grid">
      <!-- Mapa (columna izquierda) -->
      <section class="map-section">
        <div class="map-header">
          <h3>Mapa Interactivo</h3>
          <p>Vista general del campus</p>
        </div>
        <div class="map-container">
          <img src="public/images/placeholder.webp" alt="Mapa de ubicaciones EScom" class="map-image">
          <div class="map-overlay">
            <div class="map-stats">
              <div class="stat">
                <span class="stat-number" id="total-places">0</span>
                <span class="stat-label">Lugares</span>
              </div>
              <div class="stat">
                <span class="stat-number" id="total-categories">0</span>
                <span class="stat-label">Categorías</span>
              </div>
              <div class="stat">
                <span class="stat-number" id="total-favorites">0</span>
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

          <div class="search-form">
            <div class="search-input-container">
              <input type="text" id="search-input" placeholder="Buscar por nombre o categoría..." class="search-input">
              <button type="button" id="search-button" class="search-button">
                <span>🔍</span>
              </button>
            </div>
            <div class="search-filters">
              <select id="category-filter" class="category-select">
                <option value="">Todas las categorías</option>
                <? foreach ($placeTypes as $type): ?>
                  <option value="<?= $type["id"] ?>"><?= $type["name"] ?></option>
                <? endforeach ?>
              </select>
            </div>
          </div>

          <!-- Resultados de búsqueda -->
          <div id="search-results" class="search-results">
            <h4>Resultados de búsqueda</h4>
            <div id="results-container" class="results-container">
              <!-- Los resultados se mostrarán aquí -->
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
              <a href="categorias.html" class="action-btn primary">
                <span class="btn-icon">📍</span>
                <span>Ver Todos los Lugares</span>
              </a>
              <a href="favoritos.html" class="action-btn secondary">
                <span class="btn-icon">⭐</span>
                <span>Mis Favoritos</span>
              </a>
              <a href="admin.html" class="action-btn tertiary">
                <span class="btn-icon">⚙️</span>
                <span>Gestionar Lugares</span>
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

        <? endforeach ?>
      </div>
    </section>
  </main>

  <!-- FOOTER -->
  <? require "components/layouts/footer.php" ?>

  <!-- <script src="js/places.js"></script> -->
  <!-- <script src="js/favorites.js"></script> -->
  <!-- <script src="js/index.js"></script> -->
  <!-- <script src="js/header-auth.js"></script> -->
</body>

</html>
