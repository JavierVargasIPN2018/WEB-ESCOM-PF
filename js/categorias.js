// Gestión de la página de categorías
document.addEventListener("DOMContentLoaded", () => {
  const placesManager = window.placesManager
  const placesGrid = document.getElementById("places-grid")
  const emptyState = document.getElementById("empty-places")

  loadPlaces()

  function loadPlaces() {
    const places = placesManager.getAllPlaces()

    if (places.length === 0) {
      placesGrid.innerHTML = ""
      emptyState.style.display = "block"
      return
    }

    emptyState.style.display = "none"

    placesGrid.innerHTML = places
      .map(
        (place) => `
      <article class="category-card">
        <a href="detalle.html?id=${place.id}">
          <div class="category-icon">
            <img src="../img/placeholder.jpg" alt="Icono de ${place.name}" class="icon-image">
          </div>
          <h3>${place.name}</h3>
          <p class="category-type">${place.category}</p>
        </a>
        <div class="card-favorite">
          ${createFavoriteButton(place.id, "favorite-star card-star")}
        </div>
      </article>
    `,
      )
      .join("")

    // Reactivar eventos de favoritos
    document.querySelectorAll(".favorite-star").forEach((button) => {
      const placeId = button.getAttribute("data-place-id")
      const favoritesManager = window.favoritesManager

      if (favoritesManager.isFavorite(placeId)) {
        button.classList.add("active")
      }

      button.addEventListener("click", (e) => {
        e.preventDefault()
        e.stopPropagation()

        const isNowFavorite = favoritesManager.toggleFavorite(placeId)

        if (isNowFavorite) {
          button.classList.add("active")
          showNotification("Agregado a favoritos", "success")
        } else {
          button.classList.remove("active")
          showNotification("Removido de favoritos", "info")
        }
      })
    })
  }

  function createFavoriteButton(placeId, className) {
    const button = document.createElement("button")
    button.className = className
    button.setAttribute("data-place-id", placeId)
    button.innerHTML = '<i class="fas fa-star"></i>'
    return button
  }

  function showNotification(message, type) {
    const notification = document.createElement("div")
    notification.className = `notification ${type}`
    notification.textContent = message
    document.body.appendChild(notification)

    setTimeout(() => {
      document.body.removeChild(notification)
    }, 3000)
  }
})

// Estilos adicionales para las tarjetas con favoritos
const additionalStyles = `
  .category-card {
    position: relative;
  }
  
  .card-favorite {
    position: absolute;
    top: 1rem;
    right: 1rem;
    z-index: 2;
  }
  
  .card-star {
    background: rgba(255, 255, 255, 0.9);
    border: none;
    border-radius: 50%;
    width: 35px;
    height: 35px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.2rem;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
  }
  
  .card-star:hover {
    transform: scale(1.1);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
  }
  
  .card-star.active {
    color: #ffd700;
    background: rgba(255, 215, 0, 0.1);
  }
  
  .category-type {
    font-size: 0.9rem;
    color: var(--text-muted);
    margin-top: 0.5rem;
  }
`

// Inyectar estilos adicionales
const styleSheet = document.createElement("style")
styleSheet.textContent = additionalStyles
document.head.appendChild(styleSheet)
