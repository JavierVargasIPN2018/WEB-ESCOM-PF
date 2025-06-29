// Gestión de la página de detalle
document.addEventListener("DOMContentLoaded", () => {
  const placesManager = window.placesManager
  const favoritesManager = window.favoritesManager

  // Obtener ID del lugar desde la URL
  const urlParams = new URLSearchParams(window.location.search)
  const placeId = urlParams.get("id")

  if (!placeId) {
    showErrorState()
    return
  }

  // Cargar datos del lugar
  loadPlaceDetails(placeId)
  loadRelatedPlaces(placeId)

  // Configurar botón de favoritos
  setupFavoriteButton(placeId)

  // Configurar botón de compartir
  setupShareButton()
})

function loadPlaceDetails(placeId) {
  const placesManager = window.placesManager
  const place = placesManager.getPlaceById(placeId)

  if (!place) {
    showErrorState()
    return
  }

  // Actualizar título de la página
  document.title = `${place.name} - EScom Explorer`

  // Actualizar breadcrumb
  document.getElementById("breadcrumb-place").textContent = place.name

  // Actualizar hero section
  document.getElementById("place-badge").textContent = place.category
  document.getElementById("place-title").textContent = place.name
  document.getElementById("place-image").alt = `Imagen de ${place.name}`

  // Actualizar descripción
  document.getElementById("place-description").textContent = place.description

  // Actualizar características/amenidades
  updateFeatures(place.amenities)

  // Actualizar instrucciones de llegada
  updateDirections(place.directions)

  // Actualizar enlace de Google Maps
  updateMapsLink(place.mapsLink)
}

function updateFeatures(amenities) {
  const featuresContainer = document.getElementById("place-features")

  if (!amenities || amenities.length === 0) {
    featuresContainer.innerHTML = '<p class="no-features">No hay amenidades registradas</p>'
    return
  }

  // Mapeo de amenidades a iconos
  const amenityIcons = {
    "Wi-Fi": "W",
    Computadoras: "PC",
    "Aire acondicionado": "AC",
    Proyector: "P",
    "Acceso para discapacitados": "AD",
    Estacionamiento: "E",
    "Préstamo de libros": "L",
    "Horario extendido": "H",
  }

  featuresContainer.innerHTML = amenities
    .map(
      (amenity) => `
    <div class="feature">
      <div class="feature-icon">${amenityIcons[amenity] || "A"}</div>
      <span class="feature-text">${amenity}</span>
    </div>
  `,
    )
    .join("")
}

function updateDirections(directions) {
  const directionsContainer = document.getElementById("directions-content")

  if (!directions) {
    directionsContainer.innerHTML = "<p>No hay instrucciones de llegada disponibles.</p>"
    return
  }

  // Dividir las instrucciones por puntos o saltos de línea
  const steps = directions.split(/[.]\s+/).filter((step) => step.trim().length > 0)

  if (steps.length > 1) {
    // Si hay múltiples pasos, mostrar como lista numerada
    directionsContainer.innerHTML = `
      <ol class="directions-list">
        ${steps
          .map(
            (step) => `
          <li>
            <span class="direction-number">${steps.indexOf(step) + 1}</span>
            <span class="direction-text">${step.trim()}${step.trim().endsWith(".") ? "" : "."}</span>
          </li>
        `,
          )
          .join("")}
      </ol>
    `
  } else {
    // Si es un solo párrafo, mostrar como texto
    directionsContainer.innerHTML = `<p class="single-direction">${directions}</p>`
  }
}

function updateMapsLink(mapsLink) {
  const mapsButton = document.getElementById("maps-link")

  if (mapsLink && mapsLink.trim() !== "") {
    mapsButton.href = mapsLink
    mapsButton.style.display = "block"
    mapsButton.target = "_blank"
  } else {
    mapsButton.style.display = "none"
  }
}

