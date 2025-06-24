<?

$time = 0;
$distance = 0;

foreach ($connections as $path) {
  $time += $path["travel_time_minutes"];
  $distance += $path["distance_m"];
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Detalle de Lugar - EScom Explorer</title>
  <link rel="stylesheet" href="../styles/globals.css">
  <link rel="stylesheet" href="../styles/responsive.css">
  <link rel="stylesheet" href="../styles/detail.css">
</head>

<body>
  <!-- HEADER -->
  <? require 'components/layouts/header.php' ?>

  <!-- MAIN CONTENT -->
  <main class="main-content">
    <div class="breadcrumb">
      <a href="/">Inicio</a> &gt;
      <a href="/lugares">Lugares</a> &gt;
      <span id="breadcrumb-place"><?= $place["name"] ?></span>
    </div>

    <article class="place-detail">
      <!-- Imagen del lugar con overlay -->
      <div class="place-hero">
        <div class="place-image-container">
          <img id="place-image" src="../../public/images/placeholder.webp" alt="Imagen del lugar" class="place-image">
        </div>
        <div class="place-hero-content">
          <div id="place-badge" class="place-badge"><?= $place["type_name"] ?></div>
          <h1 id="place-title" class="place-title"><?= $place["name"] ?></h1>
          <button id="favorite-button" class="favorite-button">
            <span class="star-icon">⭐</span>
            <span id="favorite-text">Agregar a favoritos</span>
          </button>
        </div>
      </div>

      <!-- Información del lugar -->
      <div class="place-content">
        <section class="place-info">
          <div class="place-description">
            <h2>Descripción</h2>
            <p id="place-description"><?= $place["description"] ?></p>

            <div id="place-features" class="place-features">
              <!-- Las características se cargarán dinámicamente -->
            </div>
          </div>
        </section>

        <!-- Sección de ruta -->
        <section class="route-info">
          <h2>¿Cómo llegar?</h2>

          <div class="route-stats">
            <div class="route-stat">
              <div class="stat-icon time-icon"></div>
              <div class="stat-content">
                <span class="stat-label">Tiempo estimado</span>
                <span class="stat-value"><?= $time ?> minutos</span>
                <span class="stat-detail">desde la entrada principal</span>
              </div>
            </div>

            <div class="route-stat">
              <div class="stat-icon distance-icon"></div>
              <div class="stat-content">
                <span class="stat-label">Distancia</span>
                <span class="stat-value"><?= $distance  ?> metros</span>
                <span class="stat-detail">aproximadamente</span>
              </div>
            </div>
          </div>

          <div class="route-directions">
            <h3>Referencias visuales</h3>
            <div id="directions-content">
              <ol class="directions-list">
                <? if (!$connections || count($connections) < 1): ?>
                  <p>No hay instrucciones de llegada disponibles.</p>
                <? endif ?>

                <? foreach ($connections as $i => $path): ?>
                  <li>
                    <span class="direction-number"><?= $i + 1 ?></span>
                    <span class="direction-text">
                      Camina <?= $path["distance_m"] ?> metros, desde <?= $path["from_name"] ?> hasta <?= $path["to_name"] ?>
                    </span>
                  </li>
                <? endforeach ?>
              </ol>
            </div>
          </div>
        </section>
      </div>
    </article>

    <!-- Lugares relacionados -->
    <section class="related-places">
      <h2>Lugares relacionados</h2>
      <div id="related-grid" class="related-grid">
        <? if (count($relatedPlaces) === 0) : ?>
          <p class="no-related">No hay lugares relacionados disponibles.</p>
        <? else : ?>
          <? foreach ($relatedPlaces as $nearbyPlace): ?>
            <a href="/lugares/<?= $nearbyPlace["id"] ?>" class="related-card">
              <img src="../../public/images/placeholder.webp" alt="<?= $nearbyPlace["name"] ?>" class="related-image">
              <div class="related-info">
                <h3><?= $nearbyPlace["name"] ?></h3>
                <span class="related-category"><?= $nearbyPlace["type"]["name"] ?></span>
              </div>
            </a>
          <? endforeach ?>
        <? endif ?>
      </div>
    </section>

    <!-- Estado de error -->
    <div id="error-state" class="error-state" style="display: none;">
      <div class="error-icon">❌</div>
      <h2>Lugar no encontrado</h2>
      <p>El lugar que buscas no existe o ha sido eliminado.</p>
      <a href="/lugares" class="cta-button">Ver todos los lugares</a>
    </div>
  </main>

  <!-- FOOTER -->
  <? require 'components/layouts/footer.php' ?>
</body>

</html>
