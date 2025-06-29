<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Gestión de Lugares - Batiz Explorer</title>
  <link rel="stylesheet" href="../css/styles.css">
  <link rel="stylesheet" href="../css/responsive.css">
  <link rel="stylesheet" href="../css/admin.css">
</head>

<body>
  <!-- HEADER -->
  <header class="main-header">
    <div class="header-left">
      <h1>Batiz Explorer</h1>
      <nav class="main-nav">
        <ul>
          <li><a href="index.html">Inicio</a></li>
          <li><a href="categorias.html">Categorías</a></li>
          <li><a href="favoritos.html">Favoritos</a></li>
          <li><a href="admin.html" class="active">Gestión</a></li>
        </ul>
      </nav>
    </div>
    <div class="auth-section">
      <div id="auth-container">
        <!-- Se cargará dinámicamente -->
      </div>
    </div>
  </header>

  <!-- MAIN CONTENT -->
  <main class="main-content">
    <div class="admin-layout">
      <!-- Formulario de lugar -->
      <section class="form-section">
        <div class="section-header">
          <h2 id="form-title">Agregar Nuevo Lugar</h2>
          <button id="cancel-edit" class="cancel-button" style="display: none;">Cancelar edición</button>
        </div>

        <form id="place-form" class="place-form">
          <input type="hidden" id="place-id">

          <div class="form-group">
            <label for="place-name">Nombre del sitio *</label>
            <input type="text" id="place-name" name="name" required>
          </div>

          <div class="form-group">
            <label for="place-category">Categoría *</label>
            <select id="place-category" name="category" required>
              <option value="">Seleccionar categoría</option>
              <option value="Aulas">Aulas</option>
              <option value="Laboratorios">Laboratorios</option>
              <option value="Biblioteca">Biblioteca</option>
              <option value="Cafetería">Cafetería</option>
              <option value="Áreas Deportivas">Áreas Deportivas</option>
              <option value="Servicios Administrativos">Servicios Administrativos</option>
            </select>
          </div>

          <div class="form-group">
            <label for="place-description">Descripción *</label>
            <textarea id="place-description" name="description" rows="4" required></textarea>
          </div>

          <div class="form-group">
            <label for="place-directions">Cómo llegar *</label>
            <textarea id="place-directions" name="directions" rows="3" required placeholder="Instrucciones paso a paso para llegar al lugar"></textarea>
          </div>

          <div class="form-group">
            <label>Amenidades</label>
            <div class="amenities-grid">
              <label class="amenity-item">
                <input type="checkbox" name="amenities" value="Wi-Fi">
                <span class="checkmark"></span>
                Wi-Fi
              </label>
              <label class="amenity-item">
                <input type="checkbox" name="amenities" value="Computadoras">
                <span class="checkmark"></span>
                Computadoras
              </label>
              <label class="amenity-item">
                <input type="checkbox" name="amenities" value="Aire acondicionado">
                <span class="checkmark"></span>
                Aire acondicionado
              </label>
              <label class="amenity-item">
                <input type="checkbox" name="amenities" value="Proyector">
                <span class="checkmark"></span>
                Proyector
              </label>
              <label class="amenity-item">
                <input type="checkbox" name="amenities" value="Acceso para discapacitados">
                <span class="checkmark"></span>
                Acceso para discapacitados
              </label>
              <label class="amenity-item">
                <input type="checkbox" name="amenities" value="Estacionamiento">
                <span class="checkmark"></span>
                Estacionamiento
              </label>
            </div>
          </div>

          <div class="form-group">
            <label for="place-maps-link">Link a Google Maps</label>
            <input type="url" id="place-maps-link" name="mapsLink" placeholder="https://maps.google.com/...">
          </div>

          <div class="form-actions">
            <button type="submit" class="submit-button">
              <span id="submit-text">Agregar Lugar</span>
            </button>
          </div>
        </form>
      </section>

      <!-- Lista de lugares -->
      <section class="places-section">
        <div class="section-header">
          <h2>Lugares Registrados</h2>
          <div class="places-stats">
            <span id="places-count">0 lugares</span>
          </div>
        </div>

        <div class="places-filters">
          <input type="text" id="search-places" placeholder="Buscar lugares..." class="search-input">
          <select id="filter-category" class="filter-select">
            <option value="">Todas las categorías</option>
            <option value="Aulas">Aulas</option>
            <option value="Laboratorios">Laboratorios</option>
            <option value="Biblioteca">Biblioteca</option>
            <option value="Cafetería">Cafetería</option>
            <option value="Áreas Deportivas">Áreas Deportivas</option>
            <option value="Servicios Administrativos">Servicios Administrativos</option>
          </select>
        </div>

        <div id="places-list" class="places-list">
          <!-- Los lugares se cargarán dinámicamente -->
        </div>

        <div id="empty-places" class="empty-state" style="display: none;">
          <div class="empty-icon">📍</div>
          <h3>No hay lugares registrados</h3>
          <p>Comienza agregando tu primer lugar usando el formulario</p>
        </div>
      </section>
    </div>
  </main>

  <!-- FOOTER -->
  <footer class="main-footer">
    <p>&copy; 2025 IPN</p>
  </footer>

  <!-- Modal de confirmación -->
  <div id="delete-modal" class="modal" style="display: none;">
    <div class="modal-content">
      <h3>Confirmar eliminación</h3>
      <p>¿Estás seguro de que quieres eliminar este lugar? Esta acción no se puede deshacer.</p>
      <div class="modal-actions">
        <button id="confirm-delete" class="delete-button">Eliminar</button>
        <button id="cancel-delete" class="cancel-button">Cancelar</button>
      </div>
    </div>
  </div>

  <script src="../js/places.js"></script>
  <script src="../js/admin.js"></script>
  <script src="../js/header-auth.js"></script>
</body>

</html>
