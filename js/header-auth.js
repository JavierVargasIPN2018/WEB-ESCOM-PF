// header-auth.js
class AuthManager {
  constructor() {
    this.currentUser = this.loadCurrentUser();
    this.users       = this.loadUsers();
  }
  loadCurrentUser() {
    const s = localStorage.getItem("escom_current_user");
    return s ? JSON.parse(s) : null;
  }
  loadUsers() {
    const s = localStorage.getItem("escom_users");
    return s ? JSON.parse(s) : [];
  }
  saveCurrentUser(u) {
    this.currentUser = u;
    if (u) localStorage.setItem("escom_current_user", JSON.stringify(u));
    else  localStorage.removeItem("escom_current_user");
    window.dispatchEvent(new CustomEvent("authChanged"));
  }
  isAuthenticated() { return this.currentUser !== null; }
  getCurrentUser() { return this.currentUser; }
  logout() {
    this.saveCurrentUser(null);
    localStorage.removeItem("escom_remember_user");
  }
}

if (!window.authManager) {
  window.authManager = new AuthManager();
}

document.addEventListener("DOMContentLoaded", updateAuthUI);
window.addEventListener("authChanged", updateAuthUI);

function updateAuthUI() {
  const c = document.getElementById("auth-container");
  if (!c) return;
  if (authManager.isAuthenticated()) {
    const u = authManager.getCurrentUser();
    renderUserMenu(c, u);
  } else {
    renderLoginButton(c);
  }
}

function renderLoginButton(c) {
  c.innerHTML = `<a href="/html/auth.html" class="auth-button">Iniciar Sesión</a>`;
}

function renderUserMenu(c, u) {
  const initials = (u.firstname.charAt(0)+u.lastname.charAt(0)).toUpperCase();
  c.innerHTML = `
    <div class="user-menu">
      <div class="user-avatar">${initials}</div>
      <span class="user-name">${u.firstname}</span>
      <div class="user-dropdown">
        <a href="#" onclick="showProfile()">Mi Perfil</a>
        <a href="/html/favoritos.html">Mis Favoritos</a>
        <a href="/html/admin.html">Gestión</a>
        <a href="#" id="logout-btn">Cerrar Sesión</a>
      </div>
    </div>`;
  const menu = c.querySelector(".user-menu");
  const dd   = c.querySelector(".user-dropdown");
  menu.addEventListener("click", e => {
    e.stopPropagation(); dd.classList.toggle("active");
  });
  document.addEventListener("click", ()=> dd.classList.remove("active"));
  c.querySelector("#logout-btn").addEventListener("click", e => {
    e.preventDefault();
    authManager.logout();
    updateAuthUI();
    showNotification("Sesión cerrada", "success");
    setTimeout(() => location.href = "index.html", 800);
  });
}

function showProfile() {
  showNotification("Próximamente perfil", "info");
}

function showNotification(msg, type="info") {
  /* tu impl. habitual de toast aquí */
}
