<template>
  <div class="container py-4">
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between mb-3">
      <div>
        <h2 class="mb-1">{{ title }}</h2>
        <div class="text-muted">{{ filteredProducts.length }} products found</div>
      </div>
      <div class="d-flex align-items-center gap-2 mt-3 mt-md-0">
        <div class="text-muted small">Sort by:</div>
        <select v-model="sortBy" class="form-select form-select-sm sort-select">
          <option value="featured">Featured</option>
          <option value="price-low">Price: Low to High</option>
          <option value="price-high">Price: High to Low</option>
          <option value="newest">Newest Arrivals</option>
          <option value="rating">Top Rated</option>
        </select>
      </div>
    </div>

    <div class="row g-4">
      <div class="col-lg-3">
        <div class="filters-card p-3">
          <div class="fw-semibold mb-3">Filters</div>

          <div class="mb-4">
            <div class="mb-2 fw-semibold">Price Range</div>
            <input
              v-model.number="maxPrice"
              type="range"
              class="form-range"
              min="20"
              max="300"
              step="10"
            />
            <div class="d-flex justify-content-between small text-muted">
              <span>$20</span>
              <span>${{ maxPrice }}</span>
            </div>
          </div>

          <div class="mb-4">
            <div class="mb-2 fw-semibold">Size</div>
            <div class="d-grid gap-1 small">
              <label
                v-for="size in sizes"
                :key="size"
                class="d-flex align-items-center gap-2"
              >
                <input v-model="selectedSizes" type="checkbox" :value="size" />
                <span>{{ size }}</span>
              </label>
            </div>
          </div>

          <div class="mb-4">
            <div class="mb-2 fw-semibold">Color</div>
            <div class="d-grid gap-1 small">
              <label
                v-for="color in colors"
                :key="color"
                class="d-flex align-items-center gap-2"
              >
                <input v-model="selectedColors" type="checkbox" :value="color" />
                <span>{{ color }}</span>
              </label>
            </div>
          </div>

          <div class="mb-4">
            <div class="mb-2 fw-semibold">Rating</div>
            <div class="d-grid gap-1 small">
              <label
                v-for="rating in ratingOptions"
                :key="rating"
                class="d-flex align-items-center gap-2"
              >
                <input v-model="selectedRatings" type="checkbox" :value="rating" />
                <span>{{ rating }} ★ & up</span>
              </label>
            </div>
          </div>

          <button class="btn btn-outline-dark w-100" @click="clearFilters">
            Clear All Filters
          </button>
        </div>
      </div>

      <div class="col-lg-9">
        <div class="row g-4">
          <div
            v-for="product in sortedProducts"
            :key="product.id"
            class="col-lg-4 col-md-6"
          >
            <div class="card product-card h-100">
              <router-link
                :to="`/product/${product.id}`"
                class="text-decoration-none text-dark"
              >
                <div class="image-wrapper">
                  <img :src="product.image" class="card-img-top" :alt="product.name" />

                  <span
                    v-if="product.originalPrice"
                    class="badge bg-danger position-absolute top-0 start-0 m-2"
                  >
                    Sale
                  </span>
                  <span
                    v-if="product.isNew"
                    class="badge bg-primary position-absolute top-0 start-0 m-2"
                  >
                    New
                  </span>

                  <button class="wishlist-btn" type="button">♥</button>
                </div>
              </router-link>

              <div class="card-body">
                <div class="text-muted small mb-1">{{ product.type }}</div>
                <router-link
                  :to="`/product/${product.id}`"
                  class="text-decoration-none text-dark"
                >
                  <div class="fw-semibold mb-1">{{ product.name }}</div>
                  <div class="rating small text-muted mb-2">
                    ⭐ {{ product.rating }} ({{ product.reviews }})
                  </div>
                  <div>
                    <span v-if="product.displayPrice !== null" class="fw-bold">${{ product.displayPrice.toFixed(2) }}</span>
                    <span v-else class="fw-bold">Select options</span>
                    <span
                      v-if="product.originalPrice"
                      class="text-muted text-decoration-line-through ms-2"
                    >
                      ${{ product.originalPrice.toFixed(2) }}
                    </span>
                  </div>
                </router-link>
              </div>
            </div>
          </div>

          <div v-if="sortedProducts.length === 0" class="col-12">
            <div class="empty-state text-center p-5">
              No products match your filters.
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, ref, onMounted } from "vue"
import axios from "axios"

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

const sizes = ref([])
const colors = ref([])
const ratingOptions = [5, 4, 3, 2, 1]

const products = ref([])
const loading = ref(true)

const fetchProducts = async () => {
  loading.value = true
  try {
    const response = await axios.get('/products')
    // Transform backend data to frontend structure
    products.value = response.data.map(p => ({
      ...p,
      image: p.image ? `http://localhost:8000/api/files/${p.image}` : 'https://via.placeholder.com/300x400',
      originalPrice: null, // Add logic if you have original price in backend
      rating: 4.5, // Mock rating
      reviews: 0, // Mock reviews
      isNew: new Date(p.created_at) > new Date(Date.now() - 30 * 24 * 60 * 60 * 1000), // New if created in last 30 days
      displayPrice: (() => {
        const prices = (p.variants || [])
          .map(v => Number(v.price))
          .filter(price => Number.isFinite(price) && price > 0)
        return prices.length ? Math.min(...prices) : null
      })(),
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
})

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
  
  return result
})

const clearFilters = () => {
  maxPrice.value = 300
  selectedSizes.value = []
  selectedColors.value = []
  selectedRatings.value = []
}
</script>

<style scoped>
.filters-card {
  border: 1px solid #eee;
  border-radius: 12px;
  background: #fff;
}

.product-card {
  border-radius: 12px;
  overflow: hidden;
  transition: transform 0.2s ease;
}

.product-card:hover {
  transform: translateY(-6px);
}

.image-wrapper {
  position: relative;
  aspect-ratio: 3/4;
  overflow: hidden;
}

.card-img-top {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.wishlist-btn {
  position: absolute;
  top: 12px;
  right: 12px;
  background: white;
  border: none;
  border-radius: 50%;
  padding: 6px 10px;
  cursor: pointer;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
}

.empty-state {
  background: #f8f9fa;
  border-radius: 12px;
}

.sort-select {
  min-width: 180px;
}
</style>
