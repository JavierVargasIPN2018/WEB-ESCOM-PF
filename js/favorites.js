// favorites.js

// ------------------------------
// Clase para gestionar favoritos
// ------------------------------
class FavoritesManager {
  constructor() {
    this.favorites = this.loadFavorites();
  }

  loadFavorites() {
    const stored = localStorage.getItem("escom_favorites");
    return stored ? JSON.parse(stored) : [];
  }

  saveFavorites() {
    localStorage.setItem("escom_favorites", JSON.stringify(this.favorites));
  }

  isFavorite(id) {
    return this.favorites.includes(id);
  }

  addFavorite(id) {
    if (!this.isFavorite(id)) {
      this.favorites.push(id);
      this.saveFavorites();
    }
  }

  removeFavorite(id) {
    const i = this.favorites.indexOf(id);
    if (i > -1) {
      this.favorites.splice(i, 1);
      this.saveFavorites();
    }
  }

  toggleFavorite(id) {
    if (this.isFavorite(id)) {
      this.removeFavorite(id);
      return false; // ahora NO es favorito
    } else {
      this.addFavorite(id);
      return true;  // ahora SÍ es favorito
    }
  }

  getFavoritePlaces() {
    return this.favorites
      .map(id => window.placesManager.getPlaceById(id))
      .filter(p => p);
  }
}

// Crear instancia global
window.favoritesManager = new FavoritesManager();


// ---------------------------------
// Función para mostrar notificaciones
// ---------------------------------
function showNotification(message, type = "info") {
  const notification = document.createElement("div");
  notification.className = `notification notification-${type}`;
  notification.textContent = message;

  Object.assign(notification.style, {
    position: "fixed",
    top: "20px",
    right: "20px",
    padding: "1rem 1.5rem",
    borderRadius: "12px",
    color: "white",
    fontWeight: "600",
    zIndex: "9999",
    transform: "translateX(100%)",
    transition: "transform 0.3s ease",
    backgroundColor:
      type === "success" ? "#2ECC71" :
      type === "error"   ? "#FF6B6B" :
      type === "info"    ? "#00C2FF" : "#0057FF",
    boxShadow: "0 4px 12px rgba(0, 0, 0, 0.15)",
  });

  document.body.appendChild(notification);

  setTimeout(() => {
    notification.style.transform = "translateX(0)";
  }, 100);

  setTimeout(() => {
    notification.style.transform = "translateX(100%)";
    setTimeout(() => {
      notification.remove();
    }, 300);
  }, 3000);
}


// ---------------------------------
// Carga inicial y configuración
// ---------------------------------
document.addEventListener("DOMContentLoaded", () => {
  loadFavorites();
  setupFilters();

  // Escuchar cambios desde otras pestañas
  window.addEventListener("storage", e => {
    if (e.key === "escom_favorites") loadFavorites();
  });

  // Escuchar evento personalizado
  window.addEventListener("favoritesChanged", loadFavorites);
});


// ------------------------------
// Función principal: renderizar
// ------------------------------
function loadFavorites() {
  const favMgr = window.favoritesManager;
  const favPlaces = favMgr.getFavoritePlaces();
  const container = document.getElementById("favorites-container");
  const emptyState = document.getElementById("empty-favorites");

  updateStats(favPlaces);
  updateCategoryFilter(favPlaces);

  if (favPlaces.length === 0) {
    container.innerHTML = "";
    emptyState.style.display = "block";
    return;
  }
  emptyState.style.display = "none";

  container.innerHTML = favPlaces
    .map((place, idx) => {
      if (!place) return "";
      const amenitiesHTML = place.amenities && place.amenities.length
        ? `<div class="favorite-amenities">
            ${place.amenities.slice(0,3).map(a => `<span class="amenity-tag">${a}</span>`).join("")}
            ${place.amenities.length > 3 ? `<span class="amenity-tag">+${place.amenities.length-3} más</span>` : ""}
           </div>`
        : "";
      const mapsLinkHTML = place.mapsLink?.trim()
        ? `<a href="${place.mapsLink}" target="_blank" class="action-link view-maps"
              onclick="event.stopPropagation()">Maps</a>`
        : "";
      return `
        <div class="favorite-card"
             style="animation-delay: ${idx * 0.1}s"
             data-place-id="${place.id}"
             data-category="${place.category}"
             data-name="${place.name.toLowerCase()}">

          <div class="favorite-image">
            <div class="favorite-badge">${place.category}</div>
            <div class="favorite-star-container">
              <button class="favorite-star active" data-place-id="${place.id}">⭐</button>
            </div>
          </div>

          <div class="favorite-content">
            <div class="favorite-header">
              <h3 class="favorite-title">${place.name}</h3>
              <div class="favorite-location">EScom – ${place.category}</div>
            </div>

            <p class="favorite-description">${place.description}</p>
            ${amenitiesHTML}

            <div class="favorite-footer">
              <div class="favorite-actions">${mapsLinkHTML}</div>
              <div class="click-indicator">Click para ver detalles</div>
            </div>
          </div>
        </div>`;
    })
    .filter(html => html)
    .join("");

  setupCardEvents();
  setupFavoriteButtons();
}


