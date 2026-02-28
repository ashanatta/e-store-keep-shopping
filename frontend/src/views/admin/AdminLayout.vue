<template>
  <div class="admin-layout">
    <aside class="sidebar">
      <div class="sidebar-header">
        <h3>Admin Panel</h3>
      </div>
      <nav class="sidebar-nav">
        <ul>
          <li><router-link to="/admin/products">Products</router-link></li>
          <li><router-link to="/admin/products/create">Add New Product</router-link></li>
          <li><router-link to="/admin/orders">Orders</router-link></li>
          <li><router-link to="/admin/users">Users</router-link></li>
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
  margin-bottom: 30px;
  text-align: center;
}

.sidebar-nav ul {
  list-style: none;
  padding: 0;
}

.sidebar-nav li {
  margin-bottom: 10px;
}

.sidebar-nav a {
  color: white;
  text-decoration: none;
  padding: 10px 15px;
  display: block;
  border-radius: 5px;
  transition: background-color 0.3s ease;
}

.sidebar-nav a:hover,
.sidebar-nav a.router-link-active {
  background-color: #495057;
}

.admin-content {
  flex-grow: 1;
  padding: 20px;
}
</style>
