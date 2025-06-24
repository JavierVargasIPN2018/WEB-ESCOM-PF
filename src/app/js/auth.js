// Sistema de autenticación
class AuthManager {
  constructor() {
    this.currentUser = this.loadCurrentUser()
    this.users = this.loadUsers()
  }

  // Cargar usuario actual desde localStorage
  loadCurrentUser() {
    const stored = localStorage.getItem("escom_current_user")
    return stored ? JSON.parse(stored) : null
  }

  // Cargar usuarios registrados
  loadUsers() {
    const stored = localStorage.getItem("escom_users")
    return stored ? JSON.parse(stored) : []
  }

  // Guardar usuarios
  saveUsers() {
    localStorage.setItem("escom_users", JSON.stringify(this.users))
  }

  // Guardar usuario actual
  saveCurrentUser(user) {
    this.currentUser = user
    if (user) {
      localStorage.setItem("escom_current_user", JSON.stringify(user))
    } else {
      localStorage.removeItem("escom_current_user")
    }

    // Disparar evento de cambio de autenticación
    window.dispatchEvent(
      new CustomEvent("authChanged", {
        detail: { user: this.currentUser },
      }),
    )
  }

  // Registrar usuario
  register(userData) {
    // Validar que el email no exista
    if (this.users.find((user) => user.email === userData.email)) {
      throw new Error("Ya existe una cuenta con este correo electrónico")
    }

    // Crear nuevo usuario
    const newUser = {
      id: Date.now().toString(),
      ...userData,
      createdAt: new Date().toISOString(),
      lastLogin: null,
    }

    this.users.push(newUser)
    this.saveUsers()

    return newUser
  }

  // Iniciar sesión
  login(email, password, rememberMe = false) {
    const user = this.users.find((u) => u.email === email && u.password === password)

    if (!user) {
      throw new Error("Credenciales incorrectas")
    }

    // Actualizar último login
    user.lastLogin = new Date().toISOString()
    this.saveUsers()

    // Guardar sesión
    this.saveCurrentUser(user)

    if (rememberMe) {
      localStorage.setItem("escom_remember_user", email)
    }

    return user
  }

  // Cerrar sesión
  logout() {
    this.saveCurrentUser(null)
    localStorage.removeItem("escom_remember_user")
  }

  // Verificar si está autenticado
  isAuthenticated() {
    return this.currentUser !== null
  }

  // Obtener usuario actual
  getCurrentUser() {
    return this.currentUser
  }

  // Obtener usuario recordado
  getRememberedUser() {
    return localStorage.getItem("escom_remember_user")
  }
}

// Instancia global
window.authManager = new AuthManager()

// Gestión de la página de autenticación
document.addEventListener("DOMContentLoaded", () => {
  // Si ya está autenticado, redirigir
  if (window.authManager.isAuthenticated()) {
    window.location.href = "index.html"
    return
  }

  setupTabs()
  setupForms()
  setupPasswordToggles()
  setupPasswordStrength()
  loadRememberedUser()
})

function setupTabs() {
  const tabButtons = document.querySelectorAll(".tab-button")
  const tabContainers = document.querySelectorAll(".auth-form-container")

  tabButtons.forEach((button) => {
    button.addEventListener("click", () => {
      const targetTab = button.getAttribute("data-tab")

      // Actualizar botones
      tabButtons.forEach((btn) => btn.classList.remove("active"))
      button.classList.add("active")

      // Actualizar contenedores
      tabContainers.forEach((container) => {
        container.classList.remove("active")
        if (container.id === `${targetTab}-tab`) {
          container.classList.add("active")
        }
      })
    })
  })
}

function setupForms() {
  const loginForm = document.getElementById("login-form")
  const registerForm = document.getElementById("register-form")

  // Login form
  loginForm.addEventListener("submit", async (e) => {
    e.preventDefault()
    await handleLogin(e.target)
  })

  // Register form
  registerForm.addEventListener("submit", async (e) => {
    e.preventDefault()
    await handleRegister(e.target)
  })

  // Social login buttons
  document.querySelectorAll(".social-btn").forEach((btn) => {
    btn.addEventListener("click", () => {
      showNotification("Funcionalidad de login social próximamente", "info")
    })
  })
}

async function handleLogin(form) {
  const formData = new FormData(form)
  const email = formData.get("email")
  const password = formData.get("password")
  const rememberMe = formData.get("remember-me") === "on"

  try {
    showLoading(true)

    // Simular delay de red
    await new Promise((resolve) => setTimeout(resolve, 1000))

    const user = window.authManager.login(email, password, rememberMe)

    showNotification(`¡Bienvenido, ${user.firstname}!`, "success")

    // Redirigir después de un momento
    setTimeout(() => {
      window.location.href = "index.html"
    }, 1500)
  } catch (error) {
    showNotification(error.message, "error")
  } finally {
    showLoading(false)
  }
}

