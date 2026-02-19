<template>
  <nav class="navbar navbar-expand-lg bg-white shadow-sm py-3">
    <div class="container-fluid px-4">

      <!-- Logo -->
      <router-link to="/" class="navbar-brand fw-bold fs-4">
        FASHION
      </router-link>

      <!-- Mobile Toggle Button -->
      <button
        class="navbar-toggler"
        type="button"
        data-bs-toggle="collapse"
        data-bs-target="#navbarContent"
      >
        <span class="navbar-toggler-icon"></span>
      </button>

      <!-- Collapsible Content -->
      <div class="collapse navbar-collapse" id="navbarContent">

        <!-- Center Menu -->
        <ul class="navbar-nav mx-auto text-center gap-lg-4">
          <li class="nav-item">
            <router-link to="/men" class="nav-link">Men</router-link>
          </li>
          <li class="nav-item">
            <router-link to="/women" class="nav-link">Women</router-link>
          </li>
          <li class="nav-item">
            <router-link to="/kids" class="nav-link">Kids</router-link>
          </li>
          <li class="nav-item">
            <router-link to="/about" class="nav-link">About Us</router-link>
          </li>
          <li class="nav-item">
            <router-link to="/sales" class="nav-link">Sales</router-link>
          </li>
        </ul>

        <!-- Right Side -->
        <div class="d-flex flex-column flex-lg-row align-items-center gap-3">

          <!-- Search -->
          <div class="search-box d-none d-md-flex align-items-center">
            <i class="bi bi-search text-muted me-2"></i>
            <input
              type="text"
              class="form-control border-0 shadow-none"
              placeholder="Search products..."
            />
          </div>

          <!-- Icons -->
          <div class="d-flex gap-3 mt-3 mt-lg-0">
            <i class="bi bi-person icon"></i>
            <i class="bi bi-cart icon"></i>
          </div>

        </div>

      </div>
    </div>
  </nav>
</template>

<script setup></script>

<style scoped>
.search-box {
  background: #e9ecef;
  padding: 6px 12px;
  border-radius: 10px;
  width: 500px; 
  margin-right: 150px;
}

.search-box input {
  background: transparent;
  font-size: 14px;
}

.icon {
  font-size: 18px;
  cursor: pointer;
  color: #333;
}

.icon:hover {
  color: black;
}

.router-link-exact-active {
  font-weight: bold;
}
</style>