// -----------------------
// Eventos en las tarjetas
// -----------------------
function setupCardEvents() {
  document.querySelectorAll(".favorite-card").forEach(card => {
    card.addEventListener("click", e => {
      if (e.target.closest(".favorite-star") || e.target.closest(".action-link")) return;
      const id = card.dataset.placeId;
      card.style.transform = "scale(0.98)";
      setTimeout(() => window.location.href = `detalle.html?id=${id}`, 150);
    });
    card.addEventListener("mouseenter", () => {
      card.style.cursor = "pointer";
    });
  });
}


// --------------------------
// Botones de “toggle favorite”
// --------------------------
function setupFavoriteButtons() {
  const favMgr = window.favoritesManager;
  document.querySelectorAll(".favorite-star").forEach(btn => {
    btn.addEventListener("click", e => {
      e.preventDefault();
      e.stopPropagation();

      const id = btn.dataset.placeId;
      const nowFav = favMgr.toggleFavorite(id);

      // Sincronizar
      window.dispatchEvent(new Event("favoritesChanged"));

      btn.classList.toggle("active", nowFav);
      showNotification(
        nowFav ? "Agregado a favoritos" : "Removido de favoritos",
        nowFav ? "success" : "info"
      );

      if (!nowFav) {
        const card = btn.closest(".favorite-card");
        if (card) {
          card.classList.add("removing");
          setTimeout(loadFavorites, 400);
        }
      }
    });
  });
}


// -----------------
// Filtros y búsqueda
// -----------------
function setupFilters() {
  document.getElementById("category-filter")
    .addEventListener("change", filterFavorites);
  document.getElementById("search-filter")
    .addEventListener("input", filterFavorites);
}

function filterFavorites() {
  const cat = document.getElementById("category-filter").value;
  const term = document.getElementById("search-filter").value.toLowerCase();
  const cards = document.querySelectorAll(".favorite-card");

  cards.forEach(card => {
    const matchesCat  = !cat || card.dataset.category === cat;
    const matchesTerm = !term || card.dataset.name.includes(term);
    card.style.display = (matchesCat && matchesTerm) ? "block" : "none";
    if (matchesCat && matchesTerm) {
      card.style.animation = "fadeInUp 0.3s ease-out";
    }
  });

  const visible = Array.from(cards).filter(c => c.style.display !== "none");
  if (!visible.length && cards.length) {
    if (!document.getElementById("no-results")) {
      const noRes = document.createElement("div");
      noRes.id = "no-results";
      noRes.className = "empty-state";
      noRes.innerHTML = `
        <div class="empty-icon">🔍</div>
        <h3>No se encontraron resultados</h3>
        <p>Intenta con otros términos</p>`;
      document.getElementById("favorites-container").append(noRes);
    }
  } else {
    const noRes = document.getElementById("no-results");
    if (noRes) noRes.remove();
  }
}


// ---------------------------
// Estadísticas dinámicas
// ---------------------------
function updateStats(favs) {
  animateCounter(document.getElementById("favorites-count"), favs.length);
  animateCounter(
    document.getElementById("categories-count"),
    new Set(favs.map(p => p.category)).size
  );
}

function animateCounter(el, target) {
  const current = parseInt(el.textContent) || 0;
  const step = target > current ? 1 : -1;
  const frames = Math.abs(target - current);
  const interval = frames ? 500 / frames : 0;
  let val = current;
  const timer = setInterval(() => {
    val += step;
    el.textContent = val;
    if (val === target) clearInterval(timer);
  }, interval);
}


// -------------------------------------------------
// Inyección de animaciones CSS para tarjetas/remoción
// -------------------------------------------------
const style = document.createElement("style");
style.textContent = `
  @keyframes fadeInUp { from{opacity:0;transform:translateY(20px);} to{opacity:1;transform:translateY(0);} }
  @keyframes fadeOutUp{ from{opacity:1;transform:translateY(0);} to{opacity:0;transform:translateY(-20px);} }
  .favorite-card.removing{ animation: fadeOutUp 0.4s forwards; }
`;
document.head.append(style);


// --------------------
// Exponer funcionalidades
// --------------------
window.loadFavorites = loadFavorites;