function setupFavoriteButton(placeId) {
  const favoritesManager = window.favoritesManager
  const favoriteButton = document.getElementById("favorite-button")
  const favoriteText = document.getElementById("favorite-text")

  // Establecer estado inicial
  updateFavoriteButtonState(placeId)

  // Agregar evento click
  favoriteButton.addEventListener("click", () => {
    const isNowFavorite = favoritesManager.toggleFavorite(placeId)
    updateFavoriteButtonState(placeId)

    if (isNowFavorite) {
      showNotification("¡Agregado a favoritos!", "success")
      // Efecto visual de éxito
      favoriteButton.style.transform = "scale(1.1)"
      setTimeout(() => {
        favoriteButton.style.transform = ""
      }, 200)
    } else {
      showNotification("Removido de favoritos", "info")
    }
  })
}

function updateFavoriteButtonState(placeId) {
  const favoritesManager = window.favoritesManager
  const favoriteButton = document.getElementById("favorite-button")
  const favoriteText = document.getElementById("favorite-text")

  if (favoritesManager.isFavorite(placeId)) {
    favoriteButton.classList.add("active")
    favoriteText.textContent = "En favoritos"
  } else {
    favoriteButton.classList.remove("active")
    favoriteText.textContent = "Agregar a favoritos"
  }
}

function setupShareButton() {
  const shareButton = document.getElementById("share-button")

  shareButton.addEventListener("click", () => {
    // Copiar URL al portapapeles
    const url = window.location.href

    if (navigator.share) {
      // Usar Web Share API si está disponible
      navigator
        .share({
          title: document.getElementById("place-title").textContent,
          text: document.getElementById("place-description").textContent,
          url: url,
        })
        .then(() => {
          showNotification("Ubicación compartida", "success")
        })
        .catch(() => {
          fallbackShare(url)
        })
    } else {
      fallbackShare(url)
    }
  })
}

function fallbackShare(url) {
  // Fallback: copiar al portapapeles
  if (navigator.clipboard) {
    navigator.clipboard
      .writeText(url)
      .then(() => {
        showNotification("Enlace copiado al portapapeles", "success")
      })
      .catch(() => {
        showNotification("No se pudo copiar el enlace", "error")
      })
  } else {
    // Fallback más básico
    const textArea = document.createElement("textarea")
    textArea.value = url
    document.body.appendChild(textArea)
    textArea.select()
    try {
      document.execCommand("copy")
      showNotification("Enlace copiado al portapapeles", "success")
    } catch (err) {
      showNotification("No se pudo copiar el enlace", "error")
    }
    document.body.removeChild(textArea)
  }
}

function loadRelatedPlaces(currentPlaceId) {
  const placesManager = window.placesManager
  const currentPlace = placesManager.getPlaceById(currentPlaceId)
  const allPlaces = placesManager.getAllPlaces()

  // Filtrar lugares relacionados (misma categoría, excluyendo el actual)
  const relatedPlaces = allPlaces
    .filter((place) => place.id !== currentPlaceId && place.category === currentPlace.category)
    .slice(0, 3) // Máximo 3 lugares relacionados

  const relatedGrid = document.getElementById("related-grid")

  if (relatedPlaces.length === 0) {
    relatedGrid.innerHTML = '<p class="no-related">No hay lugares relacionados disponibles.</p>'
    return
  }

  relatedGrid.innerHTML = relatedPlaces
    .map(
      (place) => `
    <a href="detalle.html?id=${place.id}" class="related-card">
      <img src="../img/placeholder.jpg" alt="${place.name}" class="related-image">
      <div class="related-info">
        <h3>${place.name}</h3>
        <span class="related-category">${place.category}</span>
      </div>
    </a>
  `,
    )
    .join("")
}

function showErrorState() {
  // Ocultar contenido principal
  document.querySelector(".place-detail").style.display = "none"
  document.querySelector(".related-places").style.display = "none"

  // Mostrar estado de error
  document.getElementById("error-state").style.display = "block"

  // Actualizar título
  document.title = "Lugar no encontrado - EScom Explorer"
  document.getElementById("breadcrumb-place").textContent = "No encontrado"
}

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
    backgroundColor:
      type === "success" ? "#28a745" : type === "error" ? "#dc3545" : type === "info" ? "#17a2b8" : "#007bff",
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
