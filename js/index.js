// index.js

document.addEventListener("DOMContentLoaded", () => {
  initStatsAndFilters();
  setupSearch();
  enableQuickActionsScroll();
});

/**
 * Inicializa los contadores de estadísticas y llena el filtro de categorías
 */
function initStatsAndFilters() {
  const places = window.placesManager.getAllPlaces();             // obtiene array de lugares
  const favCount = window.favoritesManager.getFavoriteIds().length;
  const categories = [...new Set(places.map(p => p.category))].sort();

  // Estadísticas
  document.getElementById("total-places").textContent      = places.length;
  document.getElementById("total-categories").textContent = categories.length;
  document.getElementById("total-favorites").textContent  = favCount;

  // Poblado del select de categorías
  const select = document.getElementById("category-filter");
  categories.forEach(cat => {
    const opt = document.createElement("option");
    opt.value       = cat;
    opt.textContent = cat;
    select.appendChild(opt);
  });
}

/**
 * Configura los eventos de búsqueda (botón, input y cambio de categoría)
 */
function setupSearch() {
  const input  = document.getElementById("search-input");
  const select = document.getElementById("category-filter");
  const btn    = document.getElementById("search-button");

  btn.addEventListener("click", doSearch);
  input.addEventListener("input", () => {
    if (input.value.trim() === "") clearResults();
    else doSearch();
  });
  select.addEventListener("change", doSearch);
}

/**
 * Realiza la búsqueda filtrando por término y categoría
 */
function doSearch() {
  const term     = document.getElementById("search-input").value.toLowerCase();
  const category = document.getElementById("category-filter").value;
  const places   = window.placesManager.getAllPlaces();

  const results = places.filter(p => {
    const matchesTerm = p.name.toLowerCase().includes(term) ||
                        (p.description && p.description.toLowerCase().includes(term));
    const matchesCat  = !category || p.category === category;
    return matchesTerm && matchesCat;
  });

  renderResults(results);
}

/**
 * Muestra los resultados en #results-container
 */
function renderResults(places) {
  const container = document.getElementById("results-container");
  container.innerHTML = "";

  if (places.length === 0) {
    container.innerHTML = `<p class="no-results">No se encontraron lugares.</p>`;
    return;
  }

  places.forEach(p => {
    const card = document.createElement("div");
    card.className = "result-card";
    card.innerHTML = `
      <h5>${p.name}</h5>
      <p>${p.category}</p>
      <button class="btn-view" data-id="${p.id}">Ver detalle</button>
      <button class="btn-fav" data-id="${p.id}">
        ${ window.favoritesManager.isFavorite(p.id) ? "💔 Quitar" : "❤️ Favorito" }
      </button>
    `;

    // Navegar a detalle
    card.querySelector(".btn-view").addEventListener("click", () => {
      window.location.href = `html/detalle.html?id=${p.id}`;
    });

    // Toggle favorito en línea
    card.querySelector(".btn-fav").addEventListener("click", e => {
      e.stopPropagation();
      const nowFav = window.favoritesManager.toggleFavorite(p.id);
      e.currentTarget.textContent = nowFav ? "💔 Quitar" : "❤️ Favorito";

      // Actualizar contador de favoritos en el header
      document.getElementById("total-favorites").textContent =
        window.favoritesManager.getFavoriteIds().length;

      // Notificación
      showNotification(
        nowFav ? "Agregado a favoritos" : "Removido de favoritos",
        nowFav ? "success" : "info"
      );
    });

    container.appendChild(card);
  });
}

/** Limpia los resultados de búsqueda */
function clearResults() {
  document.getElementById("results-container").innerHTML = "";
}

/**
 * Añade scroll horizontal al contenedor de accesos rápidos
 */
function enableQuickActionsScroll() {
  const wrapper = document.querySelector(".quick-actions .action-buttons");
  wrapper.style.overflowX  = "auto";
  wrapper.style.whiteSpace = "nowrap";

  wrapper.querySelectorAll("a").forEach(a => {
    a.style.display     = "inline-block";
    a.style.marginRight = "0.5rem";
  });
}
