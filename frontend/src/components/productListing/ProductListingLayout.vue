<template>
  <div class="listing">
    <!-- Page header -->
    <header class="listing-hero">
      <div class="container-xxl px-3 px-lg-5">
        <nav class="crumbs" aria-label="Breadcrumb">
          <router-link to="/">Home</router-link>
          <i class="bi bi-chevron-right"></i>
          <span>{{ crumbLabel }}</span>
        </nav>
        <h1 class="listing-title">{{ title }}</h1>
        <p class="listing-sub">
          <template v-if="loading">Loading products…</template>
          <template v-else>{{ sortedProducts.length }} {{ sortedProducts.length === 1 ? "product" : "products" }} found</template>
        </p>
      </div>
    </header>

    <div class="container-xxl px-3 px-lg-5 pb-5">
      <!-- Toolbar -->
      <div class="toolbar">
        <button type="button" class="btn btn-outline-dark btn-sm d-lg-none" @click="filtersOpen = true">
          <i class="bi bi-sliders2 me-1"></i> Filters
          <span v-if="activeFilterCount" class="filter-count">{{ activeFilterCount }}</span>
        </button>

        <div class="active-chips d-none d-lg-flex">
          <template v-if="activeChips.length">
            <button
              v-for="chip in activeChips"
              :key="chip.key"
              type="button"
              class="chip active"
              @click="chip.remove()"
            >
              {{ chip.label }} <i class="bi bi-x"></i>
            </button>
            <button type="button" class="clear-link" @click="clearFilters">Clear all</button>
          </template>
        </div>

        <label class="sort">
          <span class="d-none d-sm-inline">Sort by</span>
          <select v-model="sortBy" class="form-select form-select-sm">
            <option value="featured">Featured</option>
            <option value="price-low">Price: Low to High</option>
            <option value="price-high">Price: High to Low</option>
            <option value="newest">Newest Arrivals</option>
            <option value="rating">Top Rated</option>
          </select>
        </label>
      </div>

      <div class="listing-body">
        <!-- Filters -->
        <div v-if="filtersOpen" class="filters-backdrop d-lg-none" @click="filtersOpen = false"></div>
        <aside class="filters" :class="{ open: filtersOpen }" aria-label="Filters">
          <div class="filters-head">
            <span class="fw-bold">Filters</span>
            <button type="button" class="btn-close d-lg-none" aria-label="Close filters" @click="filtersOpen = false"></button>
          </div>

          <div class="filter-group">
            <div class="filter-title">
              <span>Max price</span>
              <span class="price-val">${{ maxPrice }}</span>
            </div>
            <input
              v-model.number="maxPrice"
              type="range"
              class="form-range"
              min="20"
              max="300"
              step="10"
              :style="{ '--fill': `${((maxPrice - 20) / 280) * 100}%` }"
            />
            <div class="d-flex justify-content-between small text-muted">
              <span>$20</span>
              <span>$300</span>
            </div>
          </div>

          <div v-if="sizes.length" class="filter-group">
            <div class="filter-title"><span>Size</span></div>
            <div class="chip-wrap">
              <button
                v-for="size in sizes"
                :key="size"
                type="button"
                class="chip size-chip"
                :class="{ active: selectedSizes.includes(size) }"
                @click="toggleIn(selectedSizes, size)"
              >
                {{ size }}
              </button>
            </div>
          </div>

          <div v-if="colors.length" class="filter-group">
            <div class="filter-title"><span>Color</span></div>
            <div class="chip-wrap">
              <button
                v-for="color in colors"
                :key="color"
                type="button"
                class="chip"
                :class="{ active: selectedColors.includes(color) }"
                @click="toggleIn(selectedColors, color)"
              >
                <span class="swatch" :style="{ background: colorCodes[color] || color.toLowerCase() }"></span>
                {{ color }}
              </button>
            </div>
          </div>

          <div class="filter-group">
            <div class="filter-title"><span>Rating</span></div>
            <div class="rating-list">
              <button
                v-for="rating in ratingOptions"
                :key="rating"
                type="button"
                class="rating-row"
                :class="{ active: selectedRatings.includes(rating) }"
                @click="toggleIn(selectedRatings, rating)"
              >
                <span class="box"><i class="bi bi-check"></i></span>
                <span class="stars">
                  <i
                    v-for="s in 5"
                    :key="s"
                    :class="s <= rating ? 'bi bi-star-fill' : 'bi bi-star'"
                  ></i>
                </span>
                <span class="small text-muted">&amp; up</span>
              </button>
            </div>
          </div>

          <div class="filters-foot">
            <button class="btn btn-outline-dark w-100" @click="clearFilters">Clear all</button>
            <button class="btn btn-dark w-100 d-lg-none" @click="filtersOpen = false">
              Show {{ sortedProducts.length }} results
            </button>
          </div>
        </aside>

        <!-- Grid -->
        <div class="results">
          <div v-if="loading" class="grid">
            <ProductCardSkeleton v-for="n in 9" :key="n" />
          </div>

          <TransitionGroup v-else-if="sortedProducts.length" name="grid" tag="div" class="grid">
            <ProductCard v-for="product in sortedProducts" :key="product.id" :product="product" />
          </TransitionGroup>

          <div v-else class="empty-state">
            <span class="empty-icon"><i class="bi bi-search"></i></span>
            <h3>No products match your filters</h3>
            <p>Try widening the price range or removing a filter.</p>
            <button class="btn btn-dark" @click="clearFilters">Clear all filters</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, ref, onMounted, onUnmounted, watch } from "vue"
