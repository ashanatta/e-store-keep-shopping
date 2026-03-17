<template>
  <nav class="navbar navbar-expand-lg bg-white shadow-sm py-2 py-lg-3">
    <div class="container-fluid px-3 px-md-4">

      <!-- Logo -->
      <router-link to="/" class="navbar-brand d-flex align-items-center gap-2 text-decoration-none text-dark">
        <img src="/logo.png" alt="Doller Shope" class="header-logo" />
        <span class="fw-bold fs-4"></span>
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
        <ul class="navbar-nav mx-auto text-center gap-lg-4 header-nav-links">
          <li class="nav-item">
            <router-link to="/" class="nav-link">Home</router-link>
          </li>
          <li class="nav-item">
            <router-link to="/shop" class="nav-link">Shop</router-link>
          </li>
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
            <router-link to="/about" class="nav-link">About&nbsp;Us</router-link>
          </li>
          <li class="nav-item">
            <router-link to="/sales" class="nav-link">Sales</router-link>
          </li>
        </ul>

        <!-- Right Side -->
        <div class="d-flex flex-column flex-lg-row align-items-stretch align-items-lg-center gap-2 gap-lg-3 flex-grow-1 flex-lg-grow-0">

          <!-- Search -->
          <div class="search-box d-flex align-items-center">
            <i class="bi bi-search text-muted me-2"></i>
            <input
              v-model="searchQuery"
              type="text"
              class="form-control border-0 shadow-none"
              placeholder="Search products..."
              @keyup.enter="handleSearch"
            />
          </div>

          <!-- Icons -->
           
          <div class="d-flex gap-2 gap-lg-3 justify-content-center justify-content-lg-end align-items-center">
            <template v-if="!isAuthenticated">
              <router-link to="/login" class="btn btn-outline-dark btn-sm">Login</router-link>
              <router-link to="/register" class="btn btn-dark btn-sm">Register</router-link>
            </template>
            <template v-else>
              <div class="dropdown">
                <button
                  class="btn btn-link text-decoration-none p-0 d-flex align-items-center avatar-dropdown-btn"
                  data-bs-toggle="dropdown"
                  aria-expanded="false"
                  aria-haspopup="true"
                >
                  <div class="avatar-initial">{{ initial }}</div>
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 mt-2 p-2">
                  <li class="mb-1">
                    <div class="px-3 py-2 text-muted small border-bottom mb-1">
                      {{ user?.name }}
                    </div>
                  </li>
                  <li v-if="isAdmin">
                    <router-link to="/admin" class="dropdown-item rounded-2">Admin Panel</router-link>
                  </li>
                  <li>
                    <router-link to="/profile" class="dropdown-item rounded-2">My Profile</router-link>
                  </li>
                  <li>
                    <router-link to="/orders" class="dropdown-item rounded-2">My Orders</router-link>
                  </li>
                  <li><hr class="dropdown-divider"></li>
                  <li>
                    <button class="dropdown-item rounded-2 text-danger" @click="handleLogout">Logout</button>
                  </li>
                </ul>
              </div>
            </template>
            <router-link to="/cart" class="icon-link cart-link">
              <i class="bi bi-cart icon" :class="{ 'cart-bounce': cartAnimating }"></i>
              <span v-if="cartCount > 0" class="cart-badge">{{ cartCount }}</span>
            </router-link>
          </div>

        </div>

      </div>
    </div>
  </nav>
</template>

<script setup>
import { computed, onMounted, ref, watch } from "vue"
import { useRouter, useRoute } from "vue-router"
import { useAuth } from "@/composables/useAuth.js"
import { useCart } from "@/composables/useCart.js"

const router = useRouter()
const route = useRoute()
const { user, isAuthenticated, isAdmin, logout } = useAuth()
const { count: cartCount, fetchCart, cartBounceTrigger } = useCart()
const cartAnimating = ref(false)
const searchQuery = ref("")

const handleSearch = () => {
  const q = searchQuery.value?.trim()
  if (q) {
    router.push({ path: "/shop", query: { q } })
  } else {
    router.push({ path: "/shop" })
  }
}

watch(() => route.query.q, (q) => {
  searchQuery.value = q || ""
}, { immediate: true })

watch(searchQuery, (val) => {
  if (!val?.trim() && route.query.q) {
    const { q, ...rest } = route.query
    router.replace({ path: route.path, query: rest })
  }
})

const initial = computed(() => {
  const name = user.value?.name || ""
  return name.trim().charAt(0).toUpperCase() || "U"
})

const handleLogout = async () => {
  await logout()
  router.push("/")
}

watch(isAuthenticated, async () => {
  await fetchCart()
}, { immediate: true })

watch(cartBounceTrigger, () => {
  cartAnimating.value = true
  setTimeout(() => { cartAnimating.value = false }, 600)
})

onMounted(async () => {
  if (isAuthenticated.value) {
    await fetchCart()
  }
})
</script>

<style scoped>
/* Prevent nav links from wrapping on production (fixes "About Us" breaking) */
.header-nav-links {
  flex-wrap: nowrap;
}

.header-nav-links .nav-item {
  flex-shrink: 0;
}

.header-nav-links .nav-link {
  white-space: nowrap;
}

.search-box {
  background: #e9ecef;
  padding: 6px 12px;
  border-radius: 10px;
  width: 100%;
  max-width: 500px;
  margin-right: 0;
}

@media (min-width: 992px) {
  .search-box {
    margin-right: 24px;
  }
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

.cart-bounce {
  animation: cartBounce 0.6s ease;
}

@keyframes cartBounce {
  0%, 100% { transform: scale(1); }
  25% { transform: scale(1.3); }
  50% { transform: scale(0.9); }
  75% { transform: scale(1.15); }
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

.avatar-dropdown-btn {
  cursor: pointer;
}

.avatar-dropdown-btn::after {
  display: none;
}

.header-logo {
  height: 36px;
  width: auto;
  object-fit: contain;
}

@media (min-width: 992px) {
  .header-logo {
    height: 42px;
  }
}

@media (max-width: 991px) {
  .header-nav-links {
    flex-wrap: wrap;
    justify-content: center;
    gap: 0.5rem;
  }

  .header-nav-links .nav-link {
    white-space: normal;
    padding: 0.5rem;
  }
}
</style>
