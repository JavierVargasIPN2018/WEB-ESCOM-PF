// Gestión de lugares - localStorage
class PlacesManager {
  constructor() {
    this.places = this.loadPlaces()
  }

  // Cargar lugares desde localStorage
  loadPlaces() {
    const stored = localStorage.getItem("escom_places")
    if (stored) {
      return JSON.parse(stored)
    }

    // Datos de ejemplo si no hay lugares guardados
    const defaultPlaces = [
      {
        id: "1",
        name: "Biblioteca Central",
        category: "Biblioteca",
        description:
          "La Biblioteca Central de ESCOM ofrece un amplio espacio para estudio, consulta de material bibliográfico y acceso a recursos digitales.",
        directions:
          "Desde la entrada principal, avanza hacia el edificio A. Gira a la derecha en el pasillo principal. Sube al segundo piso por las escaleras centrales.",
        amenities: ["Wi-Fi", "Computadoras", "Aire acondicionado"],
        mapsLink: "https://maps.google.com",
        createdAt: new Date().toISOString(),
      },
      {
        id: "2",
        name: "Laboratorio de Redes",
        category: "Laboratorios",
        description:
          "Laboratorio especializado en redes de computadoras con equipos de última generación para prácticas de networking.",
        directions: "Edificio B, tercer piso. Tomar el elevador hasta el piso 3 y caminar hacia el ala este.",
        amenities: ["Wi-Fi", "Computadoras", "Proyector", "Aire acondicionado"],
        mapsLink: "https://maps.google.com",
        createdAt: new Date().toISOString(),
      },
    ]

    this.savePlaces(defaultPlaces)
    return defaultPlaces
  }

  // Guardar lugares en localStorage
  savePlaces(places = this.places) {
    localStorage.setItem("escom_places", JSON.stringify(places))
    this.places = places
  }

  // Obtener todos los lugares
  getAllPlaces() {
    return this.places
  }

  // Obtener lugar por ID
  getPlaceById(id) {
    return this.places.find((place) => place.id === id)
  }

  // Crear nuevo lugar
  createPlace(placeData) {
    const newPlace = {
      id: Date.now().toString(),
      ...placeData,
      createdAt: new Date().toISOString(),
    }

    this.places.push(newPlace)
    this.savePlaces()
    return newPlace
  }

  // Actualizar lugar
  updatePlace(id, placeData) {
    const index = this.places.findIndex((place) => place.id === id)
    if (index !== -1) {
      this.places[index] = {
        ...this.places[index],
        ...placeData,
        updatedAt: new Date().toISOString(),
      }
      this.savePlaces()
      return this.places[index]
    }
    return null
  }

  // Eliminar lugar
  deletePlace(id) {
    const index = this.places.findIndex((place) => place.id === id)
    if (index !== -1) {
      const deletedPlace = this.places.splice(index, 1)[0]
      this.savePlaces()

      // También eliminar de favoritos si existe
      const favoritesManager = window.favoritesManager // Assuming FavoritesManager is declared globally
      if (favoritesManager) {
        favoritesManager.removeFavorite(id)
      }

      return deletedPlace
    }
    return null
  }

  // Buscar lugares
  searchPlaces(query, category = "") {
    return this.places.filter((place) => {
      const matchesQuery =
        !query ||
        place.name.toLowerCase().includes(query.toLowerCase()) ||
        place.description.toLowerCase().includes(query.toLowerCase())

      const matchesCategory = !category || place.category === category

      return matchesQuery && matchesCategory
    })
  }
}

// Instancia global
window.placesManager = new PlacesManager()