import { useRoute } from "vue-router"
import axios from "axios"
import { normalizeProduct } from "@/utils/product"
import { useWishlist } from "@/composables/useWishlist.js"
import { useAuth } from "@/composables/useAuth.js"
import ProductCard from "@/components/common/ProductCard.vue"
import ProductCardSkeleton from "@/components/common/ProductCardSkeleton.vue"

const route = useRoute()
const { fetchWishlist } = useWishlist()
const { isAuthenticated } = useAuth()

const props = defineProps({
  title: {
    type: String,
    default: "Product Listing"
  },
  category: {
    type: String,
    default: "all"
  },
  saleOnly: {
    type: Boolean,
    default: false
  }
})

const sortBy = ref("featured")
const maxPrice = ref(300)
const selectedSizes = ref([])
const selectedColors = ref([])
const selectedRatings = ref([])
const filtersOpen = ref(false)

const sizes = ref([])
const colors = ref([])
const colorCodes = ref({})
const ratingOptions = [5, 4, 3, 2, 1]

const products = ref([])
const loading = ref(true)

const crumbLabel = computed(() => {
  if (props.saleOnly) return "Sale"
  if (props.category !== "all") return props.category
  return "Shop"
})

const fetchProducts = async () => {
  loading.value = true
  try {
    const searchQuery = route.query.q || route.query.search || ''
    const params = {}
    if (searchQuery.trim()) params.search = searchQuery.trim()
    const response = await axios.get('/products', { params })
    // Transform backend data to frontend structure
    products.value = response.data.map(p => ({
      ...normalizeProduct(p),
      sizes: (p.variants || [])
        .map(v => v.size?.name)
        .filter(Boolean),
      colors: (p.variants || [])
        .map(v => v.color?.name)
        .filter(Boolean)
    }))

    // Derive filter options from available product variants so
    // shoppers only see relevant values for the current catalog.
    const sizeSet = new Set()
    const colorSet = new Set()
    ;(response.data || []).forEach((p) =>
      (p.variants || []).forEach((v) => {
        if (v.color?.name && v.color?.code) colorCodes.value[v.color.name] = v.color.code
      })
    )
    products.value.forEach((product) => {
      product.sizes.forEach((size) => sizeSet.add(size))
      product.colors.forEach((color) => colorSet.add(color))
    })
    sizes.value = Array.from(sizeSet)
    colors.value = Array.from(colorSet)
  } catch (error) {
    console.error('Error fetching products:', error)
  } finally {
    loading.value = false
  }
}

const fetchFilters = async () => {
  try {
    const [colorsRes, sizesRes] = await Promise.all([
      axios.get('/colors'),
      axios.get('/sizes')
    ])
    colorsRes.data.forEach((c) => {
      if (c.code) colorCodes.value[c.name] = c.code
    })
    if (!colors.value.length) {
      colors.value = colorsRes.data.map(c => c.name)
    }
    if (!sizes.value.length) {
      sizes.value = sizesRes.data.map(s => s.name)
    }
  } catch (error) {
    console.error('Error fetching filters:', error)
  }
}

onMounted(() => {
  fetchProducts()
  fetchFilters()
  if (isAuthenticated.value) {
    fetchWishlist()
  }
})

watch(() => route.query.q, () => {
  fetchProducts()
})

watch(filtersOpen, (open) => {
  document.body.style.overflow = open ? "hidden" : ""
})

onUnmounted(() => {
  document.body.style.overflow = ""
})

const toggleIn = (list, value) => {
  const index = list.indexOf(value)
  if (index === -1) list.push(value)
  else list.splice(index, 1)
}

