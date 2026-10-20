<template>
  <!-- Announcement bar -->
  <div v-if="!isAdminRoute" class="announce">
    <div class="announce-track">
      <span v-for="n in 2" :key="n" class="announce-group" :aria-hidden="n === 2">
        <span><i class="bi bi-truck"></i> Free shipping on orders over $100</span>
        <span class="dot">✦</span>
        <span><i class="bi bi-stars"></i> New: try clothes on with AI before you buy</span>
        <span class="dot">✦</span>
        <span><i class="bi bi-arrow-repeat"></i> 30-day free returns</span>
        <span class="dot">✦</span>
        <span><i class="bi bi-lightning-charge"></i> Up to 50% off in the Sale</span>
        <span class="dot">✦</span>
      </span>
    </div>
  </div>

  <header class="site-header" :class="{ scrolled }">
    <div class="container-xxl header-inner">
      <!-- Mobile menu toggle -->
      <button
        class="icon-btn d-lg-none"
        type="button"
        aria-label="Open menu"
        @click="menuOpen = true"
      >
        <i class="bi bi-list"></i>
      </button>

      <!-- Logo -->
      <router-link to="/" class="brand" aria-label="Doller Shope home">
        <img src="/logo.png" alt="Doller Shope" class="header-logo" />
      </router-link>

      <!-- Desktop nav -->
      <nav class="main-nav d-none d-lg-flex" aria-label="Main">
        <router-link
          v-for="link in links"
          :key="link.to"
          :to="link.to"
          class="nav-item-link"
          :class="{ sale: link.sale }"
        >
          {{ link.label }}
        </router-link>
      </nav>

      <!-- Actions -->
      <div class="actions">
        <form class="search-pill d-none d-md-flex" role="search" @submit.prevent="handleSearch">
          <i class="bi bi-search"></i>
          <input
            v-model="searchQuery"
            type="search"
            placeholder="Search products…"
            aria-label="Search products"
          />
          <kbd class="d-none d-xl-inline">↵</kbd>
        </form>

        <template v-if="!isAuthenticated">
          <router-link to="/login" class="icon-btn d-none d-sm-grid" aria-label="Login">
            <i class="bi bi-person"></i>
          </router-link>
        </template>
        <div v-else class="dropdown">
          <button
            class="avatar-btn"
            data-bs-toggle="dropdown"
            aria-expanded="false"
            aria-haspopup="true"
            aria-label="Account menu"
          >
            <span class="avatar-initial">{{ initial }}</span>
          </button>
          <ul class="dropdown-menu dropdown-menu-end account-menu">
            <li class="account-head">
              <span class="avatar-initial lg">{{ initial }}</span>
              <div class="min-w-0">
                <div class="fw-semibold text-truncate">{{ user?.name }}</div>
                <div class="small text-muted text-truncate">{{ user?.email }}</div>
              </div>
            </li>
            <li v-if="isAdmin">
              <router-link to="/admin" class="dropdown-item"><i class="bi bi-grid-1x2"></i>Admin Panel</router-link>
            </li>
            <li>
              <router-link to="/profile" class="dropdown-item"><i class="bi bi-person"></i>My Profile</router-link>
            </li>
            <li>
              <router-link to="/orders" class="dropdown-item"><i class="bi bi-bag"></i>My Orders</router-link>
            </li>
            <li><hr class="dropdown-divider"></li>
            <li>
              <button class="dropdown-item text-danger" @click="handleLogout">
                <i class="bi bi-box-arrow-right"></i>Logout
              </button>
            </li>
          </ul>
        </div>

        <router-link to="/cart" class="icon-btn cart-link" aria-label="Cart">
          <i class="bi bi-bag" :class="{ 'cart-bounce': cartAnimating }"></i>
          <span v-if="cartCount > 0" class="cart-badge">{{ cartCount }}</span>
        </router-link>
      </div>
    </div>
  </header>

  <!-- Mobile drawer -->
  <Teleport to="body">
    <Transition name="fade">
      <div v-if="menuOpen" class="drawer-backdrop" @click="menuOpen = false"></div>
    </Transition>
    <Transition name="drawer">
      <aside v-if="menuOpen" class="drawer" aria-label="Mobile menu">
        <div class="drawer-top">
          <img src="/logo.png" alt="Doller Shope" class="header-logo" />
          <button class="icon-btn" aria-label="Close menu" @click="menuOpen = false">
            <i class="bi bi-x-lg"></i>
          </button>
        </div>

        <form class="search-pill w-100 mb-4" role="search" @submit.prevent="handleSearch">
          <i class="bi bi-search"></i>
          <input v-model="searchQuery" type="search" placeholder="Search products…" aria-label="Search products" />
        </form>

        <nav class="drawer-nav">
          <router-link
            v-for="(link, i) in links"
            :key="link.to"
            :to="link.to"
            :class="{ sale: link.sale }"
            :style="{ '--i': i }"
          >
            <span>{{ link.label }}</span>
            <i class="bi bi-arrow-up-right"></i>
          </router-link>
        </nav>

        <div class="drawer-foot">
          <template v-if="!isAuthenticated">
            <router-link to="/login" class="btn btn-outline-dark w-100">Login</router-link>
            <router-link to="/register" class="btn btn-dark w-100">Create account</router-link>
          </template>
          <template v-else>
            <router-link v-if="isAdmin" to="/admin" class="btn btn-outline-dark w-100">Admin Panel</router-link>
            <router-link to="/orders" class="btn btn-outline-dark w-100">My Orders</router-link>
            <button class="btn btn-dark w-100" @click="handleLogout">Logout</button>
          </template>
        </div>
      </aside>
    </Transition>
  </Teleport>