async function handleRegister(form) {
  const formData = new FormData(form)

  // Validar contraseñas
  const password = formData.get("password")
  const confirmPassword = formData.get("confirmPassword")

  if (password !== confirmPassword) {
    showNotification("Las contraseñas no coinciden", "error")
    return
  }

  if (password.length < 8) {
    showNotification("La contraseña debe tener al menos 8 caracteres", "error")
    return
  }

  const userData = {
    firstname: formData.get("firstname"),
    lastname: formData.get("lastname"),
    email: formData.get("email"),
    studentId: formData.get("studentId") || null,
    password: password,
  }

  try {
    showLoading(true)

    // Simular delay de red
    await new Promise((resolve) => setTimeout(resolve, 1500))

    const user = window.authManager.register(userData)

    showNotification(`¡Cuenta creada exitosamente! Bienvenido, ${user.firstname}`, "success")

    // Auto-login después del registro
    window.authManager.saveCurrentUser(user)

    // Redirigir después de un momento
    setTimeout(() => {
      window.location.href = "index.html"
    }, 2000)
  } catch (error) {
    showNotification(error.message, "error")
  } finally {
    showLoading(false)
  }
}

function setupPasswordToggles() {
  document.querySelectorAll(".password-toggle").forEach((toggle) => {
    toggle.addEventListener("click", () => {
      const targetId = toggle.getAttribute("data-target")
      const input = document.getElementById(targetId)
      const icon = toggle.querySelector(".toggle-icon")

      if (input.type === "password") {
        input.type = "text"
        icon.textContent = "🙈"
      } else {
        input.type = "password"
        icon.textContent = "👁️"
      }
    })
  })
}

function setupPasswordStrength() {
  const passwordInput = document.getElementById("register-password")
  const strengthBar = document.querySelector(".strength-fill")
  const strengthText = document.querySelector(".strength-text")

  if (!passwordInput) return

  passwordInput.addEventListener("input", () => {
    const password = passwordInput.value
    const strength = calculatePasswordStrength(password)

    // Actualizar barra
    strengthBar.style.width = `${strength.percentage}%`
    strengthBar.style.backgroundColor = strength.color

    // Actualizar texto
    strengthText.textContent = strength.text
    strengthText.style.color = strength.color
  })
}

function calculatePasswordStrength(password) {
  let score = 0
  const feedback = []

  if (password.length >= 8) score += 25
  else feedback.push("mínimo 8 caracteres")

  if (/[a-z]/.test(password)) score += 25
  else feedback.push("minúsculas")

  if (/[A-Z]/.test(password)) score += 25
  else feedback.push("mayúsculas")

  if (/[0-9]/.test(password)) score += 25
  else feedback.push("números")

  if (/[^A-Za-z0-9]/.test(password)) score += 10

  let text, color
  if (score < 30) {
    text = "Muy débil"
    color = "#FF6B6B"
  } else if (score < 60) {
    text = "Débil"
    color = "#F39C12"
  } else if (score < 90) {
    text = "Buena"
    color = "#00C2FF"
  } else {
    text = "Excelente"
    color = "#2ECC71"
  }

  return {
    percentage: Math.min(score, 100),
    text,
    color,
    feedback,
  }
}

function loadRememberedUser() {
  const rememberedEmail = window.authManager.getRememberedUser()
  if (rememberedEmail) {
    const emailInput = document.getElementById("login-email")
    const rememberCheckbox = document.getElementById("remember-me")

    if (emailInput) {
      emailInput.value = rememberedEmail
      rememberCheckbox.checked = true
    }
  }
}

function showLoading(show) {
  const overlay = document.getElementById("loading-overlay")
  overlay.style.display = show ? "flex" : "none"
}

function showNotification(message, type = "info") {
  const notification = document.createElement("div")
  notification.className = `notification notification-${type}`
  notification.textContent = message

  Object.assign(notification.style, {
    position: "fixed",
    top: "20px",
    right: "20px",
    padding: "1rem 1.5rem",
    borderRadius: "12px",
    color: "white",
    fontWeight: "600",
    zIndex: "10000",
    transform: "translateX(100%)",
    transition: "transform 0.3s ease",
    backgroundColor:
      type === "success" ? "#2ECC71" : type === "error" ? "#FF6B6B" : type === "info" ? "#00C2FF" : "#0057FF",
    boxShadow: "0 4px 12px rgba(0, 0, 0, 0.15)",
  })

  document.body.appendChild(notification)

  setTimeout(() => {
    notification.style.transform = "translateX(0)"
  }, 100)

  setTimeout(() => {
    notification.style.transform = "translateX(100%)"
    setTimeout(() => {
      if (notification.parentNode) {
        notification.parentNode.removeChild(notification)
      }
    }, 300)
  }, 4000)
}