const filteredProducts = computed(() => {
  let result = products.value

  if (props.category !== "all") {
    const targetCategory = props.category.trim().toLowerCase()
    result = result.filter((product) => product.category?.name?.trim()?.toLowerCase() === targetCategory)
  }

  if (props.saleOnly) {
    result = result.filter((product) => product.originalPrice)
  }

  result = result.filter((product) => product.displayPrice !== null && product.displayPrice <= maxPrice.value)

  if (selectedSizes.value.length > 0) {
    result = result.filter((product) =>
      product.sizes.some((size) => selectedSizes.value.includes(size))
    )
  }

  if (selectedColors.value.length > 0) {
    result = result.filter((product) =>
      product.colors.some((color) => selectedColors.value.includes(color))
    )
  }

  if (selectedRatings.value.length > 0) {
    // "N ★ & up" — the loosest selected threshold wins
    const minRating = Math.min(...selectedRatings.value)
    result = result.filter((product) => product.rating >= minRating)
  }

  // Note: Backend variants filtering would be better here for scalability
  // For now, we are filtering based on loaded products if we populated sizes/colors

  return result
})

const sortedProducts = computed(() => {
  const result = [...filteredProducts.value]

  if (sortBy.value === "price-low") {
    return result.sort((a, b) => a.displayPrice - b.displayPrice)
  }
  if (sortBy.value === "price-high") {
    return result.sort((a, b) => b.displayPrice - a.displayPrice)
  }
  if (sortBy.value === "newest") {
    return result.sort((a, b) => new Date(b.created_at) - new Date(a.created_at))
  }
  if (sortBy.value === "rating") {
    return result.sort((a, b) => b.rating - a.rating || b.reviews - a.reviews)
  }

  return result
})

const activeChips = computed(() => {
  const chips = []
  if (maxPrice.value < 300) {
    chips.push({ key: "price", label: `Under $${maxPrice.value}`, remove: () => (maxPrice.value = 300) })
  }
  selectedSizes.value.forEach((size) =>
    chips.push({ key: `s-${size}`, label: `Size ${size}`, remove: () => toggleIn(selectedSizes.value, size) })
  )
  selectedColors.value.forEach((color) =>
    chips.push({ key: `c-${color}`, label: color, remove: () => toggleIn(selectedColors.value, color) })
  )
  selectedRatings.value.forEach((rating) =>
    chips.push({ key: `r-${rating}`, label: `${rating}★ & up`, remove: () => toggleIn(selectedRatings.value, rating) })
  )
  return chips
})

const activeFilterCount = computed(() => activeChips.value.length)

const clearFilters = () => {
  maxPrice.value = 300
  selectedSizes.value = []
  selectedColors.value = []
  selectedRatings.value = []
}
</script>

<style scoped>
.listing-hero {
  background: var(--ivory);
  padding: 40px 0 44px;
  margin-bottom: 28px;
}

@media (min-width: 992px) {
  .listing-hero {
    padding: 56px 0 64px;
  }
}

.crumbs {
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 13px;
  color: var(--ink-soft);
  margin-bottom: 16px;
}

.crumbs a {
  color: var(--ink-soft);
  text-decoration: none;
}

.crumbs a:hover {
  color: var(--ink);
}

.crumbs i {
  font-size: 10px;
}

.crumbs span {
  color: var(--ink);
  font-weight: 600;
}

.listing-title {
  font-size: clamp(34px, 5vw, 64px);
  line-height: 1;
  letter-spacing: -0.04em;
  font-weight: 800;
  margin: 0 0 12px;
  word-break: break-word;
}

.listing-sub {
  color: var(--ink-soft);
  margin: 0;
}

/* Toolbar */
.toolbar {
  position: sticky;
  top: var(--header-h);
  z-index: 5;
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 12px 0;
  margin-bottom: 20px;
  background: rgba(255, 255, 255, 0.9);
  backdrop-filter: blur(10px);
  border-bottom: 1px solid var(--line);
}

.active-chips {
  flex-wrap: wrap;
  gap: 8px;
  align-items: center;
  margin-left: calc(280px + 40px);
}

.active-chips .chip {
  padding: 4px 10px 4px 12px;
  font-size: 12.5px;
}

.clear-link {
  border: 0;
  background: transparent;
  font-size: 13px;
  font-weight: 600;
  color: var(--ink-soft);
  text-decoration: underline;
}

.filter-count {
  display: inline-grid;
  place-items: center;
  min-width: 18px;
  height: 18px;
  margin-left: 4px;
  border-radius: 999px;
  background: var(--accent);
  color: #fff;
  font-size: 11px;
}

.sort {
  margin-left: auto;
  display: flex;
  align-items: center;
  gap: 10px;
  font-size: 13px;
  color: var(--ink-soft);
  white-space: nowrap;
}