</template>

<script setup>
import { computed, onMounted, onUnmounted, ref, watch } from "vue"
import { useRouter, useRoute } from "vue-router"
import { useAuth } from "@/composables/useAuth.js"
import { useCart } from "@/composables/useCart.js"

const router = useRouter()
const route = useRoute()
const { user, isAuthenticated, isAdmin, logout } = useAuth()
const { count: cartCount, fetchCart, cartBounceTrigger } = useCart()
const cartAnimating = ref(false)
const searchQuery = ref("")
const scrolled = ref(false)
const menuOpen = ref(false)

const links = [
  { to: "/", label: "Home" },
  { to: "/shop", label: "Shop" },
  { to: "/men", label: "Men" },
  { to: "/women", label: "Women" },
  { to: "/kids", label: "Kids" },
  { to: "/about", label: "About Us" },
  { to: "/sales", label: "Sale", sale: true },
]

const isAdminRoute = computed(() => route.path.startsWith("/admin"))

const handleSearch = () => {
  const q = searchQuery.value?.trim()
  menuOpen.value = false
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

watch(() => route.fullPath, () => {
  menuOpen.value = false
})

watch(menuOpen, (open) => {
  document.body.style.overflow = open ? "hidden" : ""
})

const initial = computed(() => {
  const name = user.value?.name || ""
  return name.trim().charAt(0).toUpperCase() || "U"
})

const handleLogout = async () => {
  menuOpen.value = false
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

const onScroll = () => {
  scrolled.value = window.scrollY > 8
}

onMounted(async () => {
  window.addEventListener("scroll", onScroll, { passive: true })
  onScroll()
  if (isAuthenticated.value) {
    await fetchCart()
  }
})

onUnmounted(() => {
  window.removeEventListener("scroll", onScroll)
  document.body.style.overflow = ""
})
</script>

<style scoped>
/* ---------- Announcement ---------- */
.announce {
  background: var(--ink);
  color: rgba(255, 255, 255, 0.86);
  font-size: 12.5px;
  font-weight: 500;
  overflow: hidden;
  white-space: nowrap;
  height: 36px;
  display: flex;
  align-items: center;
}

.announce-track {
  display: inline-flex;
  animation: marquee 38s linear infinite;
}

.announce:hover .announce-track {
  animation-play-state: paused;
}

.announce-group {
  display: inline-flex;
  align-items: center;
  gap: 28px;
  padding-right: 28px;
}

.announce-group i {
  color: var(--accent);
  margin-right: 4px;
}

.announce .dot {
  color: rgba(255, 255, 255, 0.35);
  font-size: 10px;
}

@keyframes marquee {
  to {
    transform: translateX(-50%);
  }
}

/* ---------- Header ---------- */
.site-header {
  position: sticky;
  top: 0;
  z-index: 1030;
  background: rgba(255, 255, 255, 0.82);
  backdrop-filter: saturate(180%) blur(16px);
  -webkit-backdrop-filter: saturate(180%) blur(16px);
  border-bottom: 1px solid transparent;
  transition: border-color 0.3s, box-shadow 0.3s;
}

.site-header.scrolled {
  border-bottom-color: var(--line);
  box-shadow: 0 8px 30px -18px rgba(14, 14, 16, 0.25);
}

.header-inner {
  height: var(--header-h);
  display: flex;
  align-items: center;
  gap: 12px;
  padding-left: 16px;
  padding-right: 16px;
}

@media (min-width: 992px) {
  .header-inner {
    gap: 24px;
    padding-left: 32px;
    padding-right: 32px;
  }
}

.brand {
  display: inline-flex;
  align-items: center;
  flex-shrink: 0;
}

.header-logo {
  height: 34px;
  width: auto;
  object-fit: contain;
}

@media (min-width: 992px) {
  .header-logo {
    height: 40px;
  }
}

/* Desktop nav */
.main-nav {
  flex: 1;
  justify-content: center;
  gap: 4px;
}

.nav-item-link {
  position: relative;
  padding: 8px 14px;
  font-size: 14.5px;
  font-weight: 600;
  color: var(--ink-soft);
  text-decoration: none;
  white-space: nowrap;
  border-radius: 999px;
  transition: color 0.2s, background-color 0.2s;
}

.nav-item-link:hover {
  color: var(--ink);
  background: var(--ivory);
}

.nav-item-link.router-link-exact-active {
  color: var(--ink);
}

.nav-item-link.router-link-exact-active::after {
  content: "";
  position: absolute;
  left: 50%;
  bottom: 0;
  width: 5px;
  height: 5px;
  border-radius: 50%;
  background: var(--accent);
  transform: translateX(-50%);
}

.nav-item-link.sale {
  color: var(--accent-ink);
}

.nav-item-link.sale::before {
  content: "";
  display: inline-block;
  width: 6px;
  height: 6px;
  margin-right: 7px;
  margin-bottom: 2px;
  border-radius: 50%;
  background: var(--accent);
  box-shadow: 0 0 0 0 rgba(255, 90, 54, 0.6);
  animation: ping 2s infinite;
}

@keyframes ping {
  70% { box-shadow: 0 0 0 7px rgba(255, 90, 54, 0); }
  100% { box-shadow: 0 0 0 0 rgba(255, 90, 54, 0); }
}

/* Actions */
.actions {
  margin-left: auto;
  display: flex;
  align-items: center;
  gap: 6px;
}

@media (min-width: 992px) {
  .actions {
    margin-left: 0;
  }
}

.search-pill {
  align-items: center;
  gap: 10px;
  height: 44px;
  padding: 0 14px;
  border-radius: 999px;
  background: var(--ivory);
  border: 1.5px solid transparent;
  width: 240px;
  transition: width 0.4s var(--ease), border-color 0.2s, background-color 0.2s;
  display: flex;
}

.search-pill:focus-within {
  width: 300px;
  background: #fff;
  border-color: var(--ink);
}

.drawer .search-pill,
.drawer .search-pill:focus-within {
  width: 100%;
}

.search-pill i {
  color: var(--muted);
}

.search-pill input {
  flex: 1;
  min-width: 0;
  border: 0;
  outline: 0;
  background: transparent;
  font-size: 14px;
  color: var(--ink);
}

.search-pill input::-webkit-search-cancel-button {
  display: none;
}

.search-pill kbd {
  background: #fff;
  color: var(--muted);
  border: 1px solid var(--line);
  font-size: 11px;
  padding: 1px 6px;
  border-radius: 6px;
}

.icon-btn {
  position: relative;
  width: 44px;
  height: 44px;
  display: grid;
  place-items: center;
  border-radius: 50%;
  border: 0;
  background: transparent;
  color: var(--ink);
  font-size: 20px;
  text-decoration: none;
  transition: background-color 0.2s;
}

.icon-btn:hover {
  background: var(--ivory);
  color: var(--ink);
}

.cart-badge {
  position: absolute;
  top: 4px;
  right: 2px;
  min-width: 19px;
  height: 19px;
  padding: 0 5px;
  border-radius: 999px;
  background: var(--accent);
  color: #fff;
  font-size: 11px;
  font-weight: 700;
  line-height: 19px;
  text-align: center;
  border: 2px solid #fff;
  box-sizing: content-box;
}

.cart-bounce {
  display: inline-block;
  animation: cartBounce 0.6s ease;
}

@keyframes cartBounce {
  0%, 100% { transform: scale(1); }
  25% { transform: scale(1.3); }
  50% { transform: scale(0.9); }
  75% { transform: scale(1.15); }
}

.avatar-btn {
  border: 0;
  background: transparent;
  padding: 4px;
  border-radius: 50%;
}

.avatar-initial {
  width: 34px;
  height: 34px;
  border-radius: 50%;
  background: linear-gradient(135deg, var(--ink) 0%, #3a3a44 100%);
  color: #fff;
  display: grid;
  place-items: center;
  font-size: 13px;
  font-weight: 700;
  flex-shrink: 0;
}

.avatar-initial.lg {
  width: 42px;
  height: 42px;
  font-size: 15px;
}

.account-menu {
  min-width: 260px;
  padding: 8px;
  margin-top: 10px !important;
}

.account-head {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 10px 10px 14px;
  margin-bottom: 6px;
  border-bottom: 1px solid var(--line);
}

.min-w-0 {
  min-width: 0;
}

.account-menu .dropdown-item {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 9px 10px;
  border-radius: 10px;
  font-weight: 500;
  font-size: 14px;
}

.account-menu .dropdown-item i {
  font-size: 16px;
  color: var(--muted);
}

.account-menu .dropdown-item.text-danger i {
  color: inherit;
}

/* ---------- Drawer ---------- */
.drawer-backdrop {
  position: fixed;
  inset: 0;
  z-index: 1060;
  background: rgba(14, 14, 16, 0.45);
  backdrop-filter: blur(4px);
}

.drawer {
  position: fixed;
  top: 0;
  left: 0;
  bottom: 0;
  z-index: 1070;
  width: min(380px, 88vw);
  background: #fff;
  padding: 18px 20px 24px;
  display: flex;
  flex-direction: column;
  overflow-y: auto;
}

.drawer-top {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 20px;
}

.drawer-nav {
  display: flex;
  flex-direction: column;
}

.drawer-nav a {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 14px 4px;
  font-size: 22px;
  font-weight: 700;
  letter-spacing: -0.02em;
  color: var(--ink);
  text-decoration: none;
  border-bottom: 1px solid var(--line);
  animation: drawerItem 0.5s var(--ease) both;
  animation-delay: calc(var(--i) * 40ms + 80ms);
}

.drawer-nav a i {
  font-size: 16px;
  color: var(--muted);
}

.drawer-nav a.router-link-exact-active span {
  font-family: var(--font-display);
  font-style: italic;
  font-weight: 400;
  font-size: 26px;
}

.drawer-nav a.sale {
  color: var(--accent-ink);
}

@keyframes drawerItem {
  from {
    opacity: 0;
    transform: translateX(-16px);
  }
}

.drawer-foot {
  margin-top: auto;
  padding-top: 24px;
  display: grid;
  gap: 10px;
}

.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}

.drawer-enter-active,
.drawer-leave-active {
  transition: transform 0.45s var(--ease);
}

.drawer-enter-from,
.drawer-leave-to {
  transform: translateX(-100%);
}
</style>
