<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Favoritos - Batiz Explorer</title>
  <link rel="stylesheet" href="styles/globals.css">
  <link rel="stylesheet" href="styles/responsive.css">
  <link rel="stylesheet" href="styles/favorites.css">
</head>

<body>
  <!-- HEADER -->
  <? require "components/layouts/header.php" ?>

  <!-- MAIN CONTENT -->
  <main class="main-content">
    <section class="favorites-section">
      <div class="section-header">
        <div class="header-content">
          <h2>Mis Lugares Favoritos</h2>
          <p class="section-subtitle">Tus lugares guardados para acceso rápido</p>
        </div>
        <div class="favorites-stats">
          <div class="stat-item">
            <span class="stat-number" id="favorites-count">
              <?= $favoritesCount ?>
            </span>
            <span class="stat-label">Favoritos</span>
          </div>
          <div class="stat-item">
            <span class="stat-number" id="categories-count">
              <?= $categoryCount ?>
            </span>
            <span class="stat-label">Categorías</span>
          </div>
        </div>
      </div>

      <!-- Filtros -->
      <div class="favorites-filters">
        <div class="filter-group">
          <label for="category-filter">Filtrar por categoría:</label>
          <select id="category-filter" class="filter-select">
            <option value="">Todas las categorías</option>
            <? foreach ($placeTypes as $type): ?>
              <option value="<?= $type["id"] ?>"><?= $type["name"] ?></option>
            <? endforeach ?>
          </select>
        </div>
        <div class="filter-group">
          <label for="search-filter">Buscar:</label>
          <input type="text" id="search-filter" placeholder="Buscar en favoritos..." class="filter-input">
        </div>
      </div>

      <div id="favorites-container" class="favorites-grid">
        <!-- Los favoritos se cargarán dinámicamente -->
      </div>

      <div id="empty-favorites" class="empty-state" style="display: none;">
        <div class="empty-illustration">
          <div class="empty-icon">⭐</div>
          <div class="empty-stars">
            <span>✨</span>
            <span>⭐</span>
            <span>✨</span>
          </div>
        </div>
        <h3>¡Aún no tienes favoritos!</h3>
        <p>Explora lugares increíbles y guarda tus favoritos para acceder rápidamente a ellos</p>
        <a href="categorias.html" class="cta-button">
          <span>Explorar lugares</span>
          <span class="button-icon">→</span>
        </a>
      </div>
    </section>
  </main>

  <!-- FOOTER -->
  <? require "components/layouts/footer.php" ?>

  <script src="../js/places.js"></script>
  <script src="../js/favorites.js"></script>
  <script src="../js/favorites-page.js"></script>
  <script src="../js/header-auth.js"></script>
</body>

</html>