.sort .form-select {
  min-width: 180px;
  font-weight: 600;
  color: var(--ink);
}

/* Layout */
.listing-body {
  display: grid;
  gap: 40px;
}

@media (min-width: 992px) {
  .listing-body {
    grid-template-columns: 280px 1fr;
    align-items: start;
  }
}

/* Filters */
.filters {
  background: #fff;
}

@media (min-width: 992px) {
  .filters {
    position: sticky;
    top: calc(var(--header-h) + 84px);
    max-height: calc(100vh - var(--header-h) - 110px);
    overflow-y: auto;
    padding-right: 6px;
  }
}

@media (max-width: 991.98px) {
  .filters {
    position: fixed;
    left: 0;
    right: 0;
    bottom: 0;
    z-index: 1080;
    max-height: 86vh;
    overflow-y: auto;
    padding: 20px 20px 0;
    border-radius: 24px 24px 0 0;
    box-shadow: 0 -20px 60px -20px rgba(14, 14, 16, 0.35);
    transform: translateY(105%);
    transition: transform 0.45s var(--ease);
  }

  .filters.open {
    transform: none;
  }
}

.filters-backdrop {
  position: fixed;
  inset: 0;
  z-index: 1075;
  background: rgba(14, 14, 16, 0.45);
  backdrop-filter: blur(3px);
}

.filters-head {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding-bottom: 14px;
  font-size: 16px;
}

.filter-group {
  padding: 20px 0;
  border-top: 1px solid var(--line);
}

.filter-title {
  display: flex;
  justify-content: space-between;
  align-items: center;
  font-weight: 700;
  font-size: 14px;
  margin-bottom: 14px;
}

.price-val {
  padding: 2px 10px;
  border-radius: 999px;
  background: var(--ink);
  color: #fff;
  font-size: 12.5px;
}

.form-range {
  --fill: 100%;
}

.form-range::-webkit-slider-runnable-track {
  background: linear-gradient(to right, var(--ink) var(--fill), var(--sand) var(--fill));
  border-radius: 6px;
}

.chip-wrap {
  display: flex;
  flex-wrap: wrap;
  gap: 8px;
}

.size-chip {
  min-width: 46px;
  justify-content: center;
}

.swatch {
  width: 14px;
  height: 14px;
  border-radius: 50%;
  background: var(--sand);
  box-shadow: inset 0 0 0 1px rgba(14, 14, 16, 0.15);
}

.chip.active .swatch {
  box-shadow: 0 0 0 2px #fff;
}

.rating-list {
  display: grid;
  gap: 4px;
}

.rating-row {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 6px 8px;
  margin: 0 -8px;
  border: 0;
  border-radius: 10px;
  background: transparent;
  text-align: left;
}

.rating-row:hover {
  background: var(--ivory);
}

.rating-row .box {
  width: 20px;
  height: 20px;
  border-radius: 6px;
  border: 1.5px solid var(--line-strong);
  display: grid;
  place-items: center;
  color: transparent;
  font-size: 16px;
  transition: all 0.2s;
}

.rating-row.active .box {
  background: var(--ink);
  border-color: var(--ink);
  color: #fff;
}

.stars {
  display: inline-flex;
  gap: 2px;
  color: var(--star);
  font-size: 13px;
}

.filters-foot {
  display: grid;
  gap: 10px;
  padding: 20px 0;
  border-top: 1px solid var(--line);
  background: #fff;
}

@media (max-width: 991.98px) {
  .filters-foot {
    position: sticky;
    bottom: 0;
    grid-template-columns: 1fr 1.4fr;
  }
}

/* Grid */
.grid {
  display: grid;
  grid-template-columns: repeat(2, minmax(0, 1fr));
  gap: 32px 16px;
}

@media (min-width: 768px) {
  .grid {
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 40px 24px;
  }
}

.grid-enter-active {
  transition: all 0.5s var(--ease);
}

.grid-leave-active {
  display: none;
}

.grid-enter-from {
  opacity: 0;
  transform: translateY(16px);
}

.grid-move {
  transition: transform 0.5s var(--ease);
}

.empty-state {
  text-align: center;
  padding: 72px 24px;
  border-radius: var(--radius-lg);
  background: var(--ivory);
}

.empty-icon {
  width: 64px;
  height: 64px;
  margin: 0 auto 20px;
  border-radius: 50%;
  background: #fff;
  display: grid;
  place-items: center;
  font-size: 24px;
}

.empty-state h3 {
  font-size: 22px;
  margin-bottom: 8px;
}

.empty-state p {
  color: var(--ink-soft);
  margin-bottom: 24px;
}
</style>
