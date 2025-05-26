// Gestión de favoritos - localStorage
class FavoritesManager {
  constructor() {
    this.favorites = this.loadFavorites()
  }

  // Cargar favoritos desde localStorage
  loadFavorites() {
    const stored = localStorage.getItem("escom_favorites")
    return stored ? JSON.parse(stored) : []
  }

  // Guardar favoritos en localStorage
  saveFavorites() {
    localStorage.setItem("escom_favorites", JSON.stringify(this.favorites))
  }

  // Verificar si un lugar es favorito
  isFavorite(placeId) {
    return this.favorites.includes(placeId)
  }

  // Agregar a favoritos
  addFavorite(placeId) {
    if (!this.isFavorite(placeId)) {
      this.favorites.push(placeId)
      this.saveFavorites()
      return true
    }
    return false
  }

  // Remover de favoritos
  removeFavorite(placeId) {
    const index = this.favorites.indexOf(placeId)
    if (index > -1) {
      this.favorites.splice(index, 1)
      this.saveFavorites()
      return true
    }
    return false
  }

  // Toggle favorito
  toggleFavorite(placeId) {
    if (this.isFavorite(placeId)) {
      this.removeFavorite(placeId)
      return false
    } else {
      this.addFavorite(placeId)
      return true
    }
  }

  // Obtener lugares favoritos
  getFavoritePlaces() {
    const placesManager = window.placesManager
    return this.favorites.map((id) => placesManager.getPlaceById(id)).filter((place) => place !== undefined)
  }

  // Obtener IDs de favoritos
  getFavoriteIds() {
    return [...this.favorites]
  }
}

// Función para crear botón de favorito
function createFavoriteButton(placeId, className = "favorite-star") {
  const favoritesManager = new FavoritesManager()
  const button = document.createElement("button")
  button.className = className
  button.innerHTML = "⭐"
  button.setAttribute("data-place-id", placeId)

  // Establecer estado inicial
  if (favoritesManager.isFavorite(placeId)) {
    button.classList.add("active")
  }

  // Agregar evento click
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

      // Si estamos en la página de favoritos, recargar la lista
      if (window.location.pathname.includes("favoritos.html")) {
        window.loadFavorites() // Assuming loadFavorites is a global function
      }
    }
  })

  return button
}

// Función para mostrar notificaciones
function showNotification(message, type = "info") {
  // Crear elemento de notificación
  const notification = document.createElement("div")
  notification.className = `notification notification-${type}`
  notification.textContent = message

  // Estilos inline para la notificación
  Object.assign(notification.style, {
    position: "fixed",
    top: "20px",
    right: "20px",
    padding: "1rem 1.5rem",
    borderRadius: "8px",
    color: "white",
    fontWeight: "600",
    zIndex: "9999",
    transform: "translateX(100%)",
    transition: "transform 0.3s ease",
    backgroundColor: type === "success" ? "#28a745" : type === "error" ? "#dc3545" : "#007bff",
  })

  document.body.appendChild(notification)

  // Animar entrada
  setTimeout(() => {
    notification.style.transform = "translateX(0)"
  }, 100)

  // Remover después de 3 segundos
  setTimeout(() => {
    notification.style.transform = "translateX(100%)"
    setTimeout(() => {
      if (notification.parentNode) {
        notification.parentNode.removeChild(notification)
      }
    }, 300)
  }, 3000)
}

// Instancia global
window.favoritesManager = new FavoritesManager()

// Assuming loadFavorites is a global function
function loadFavorites() {
  // Implementation of loadFavorites
}
