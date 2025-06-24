<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Lugares - EScom Explorer</title>
  <link rel="stylesheet" href="styles/globals.css">
  <link rel="stylesheet" href="styles/responsive.css">
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
          <article class="category-card">
            <a href="/lugares/<?= $place["id"] ?>">
              <div class="category-icon">
                <?php if (!empty($dish['image']) && file_exists($dish['image'])): ?>
                  <img src="<?= htmlspecialchars($dish['image']); ?>"
                    alt="<?= htmlspecialchars($dish['name']); ?>"
                    class="w-full h-full object-cover">
                <?php else: ?>
                  <img src="/public/images/placeholder.webp" alt="Icono de <?= $place["name"] ?>" class="icon-image">
                <?php endif; ?>

              </div>
              <h3><?= $place["name"] ?></h3>
              <p class="category-type"><?= $place["type"]["name"] ?></p>
            </a>
            <div class="card-favorite">
              ${createFavoriteButton(place.id, "favorite-star card-star")}
            </div>
          </article>
        <?php endforeach ?>
      </div>

      <div id="empty-places" class="empty-state" style="display: none;">
        <div class="empty-icon">📍</div>
        <h3>No hay lugares disponibles</h3>
        <p>Agrega lugares desde la sección de gestión</p>
        <a href="admin.html" class="cta-button">Gestionar lugares</a>
      </div>
    </section>
  </main>

  <!-- FOOTER -->
  <? require "components/layouts/footer.php" ?>

  <!-- <script src="../js/places.js"></script> -->
  <script src="../js/favorites.js"></script>
  <!-- <script src="../js/categorias.js"></script> -->
  <script src="../js/header-auth.js"></script>
</body>

</html>
