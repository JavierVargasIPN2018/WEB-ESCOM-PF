<?php
require_once 'components/icons/favorite-icon.php';

?>

<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Lugares - Batiz Explorer</title>
  <link rel="stylesheet" href="styles/globals.css">
  <link rel="stylesheet" href="styles/responsive.css">
  <link rel="stylesheet" href="styles/favorites.css">
  <link rel="stylesheet" href="styles/places-card.css">
</head>

<body>
  <!-- HEADER -->
  <? require "components/layouts/header.php" ?>

  <!-- MAIN CONTENT -->
  <main class="main-content">
    <section class="categories-section">
      <h2>Lugares disponibles</h2>

      <div id="places-grid" class="categories-grid">
        <?php foreach ($places as $place) : ?>
          <?php
          $image = trim($place['image'] ?? '');
          ?>

          <article class="category-card">
            <a href="/lugares/<?= $place["id"] ?>">
              <div class="category-icon">
                <?php if (!empty($image)): ?>
                  <img
                    src="<?= htmlspecialchars("/" . $image); ?>"
                    alt="<?= htmlspecialchars($place['name']); ?>"
                    class="icon-image">
                <?php else: ?>
                  <img src="/public/images/placeholder.webp" alt="Icono de <?= $place["name"] ?>" class="icon-image">
                <?php endif; ?>

              </div>
              <h3><?= $place["name"] ?></h3>
              <p class="category-type"><?= $place["type"]["name"] ?></p>
            </a>
            <div class="card-favorite">
              <?php $isFavorite = in_array($place['id'], $favPlaceIds); ?>

              <form method="POST" action="/toggle-favorite" class="favorite-form">
                <input type="hidden" name="place_id" value="<?= $place["id"] ?>">
                <input type="hidden" name="redirect_url" value="<?= $_SERVER['REQUEST_URI'] ?>">
                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">

                <button type="submit" class="favorite-star card-star <?= $isFavorite ? 'active' : '' ?>"
                  title="<?= $isFavorite ? 'Eliminar de favoritos' : 'Agregar a favoritos' ?>">
                  <?= favoriteIcon() ?>
                </button>
              </form>
            </div>
          </article>
        <?php endforeach ?>
      </div>

      <div id="empty-places" class="empty-state" style="display: none;">
        <div class="empty-icon">📍</div>
        <h3>No hay lugares disponibles</h3>
        <p>Agrega lugares desde la sección de gestión</p>
      </div>
    </section>
  </main>

  <!-- FOOTER -->
  <? require "components/layouts/footer.php" ?>

</body>

</html>
