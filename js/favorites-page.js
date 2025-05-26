// Gestión específica de la página de favoritos
document.addEventListener("DOMContentLoaded", () => {
  loadFavorites()
})

function loadFavorites() {
  const favoritesManager = window.favoritesManager
  const favoritePlaces = favoritesManager.getFavoritePlaces()
  const container = document.getElementById("favorites-container")
  const emptyState = document.getElementById("empty-favorites")

  if (favoritePlaces.length === 0) {
    container.innerHTML = ""
    emptyState.style.display = "block"
    return
  }

  emptyState.style.display = "none"

  container.innerHTML = favoritePlaces
    .map(
      (place) => `
    <div class="favorite-card">
      <div class="favorite-image">
        <div class="favorite-badge">${place.category}</div>
      </div>
      <div class="favorite-content">
        <div class="favorite-header">
          <h3 class="favorite-title">${place.name}</h3>
          ${createFavoriteButton(place.id, "favorite-star")}
        </div>
        <p class="favorite-description">${place.description}</p>
        ${
          place.amenities.length > 0
            ? `
          <div class="favorite-amenities">
            ${place.amenities.map((amenity) => `<span class="amenity-tag">${amenity}</span>`).join("")}
          </div>
        `
            : ""
        }
        <div class="favorite-actions">
          <a href="detalle.html?id=${place.id}" class="action-link view-details">Ver detalles</a>
          ${place.mapsLink ? `<a href="${place.mapsLink}" target="_blank" class="action-link view-maps">Ver en Maps</a>` : ""}
        </div>
      </div>
    </div>
  `,
    )
    .join("")

  // Reactivar eventos de favoritos
  document.querySelectorAll(".favorite-star").forEach((button) => {
    const placeId = button.getAttribute("data-place-id")

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
        // Recargar favoritos para actualizar la vista
        setTimeout(() => loadFavorites(), 300)
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
