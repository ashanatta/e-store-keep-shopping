<template>
  <div class="admin-layout">
    <aside class="sidebar">
      <div class="sidebar-header">
        <span class="sb-logo"><i class="bi bi-grid-1x2-fill"></i></span>
        <div>
          <div class="sb-title">Admin Panel</div>
          <div class="sb-sub">Doller Shope</div>
        </div>
      </div>
      <nav class="sidebar-nav">
        <div class="sb-label">Overview</div>
        <ul>
          <li><router-link to="/admin/dashboard"><i class="bi bi-speedometer2"></i><span>Dashboard</span></router-link></li>
          <li><router-link to="/admin/orders"><i class="bi bi-bag"></i><span>Orders</span></router-link></li>
          <li><router-link to="/admin/chats"><i class="bi bi-chat-dots"></i><span>Customer Chats</span></router-link></li>
        </ul>
        <div class="sb-label">Catalog</div>
        <ul>
          <li><router-link to="/admin/products"><i class="bi bi-box-seam"></i><span>Products</span></router-link></li>
          <li><router-link to="/admin/categories"><i class="bi bi-tags"></i><span>Categories</span></router-link></li>
          <li><router-link to="/admin/colors"><i class="bi bi-palette"></i><span>Colors</span></router-link></li>
          <li><router-link to="/admin/sizes"><i class="bi bi-rulers"></i><span>Sizes</span></router-link></li>
          <li><router-link to="/admin/banners"><i class="bi bi-image"></i><span>Banners</span></router-link></li>
        </ul>
        <div class="sb-label">Customers</div>
        <ul>
          <li><router-link to="/admin/reviews"><i class="bi bi-star"></i><span>Reviews</span></router-link></li>
          <li><router-link to="/admin/wishlists"><i class="bi bi-heart"></i><span>Wishlists</span></router-link></li>
          <li><router-link to="/admin/carts"><i class="bi bi-cart"></i><span>Carts</span></router-link></li>
        </ul>
        <ul class="sb-bottom">
          <li><router-link to="/profile"><i class="bi bi-person-circle"></i><span>My Profile</span></router-link></li>
          <li><router-link to="/"><i class="bi bi-house"></i><span>Home Page</span></router-link></li>
        </ul>
      </nav>
    </aside>
    <main class="admin-content">
      <router-view></router-view>
    </main>
  </div>
</template>

<script setup>
// Script for AdminLayout
</script>

<style scoped>
.admin-layout {
  display: flex;
  flex-direction: column;
  min-height: calc(100vh - var(--header-h));
  background-color: var(--ivory);
}

@media (min-width: 992px) {
  .admin-layout {
    flex-direction: row;
  }
}

.sidebar {
  background: var(--ink);
  color: #fff;
  padding: 16px;
}

@media (min-width: 992px) {
  .sidebar {
    width: 264px;
    flex-shrink: 0;
    padding: 24px 16px;
    position: sticky;
    top: var(--header-h);
    height: calc(100vh - var(--header-h));
    overflow-y: auto;
  }
}

.sidebar-header {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 0 8px 20px;
  margin-bottom: 8px;
  border-bottom: 1px solid rgba(255, 255, 255, 0.08);
}

.sb-logo {
  width: 40px;
  height: 40px;
  border-radius: 12px;
  background: var(--accent);
  display: grid;
  place-items: center;
  font-size: 16px;
}

.sb-title {
  font-weight: 700;
  font-size: 15px;
}

.sb-sub {
  font-size: 12px;
  color: rgba(255, 255, 255, 0.5);
}

.sb-label {
  padding: 18px 12px 8px;
  font-size: 11px;
  font-weight: 700;
  letter-spacing: 0.14em;
  text-transform: uppercase;
  color: rgba(255, 255, 255, 0.4);
}

.sidebar-nav ul {
  list-style: none;
  padding: 0;
  margin: 0;
  display: grid;
  gap: 2px;
}

@media (max-width: 991.98px) {
  .sidebar-nav {
    display: flex;
    gap: 4px;
    overflow-x: auto;
    scrollbar-width: none;
  }

  .sidebar-nav ul {
    display: flex;
  }

  .sb-label,
  .sidebar-header {
    display: none;
  }

  .sidebar-nav a {
    white-space: nowrap;
  }
}

.sidebar-nav a {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 10px 12px;
  border-radius: 10px;
  color: rgba(255, 255, 255, 0.68);
  text-decoration: none;
  font-weight: 500;
  font-size: 14px;
  transition: all 0.2s ease;
}

.sidebar-nav a i {
  font-size: 1.05rem;
  width: 20px;
  text-align: center;
}

.sidebar-nav a:hover {
  color: #fff;
  background: rgba(255, 255, 255, 0.07);
}

.sidebar-nav a.router-link-active {
  color: var(--ink);
  background: #fff;
  font-weight: 600;
}

.sidebar-nav a.router-link-active i {
  color: var(--accent);
}

.sb-bottom {
  margin-top: 20px !important;
  padding-top: 16px !important;
  border-top: 1px solid rgba(255, 255, 255, 0.08);
}

@media (max-width: 991.98px) {
  .sb-bottom {
    margin: 0 !important;
    padding: 0 !important;
    border: 0;
  }
}

.admin-content {
  flex-grow: 1;
  min-width: 0;
  padding: 20px 16px;
}

@media (min-width: 992px) {
  .admin-content {
    padding: 32px;
  }
}
</style>
