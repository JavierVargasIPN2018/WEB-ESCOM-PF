// Gestión de la página de administración
document.addEventListener("DOMContentLoaded", () => {
  const placesManager = window.placesManager
  const form = document.getElementById("place-form")
  const placesList = document.getElementById("places-list")
  const searchInput = document.getElementById("search-places")
  const categoryFilter = document.getElementById("filter-category")
  const deleteModal = document.getElementById("delete-modal")

  let editingPlaceId = null
  let placeToDelete = null

  // Función para mostrar notificaciones
  function showNotification(message, type) {
    alert(message) // Implementación simple de notificación
  }

  // Cargar lugares al iniciar
  loadPlaces()
  updatePlacesCount()

  // Evento de envío del formulario
  form.addEventListener("submit", (e) => {
    e.preventDefault()

    const formData = new FormData(form)
    const amenities = Array.from(formData.getAll("amenities"))

    const placeData = {
      name: formData.get("name"),
      category: formData.get("category"),
      description: formData.get("description"),
      directions: formData.get("directions"),
      amenities: amenities,
      mapsLink: formData.get("mapsLink") || "",
    }

    if (editingPlaceId) {
      // Actualizar lugar existente
      placesManager.updatePlace(editingPlaceId, placeData)
      showNotification("Lugar actualizado correctamente", "success")
      cancelEdit()
    } else {
      // Crear nuevo lugar
      placesManager.createPlace(placeData)
      showNotification("Lugar agregado correctamente", "success")
    }

    form.reset()
    loadPlaces()
    updatePlacesCount()
  })

  // Eventos de búsqueda y filtrado
  searchInput.addEventListener("input", filterPlaces)
  categoryFilter.addEventListener("change", filterPlaces)

  // Evento para cancelar edición
  document.getElementById("cancel-edit").addEventListener("click", cancelEdit)

  // Eventos del modal de eliminación
  document.getElementById("confirm-delete").addEventListener("click", confirmDelete)
  document.getElementById("cancel-delete").addEventListener("click", cancelDelete)

  // Función para cargar y mostrar lugares
  function loadPlaces() {
    const places = placesManager.getAllPlaces()
    renderPlaces(places)
  }

  // Función para filtrar lugares
  function filterPlaces() {
    const query = searchInput.value
    const category = categoryFilter.value
    const filteredPlaces = placesManager.searchPlaces(query, category)
    renderPlaces(filteredPlaces)
  }

  // Función para renderizar lugares
  function renderPlaces(places) {
    const emptyState = document.getElementById("empty-places")

    if (places.length === 0) {
      placesList.innerHTML = ""
      emptyState.style.display = "block"
      return
    }

    emptyState.style.display = "none"

    placesList.innerHTML = places
      .map(
        (place) => `
      <div class="place-item" data-place-id="${place.id}">
        <div class="place-header">
          <div class="place-info">
            <h3>${place.name}</h3>
            <span class="place-category">${place.category}</span>
          </div>
          <div class="place-actions">
            <button class="edit-button" onclick="editPlace('${place.id}')">Editar</button>
            <button class="delete-button" onclick="deletePlace('${place.id}')">Eliminar</button>
          </div>
        </div>
        <p class="place-description">${place.description}</p>
        ${
          place.amenities.length > 0
            ? `
          <div class="place-amenities">
            ${place.amenities.map((amenity) => `<span class="amenity-tag">${amenity}</span>`).join("")}
          </div>
        `
            : ""
        }
      </div>
    `,
      )
      .join("")
  }

  // Función para editar lugar
  window.editPlace = (placeId) => {
    const place = placesManager.getPlaceById(placeId)
    if (!place) return

    editingPlaceId = placeId

    // Llenar formulario con datos del lugar
    document.getElementById("place-id").value = place.id
    document.getElementById("place-name").value = place.name
    document.getElementById("place-category").value = place.category
    document.getElementById("place-description").value = place.description
    document.getElementById("place-directions").value = place.directions
    document.getElementById("place-maps-link").value = place.mapsLink || ""

    // Marcar amenidades
    const amenityCheckboxes = document.querySelectorAll('input[name="amenities"]')
    amenityCheckboxes.forEach((checkbox) => {
      checkbox.checked = place.amenities.includes(checkbox.value)
    })

    // Cambiar UI para modo edición
    document.getElementById("form-title").textContent = "Editar Lugar"
    document.getElementById("submit-text").textContent = "Actualizar Lugar"
    document.getElementById("cancel-edit").style.display = "block"

    // Scroll al formulario
    document.querySelector(".form-section").scrollIntoView({ behavior: "smooth" })
  }

  // Función para cancelar edición
  function cancelEdit() {
    editingPlaceId = null
    form.reset()
    document.getElementById("form-title").textContent = "Agregar Nuevo Lugar"
    document.getElementById("submit-text").textContent = "Agregar Lugar"
    document.getElementById("cancel-edit").style.display = "none"
  }

  // Función para eliminar lugar
  window.deletePlace = (placeId) => {
    placeToDelete = placeId
    deleteModal.style.display = "flex"
  }

  // Confirmar eliminación
  function confirmDelete() {
    if (placeToDelete) {
      placesManager.deletePlace(placeToDelete)
      showNotification("Lugar eliminado correctamente", "success")
      loadPlaces()
      updatePlacesCount()
      placeToDelete = null
    }
    deleteModal.style.display = "none"
  }

  // Cancelar eliminación
  function cancelDelete() {
    placeToDelete = null
    deleteModal.style.display = "none"
  }

  // Actualizar contador de lugares
  function updatePlacesCount() {
    const count = placesManager.getAllPlaces().length
    document.getElementById("places-count").textContent = `${count} lugar${count !== 1 ? "es" : ""}`
  }

  // Cerrar modal al hacer click fuera
  deleteModal.addEventListener("click", (e) => {
    if (e.target === deleteModal) {
      cancelDelete()
    }
  })
})
