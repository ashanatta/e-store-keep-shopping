<template>
  <div class="admin-layout">
    <aside class="sidebar">
      <div class="sidebar-header">
        <h3>Admin Panel</h3>
      </div>
      <nav class="sidebar-nav">
        <ul>
          <li><router-link to="/admin/dashboard"><i class="bi bi-speedometer2 me-2"></i>Dashboard</router-link></li>
          <li><router-link to="/admin/products"><i class="bi bi-box-seam me-2"></i>Products</router-link></li>
          <li><router-link to="/admin/categories"><i class="bi bi-tags me-2"></i>Categories</router-link></li>
          <li><router-link to="/admin/colors"><i class="bi bi-palette me-2"></i>Colors</router-link></li>
          <li><router-link to="/admin/sizes"><i class="bi bi-rulers me-2"></i>Sizes</router-link></li>
          <li><router-link to="/admin/reviews"><i class="bi bi-star me-2"></i>Reviews</router-link></li>
          <li><router-link to="/admin/wishlists"><i class="bi bi-heart me-2"></i>Wishlists</router-link></li>
          <li><router-link to="/admin/carts"><i class="bi bi-cart me-2"></i>Carts</router-link></li>
          <li><router-link to="/admin/orders"><i class="bi bi-bag me-2"></i>Orders</router-link></li>
          <li><router-link to="/admin/banners"><i class="bi bi-image me-2"></i>Banners</router-link></li>
          <li><router-link to="/admin/chats"><i class="bi bi-chat-dots me-2"></i>Customer Chats</router-link></li>
          <li class="mt-4 pt-4" style="border-top: 1px solid rgba(255, 255, 255, 0.1)">
            <router-link to="/profile"><i class="bi bi-person-circle me-2"></i>My Profile</router-link>
          </li>
          <li>
            <router-link to="/"><i class="bi bi-house me-2"></i>Home Page</router-link>
          </li>
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
  min-height: 100vh;
  background-color: #f8f9fa;
}

.sidebar {
  width: 250px;
  background-color: #343a40;
  color: white;
  padding: 20px;
  box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
}

.sidebar-header {
  padding: 10px 0 30px 0;
  border-bottom: 1px solid rgba(255, 255, 255, 0.1);
  margin-bottom: 25px;
  text-align: center;
}

.sidebar-header h3 {
  font-weight: 700;
  letter-spacing: 1px;
  text-transform: uppercase;
  font-size: 1.25rem;
  margin: 0;
}

.sidebar-nav ul {
  list-style: none;
  padding: 0;
}

.sidebar-nav li {
  margin-bottom: 10px;
}

.sidebar-nav a {
  color: rgba(255, 255, 255, 0.7);
  text-decoration: none;
  padding: 12px 20px;
  display: flex;
  align-items: center;
  border-radius: 8px;
  transition: all 0.2s ease;
  margin-bottom: 4px;
  font-weight: 500;
}

.sidebar-nav a i {
  font-size: 1.1rem;
  width: 24px;
}

.sidebar-nav a:hover {
  color: white;
  background-color: rgba(255, 255, 255, 0.1);
}

.sidebar-nav a.router-link-active {
  color: white;
  background-color: rgba(255, 255, 255, 0.15);
  box-shadow: inset 4px 0 0 #fff;
}

.admin-content {
  flex-grow: 1;
  padding: 20px;
}
</style>
