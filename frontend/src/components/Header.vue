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
           
          <div class="d-flex gap-3 mt-3 mt-lg-0 align-items-center">
            <template v-if="!isAuthenticated">
              <router-link to="/login" class="btn btn-outline-dark btn-sm">Login</router-link>
              <router-link to="/register" class="btn btn-dark btn-sm">Register</router-link>
            </template>
            <template v-else>
              <div class="dropdown">
                <button
                  class="btn btn-link text-decoration-none dropdown-toggle p-0 d-flex align-items-center"
                  data-bs-toggle="dropdown"
                  aria-expanded="false"
                >
                  <div class="avatar-initial me-2">{{ initial }}</div>
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                  <li v-if="isAdmin">
                    <router-link to="/admin" class="dropdown-item">Admin Panel</router-link>
                  </li>
                  <li>
                    <router-link to="/orders" class="dropdown-item">My Orders</router-link>
                  </li>
                  <li><hr class="dropdown-divider"></li>
                  <li>
                    <button class="dropdown-item text-danger" @click="handleLogout">Logout</button>
                  </li>
                </ul>
              </div>
            </template>
            <router-link to="/cart" class="icon-link cart-link">
              <i class="bi bi-cart icon"></i>
              <span v-if="cartCount > 0" class="cart-badge">{{ cartCount }}</span>
            </router-link>
          </div>

        </div>

      </div>
    </div>
  </nav>
</template>

<script setup>
import { computed, onMounted, watch } from "vue"
import { useAuth } from "@/composables/useAuth.js"
import { useCart } from "@/composables/useCart.js"

const { user, isAuthenticated, isAdmin, logout } = useAuth()
const { count: cartCount, fetchCart } = useCart()

const initial = computed(() => {
  const name = user.value?.name || ""
  return name.trim().charAt(0).toUpperCase() || "U"
})

const handleLogout = async () => {
  await logout()
}

watch(isAuthenticated, async () => {
  await fetchCart()
}, { immediate: true })

onMounted(async () => {
  if (isAuthenticated.value) {
    await fetchCart()
  }
})
</script>

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

.icon-link {
  display: inline-flex;
  align-items: center;
  color: inherit;
  text-decoration: none;
}

.cart-link {
  position: relative;
}

.cart-badge {
  position: absolute;
  top: -8px;
  right: -10px;
  min-width: 18px;
  height: 18px;
  border-radius: 999px;
  background: #dc3545;
  color: #fff;
  font-size: 11px;
  line-height: 18px;
  text-align: center;
  padding: 0 5px;
}

.router-link-exact-active {
  font-weight: bold;
}

.avatar-initial {
  width: 28px;
  height: 28px;
  border-radius: 50%;
  background: #111827;
  color: #fff;
  display: grid;
  place-items: center;
  font-size: 12px;
  font-weight: 700;
}
</style>
