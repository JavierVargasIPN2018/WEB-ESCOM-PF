<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($place['name']) ?> - EScom Explorer</title>
  <link rel="stylesheet" href="/styles/globals.css">
  <link rel="stylesheet" href="/styles/responsive.css">
  <link rel="stylesheet" href="/styles/place-detail.css">
</head>

<body>
  <!-- HEADER -->
  <?php require "components/layouts/header.php" ?>

  <!-- MAIN CONTENT -->
  <main class="main-content">
    <!-- Breadcrumb -->
    <nav class="breadcrumb">
      <a href="/">Inicio</a>
      <span class="breadcrumb-separator">›</span>
      <a href="/lugares">Lugares</a>
      <span class="breadcrumb-separator">›</span>
      <span class="current"><?= htmlspecialchars($place['name']) ?></span>
    </nav>

    <!-- Place Header -->
    <section class="place-header">
      <div class="place-hero">
        <div class="place-icon" style="background-color: <?= htmlspecialchars($place['type_color']) ?>">
          <img src="/public/images/icons/<?= htmlspecialchars($place['type_icon']) ?>.svg"
            alt="Icono de <?= htmlspecialchars($place['type_name']) ?>"
            class="icon-image">
        </div>
        <div class="place-info">
          <h1><?= htmlspecialchars($place['name']) ?></h1>
          <div class="place-meta">
            <span class="place-type"><?= htmlspecialchars($place['type_name']) ?></span>
            <?php if ($place['short_code']): ?>
              <span class="place-code"><?= htmlspecialchars($place['short_code']) ?></span>
            <?php endif; ?>
            <?php if ($place['building']): ?>
              <span class="place-building"><?= htmlspecialchars($place['building']) ?></span>
            <?php endif; ?>
          </div>
          <div class="place-actions">
            <button class="favorite-btn" data-place-id="<?= $place['id'] ?>">
              <span class="favorite-icon">☆</span>
              Agregar a favoritos
            </button>
            <button class="share-btn">
              <span class="share-icon">📤</span>
              Compartir
            </button>
          </div>
        </div>
      </div>
    </section>

    <!-- Place Details -->
    <section class="place-details">
      <div class="details-grid">
        <!-- Information Card -->
        <div class="detail-card">
          <h2>Información</h2>
          <div class="info-grid">
            <?php if ($place['description']): ?>
              <div class="info-item">
                <span class="info-label">Descripción:</span>
                <span class="info-value"><?= htmlspecialchars($place['description']) ?></span>
              </div>
            <?php endif; ?>

            <?php if ($place['capacity']): ?>
              <div class="info-item">
                <span class="info-label">Capacidad:</span>
                <span class="info-value"><?= $place['capacity'] ?> personas</span>
              </div>
            <?php endif; ?>

            <div class="info-item">
              <span class="info-label">Piso:</span>
              <span class="info-value">
                <?php
                $floorLevel = $place['floor_level'];
                if ($floorLevel == 0) {
                  echo 'Planta baja';
                } elseif ($floorLevel == 1) {
                  echo '1er piso';
                } elseif ($floorLevel == 2) {
                  echo '2do piso';
                } elseif ($floorLevel == 3) {
                  echo '3er piso';
                } else {
                  echo $floorLevel . 'º piso';
                }
                ?>
              </span>
            </div>

            <div class="info-item">
              <span class="info-label">Accesible:</span>
              <span class="info-value accessibility-<?= $place['is_accessible'] ? 'yes' : 'no' ?>">
                <?= $place['is_accessible'] ? '✓ Sí' : '✗ No' ?>
              </span>
            </div>
          </div>
        </div>

        <!-- Connections Card -->
        <?php if (!empty($connections)): ?>
          <div class="detail-card">
            <h2>Conexiones directas</h2>
            <div class="connections-list">
              <?php foreach ($connections as $connection): ?>
                <div class="connection-item">
                  <div class="connection-place">
                    <div class="connection-icon" style="background-color: <?= htmlspecialchars($connection['to_place_color']) ?>">
                      <img src="/public/images/icons/<?= htmlspecialchars($connection['to_place_icon']) ?>.svg"
                        alt="<?= htmlspecialchars($connection['to_place_type']) ?>"
                        class="icon-image">
                    </div>
                    <div class="connection-info">
                      <h4>
                        <a href="/lugares/<?= $connection['to_place_id'] ?>">
                          <?= htmlspecialchars($connection['to_place_name']) ?>
                        </a>
                      </h4>
                      <span class="connection-type"><?= htmlspecialchars($connection['to_place_type']) ?></span>
                      <?php if ($connection['to_place_building']): ?>
                        <span class="connection-building"><?= htmlspecialchars($connection['to_place_building']) ?></span>
                      <?php endif; ?>
                    </div>
                  </div>
                  <div class="connection-details">
                    <span class="connection-distance"><?= number_format($connection['distance_m'], 1) ?>m</span>
                    <?php if ($connection['travel_time_minutes']): ?>
                      <span class="connection-time"><?= number_format($connection['travel_time_minutes'], 1) ?> min</span>
                    <?php endif; ?>
                    <span class="connection-method"><?= ucfirst($connection['connection_type']) ?></span>
                  </div>
                </div>
              <?php endforeach; ?>
            </div>
          </div>
        <?php endif; ?>

        <!-- Nearby Places Card -->
        <?php if (!empty($nearbyPlaces) && count($nearbyPlaces) > 1): ?>
          <div class="detail-card">
            <h2>Lugares cercanos</h2>
            <div class="nearby-grid">
              <?php foreach (array_slice($nearbyPlaces, 1, 6) as $nearby): ?>
                <div class="nearby-item">
                  <a href="/lugares/<?= $nearby['id'] ?>">
                    <div class="nearby-icon" style="background-color: <?= htmlspecialchars($nearby['type_color']) ?>">
                      <img src="/public/images/icons/<?= htmlspecialchars($nearby['type_icon']) ?>.svg"
                        alt="<?= htmlspecialchars($nearby['type_name']) ?>"
                        class="icon-image">
                    </div>
                    <div class="nearby-info">
                      <h4><?= htmlspecialchars($nearby['name']) ?></h4>
                      <span class="nearby-distance"><?= number_format($nearby['distance'], 1) ?>m</span>
                    </div>
                  </a>
                </div>
              <?php endforeach; ?>
            </div>
          </div>
        <?php endif; ?>
      </div>
    </section>

    <!-- Map Section (if coordinates available) -->
    <?php if ($place['position_x'] && $place['position_y']): ?>
      <section class="place-map">
        <h2>Ubicación</h2>
        <div class="map-container">
          <div class="map-placeholder">
            <div class="map-marker" style="left: <?= ($place['position_x'] / 50 * 100) ?>%; top: <?= ($place['position_y'] / 50 * 100) ?>%;">
              <div class="marker-icon" style="background-color: <?= htmlspecialchars($place['type_color']) ?>">
                📍
              </div>
            </div>
            <p>Coordenadas: (<?= number_format($place['position_x'], 2) ?>, <?= number_format($place['position_y'], 2) ?>)</p>
          </div>
        </div>
      </section>
    <?php endif; ?>
  </main>

  <!-- FOOTER -->
  <?php require "components/layouts/footer.php" ?>

  <script src="/js/favorites.js"></script>
  <script src="/js/place-detail.js"></script>
  <script src="/js/header-auth.js"></script>
</body>

</html>
