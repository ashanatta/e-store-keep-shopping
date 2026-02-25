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
                    <span class="fw-bold">${{ product.price.toFixed(2) }}</span>
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
import { computed, ref } from "vue"

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

const sizes = ["S", "M", "L", "XL", "XXL", "28", "30", "32", "34", "36", "38"]
const colors = ["White", "Black", "Blue", "Light Gray", "Brown", "Navy", "Red", "Green"]
const ratingOptions = [5, 4, 3, 2, 1]

const products = [
  {
    id: 1,
    name: "Classic Oxford Shirt",
    category: "Men",
    type: "Shirts",
    image: "https://images.unsplash.com/photo-1521572163474-6864f9cf17ab",
    price: 49.99,
    originalPrice: 79.99,
    rating: 4.5,
    reviews: 127,
    isNew: false,
    sizes: ["M", "L", "XL"],
    colors: ["White", "Light Gray"]
  },
  {
    id: 2,
    name: "Slim Fit Denim Jeans",
    category: "Men",
    type: "Jeans",
    image: "https://images.unsplash.com/photo-1542272604-787c3835535d",
    price: 89.99,
    originalPrice: null,
    rating: 4.7,
    reviews: 203,
    isNew: true,
    sizes: ["30", "32", "34", "36"],
    colors: ["Blue", "Navy"]
  },
  {
    id: 3,
    name: "Leather Jacket",
    category: "Men",
    type: "Jackets",
    image: "https://images.unsplash.com/photo-1520975918318-7f2b6a29b6c3",
    price: 299.99,
    originalPrice: 359.99,
    rating: 4.6,
    reviews: 89,
    isNew: false,
    sizes: ["M", "L", "XL"],
    colors: ["Black"]
  },
  {
    id: 4,
    name: "Performance Polo",
    category: "Men",
    type: "Shirts",
    image: "https://images.unsplash.com/photo-1521572163474-6864f9cf17ab",
    price: 39.99,
    originalPrice: null,
    rating: 4.4,
    reviews: 156,
    isNew: false,
    sizes: ["S", "M", "L", "XL"],
    colors: ["Green", "Red"]
  },
  {
    id: 5,
    name: "Summer Wrap Dress",
    category: "Women",
    type: "Dresses",
    image: "https://images.unsplash.com/photo-1490481651871-ab68de25d43d",
    price: 69.99,
    originalPrice: 89.99,
    rating: 4.6,
    reviews: 210,
    isNew: true,
    sizes: ["S", "M", "L"],
    colors: ["Red", "White"]
  },
  {
    id: 6,
    name: "Tailored Blazer",
    category: "Women",
    type: "Jackets",
    image: "https://images.unsplash.com/photo-1485231183945-fffde7cc051e",
    price: 119.99,
    originalPrice: null,
    rating: 4.3,
    reviews: 84,
    isNew: false,
    sizes: ["M", "L", "XL"],
    colors: ["Black", "Navy"]
  },
  {
    id: 7,
    name: "Pleated Skirt",
    category: "Women",
    type: "Skirts",
    image: "https://images.unsplash.com/photo-1512436991641-6745cdb1723f",
    price: 54.99,
    originalPrice: 74.99,
    rating: 4.2,
    reviews: 63,
    isNew: false,
    sizes: ["S", "M", "L"],
    colors: ["Light Gray", "Navy"]
  },
  {
    id: 8,
    name: "Rib Knit Top",
    category: "Women",
    type: "Tops",
    image: "https://images.unsplash.com/photo-1524504388940-b1c1722653e1",
    price: 34.99,
    originalPrice: null,
    rating: 4.1,
    reviews: 52,
    isNew: true,
    sizes: ["S", "M", "L", "XL"],
    colors: ["White", "Brown"]
  },
  {
    id: 9,
    name: "Kids Denim Jacket",
    category: "Kids",
    type: "Jackets",
    image: "https://images.unsplash.com/photo-1503342217505-b0a15ec3261c",
    price: 44.99,
    originalPrice: 59.99,
    rating: 4.7,
    reviews: 76,
    isNew: false,
    sizes: ["S", "M", "L"],
    colors: ["Blue"]
  },
  {
    id: 10,
    name: "Graphic Tee Pack",
    category: "Kids",
    type: "T-Shirts",
    image: "https://images.unsplash.com/photo-1521572163474-6864f9cf17ab",
    price: 24.99,
    originalPrice: null,
    rating: 4.4,
    reviews: 58,
    isNew: true,
    sizes: ["S", "M", "L"],
    colors: ["Red", "White"]
  },
  {
    id: 11,
    name: "Playtime Joggers",
    category: "Kids",
    type: "Pants",
    image: "https://images.unsplash.com/photo-1503454537195-1dcabb73ffb9",
    price: 29.99,
    originalPrice: 39.99,
    rating: 4.3,
    reviews: 41,
    isNew: false,
    sizes: ["S", "M", "L"],
    colors: ["Green", "Navy"]
  },
  {
    id: 12,
    name: "Cozy Hoodie",
    category: "Kids",
    type: "Hoodies",
    image: "https://images.unsplash.com/photo-1512436991641-6745cdb1723f",
    price: 34.99,
    originalPrice: null,
    rating: 4.5,
    reviews: 69,
    isNew: true,
    sizes: ["S", "M", "L"],
    colors: ["Light Gray", "Blue"]
  }
]

const filteredProducts = computed(() => {
  let result = products

  if (props.category !== "all") {
    result = result.filter((product) => product.category === props.category)
  }

  if (props.saleOnly) {
    result = result.filter((product) => product.originalPrice)
  }

  result = result.filter((product) => product.price <= maxPrice.value)

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
    result = result.filter((product) =>
      selectedRatings.value.some((minRating) => product.rating >= minRating)
    )
  }

  return result
})

const sortedProducts = computed(() => {
  const result = [...filteredProducts.value]

  if (sortBy.value === "price-low") {
    return result.sort((a, b) => a.price - b.price)
  }
  if (sortBy.value === "price-high") {
    return result.sort((a, b) => b.price - a.price)
  }
  if (sortBy.value === "rating") {
    return result.sort((a, b) => b.rating - a.rating)
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
