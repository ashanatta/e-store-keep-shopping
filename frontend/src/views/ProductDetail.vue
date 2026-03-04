<template>
  <div class="container py-5">
    <div class="row g-5">
      <div class="col-lg-6">
        <div class="main-image mb-3">
          <img :src="selectedImage" :alt="product.name" />
        </div>
        <div class="d-flex gap-3 thumbnails">
          <button
            v-for="image in product.images"
            :key="image"
            type="button"
            class="thumb-btn"
            :class="{ active: selectedImage === image }"
            @click="selectedImage = image"
          >
            <img :src="image" :alt="product.name" />
          </button>
        </div>
      </div>

      <div class="col-lg-6">
        <div class="text-muted small mb-2">{{ product.type }}</div>
        <h2 class="fw-semibold mb-2">{{ product.name }}</h2>
        <div class="d-flex align-items-center gap-2 mb-3">
          <div class="text-warning">★★★★★</div>
          <div class="text-muted small">{{ product.rating.toFixed(1) }}</div>
          <div class="text-muted small">({{ product.reviews }} reviews)</div>
        </div>
        <div class="d-flex align-items-center gap-2 mb-4">
          <span class="fs-4 fw-bold">${{ currentPrice ? parseFloat(currentPrice).toFixed(2) : '0.00' }}</span>
          <span
            v-if="product.originalPrice"
            class="text-muted text-decoration-line-through"
          >
            ${{ product.originalPrice.toFixed(2) }}
          </span>
          <span v-if="product.originalPrice" class="badge bg-danger">
            {{ discountLabel }}
          </span>
        </div>

        <div class="mb-4" v-if="uniqueColors.length > 0">
          <div class="fw-semibold mb-2">Color: Select a color</div>
          <div class="d-flex flex-wrap gap-2">
            <button
              v-for="color in uniqueColors"
              :key="color.id"
              type="button"
              class="option-btn"
              :class="{ active: selectedColorId === color.id }"
              @click="selectedColorId = color.id"
            >
              {{ color.name }}
            </button>
          </div>
        </div>

        <div class="mb-4" v-if="availableSizes.length > 0">
          <div class="fw-semibold mb-2">Size: Select a size</div>
          <div class="d-flex flex-wrap gap-2">
            <button
              v-for="size in availableSizes"
              :key="size.id"
              type="button"
              class="option-btn"
              :class="{ active: selectedSizeId === size.id }"
              @click="selectedSizeId = size.id"
            >
              {{ size.name }}
            </button>
          </div>
        </div>

        <div class="d-flex align-items-center gap-3 mb-4">
          <div class="fw-semibold">Quantity</div>
          <div class="quantity-box">
            <button type="button" @click="decreaseQty">-</button>
            <span>{{ quantity }}</span>
            <button type="button" @click="increaseQty">+</button>
          </div>
          <div class="text-success small">In Stock</div>
        </div>

        <div class="d-flex gap-3 mb-4">
          <button class="btn btn-dark flex-grow-1">Add to Cart</button>
          <button class="btn btn-outline-dark">♥</button>
        </div>

        <ul class="list-unstyled small text-muted">
          <li class="mb-1">Free shipping on orders over $100</li>
          <li class="mb-1">30-day free returns</li>
          <li>Secure payment</li>
        </ul>
      </div>
    </div>

    <div class="tabs mt-5">
      <div class="tab-header">
        <button
          type="button"
          class="tab-btn"
          :class="{ active: activeTab === 'description' }"
          @click="activeTab = 'description'"
        >
          Description
        </button>
        <button
          type="button"
          class="tab-btn"
          :class="{ active: activeTab === 'reviews' }"
          @click="activeTab = 'reviews'"
        >
          Reviews ({{ product.reviewsSummary.length }})
        </button>
      </div>
      <div class="tab-content">
        <div v-if="activeTab === 'description'">
          <h5 class="fw-semibold">Product Description</h5>
          <p class="text-muted">{{ product.description }}</p>
          <ul class="text-muted small">
            <li v-for="detail in product.details" :key="detail">{{ detail }}</li>
          </ul>
        </div>
        <div v-else>
          <div v-for="review in product.reviewsSummary" :key="review.name" class="review">
            <div class="fw-semibold">{{ review.name }}</div>
            <div class="text-warning small">★★★★★</div>
            <div class="text-muted small">{{ review.text }}</div>
          </div>
        </div>
      </div>
    </div>

    <div class="mt-5">
      <h4 class="fw-semibold mb-4">You May Also Like</h4>
      <div class="row g-4">
        <div v-for="item in relatedProducts" :key="item.id" class="col-md-4">
          <router-link :to="`/product/${item.id}`" class="text-decoration-none text-dark">
            <div class="card related-card h-100">
              <img :src="item.image" class="card-img-top" :alt="item.name" />
              <div class="card-body">
                <div class="fw-semibold">{{ item.name }}</div>
                <div class="text-muted small">${{ item.base_price ? parseFloat(item.base_price).toFixed(2) : '0.00' }}</div>
              </div>
            </div>
          </router-link>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, ref, watch, onMounted } from "vue"
import { useRoute } from "vue-router"
import axios from "axios"

const route = useRoute()
const product = ref({
  id: null,
  name: '',
  description: '',
  base_price: 0,
  images: [],
  variants: [],
  rating: 0,
  reviews: 0,
  reviewsSummary: [],
  details: []
})
const loading = ref(true)

const selectedImage = ref('')
const selectedColorId = ref(null)
const selectedSizeId = ref(null)
const quantity = ref(1)
const activeTab = ref("description")

const fetchProduct = async () => {
  try {
    const response = await axios.get(`/products/${route.params.id}`)
    const data = response.data
    
    // Transform data to match component structure
    product.value = {
      ...data,
      // Ensure variants is an array
      variants: data.variants || [],
      // Mock data for missing fields
      images: data.image ? [`http://localhost:8000/storage/${data.image}`] : [],
      rating: 4.5,
      reviews: 12,
      reviewsSummary: [],
      details: [
        `Category: ${data.category?.name || 'Uncategorized'}`,
        `Base Price: $${data.base_price}`
      ]
    }
    
    if (product.value.images.length > 0) {
      selectedImage.value = product.value.images[0]
    }

    // Select first available color
    if (uniqueColors.value.length > 0) {
      selectedColorId.value = uniqueColors.value[0].id
    }
  } catch (error) {
    console.error("Error fetching product:", error)
  } finally {
    loading.value = false
  }
}

onMounted(fetchProduct)

// Get unique colors from variants
const uniqueColors = computed(() => {
  if (!product.value.variants) return []
  const colors = new Map()
  product.value.variants.forEach(v => {
    if (v.color && !colors.has(v.color.id)) {
      colors.set(v.color.id, v.color)
    }
  })
  return Array.from(colors.values())
})

// Get available sizes for selected color
const availableSizes = computed(() => {
  if (!selectedColorId.value || !product.value.variants) return []
  
  return product.value.variants
    .filter(v => v.color_id === selectedColorId.value && v.size)
    .map(v => v.size)
    // Remove duplicates
    .filter((size, index, self) => 
      index === self.findIndex(s => s.id === size.id)
    )
})

// Watch for color change to reset size if not available
watch(selectedColorId, (newColorId) => {
  if (!newColorId) return
  
  // Check if current selected size is available for new color
  const sizeAvailable = product.value.variants.some(v => 
    v.color_id === newColorId && 
    v.size_id === selectedSizeId.value
  )
  
  if (!sizeAvailable) {
    // Select first available size for this color
    const firstVariant = product.value.variants.find(v => v.color_id === newColorId)
    if (firstVariant) {
      selectedSizeId.value = firstVariant.size_id
    } else {
      selectedSizeId.value = null
    }
  }
})

// Calculate current price based on selection
const currentPrice = computed(() => {
  if (selectedColorId.value && selectedSizeId.value) {
    const variant = product.value.variants.find(v => 
      v.color_id === selectedColorId.value && 
      v.size_id === selectedSizeId.value
    )
    if (variant && variant.price) {
      return variant.price
    }
  }
  return product.value.base_price
})

const relatedProducts = computed(() => []) // TODO: Implement related products

const discountLabel = computed(() => {
  if (!product.value.originalPrice) {
    return ""
  }
  const percent = Math.round(
    ((product.value.originalPrice - currentPrice.value) / product.value.originalPrice) * 100
  )
  return `${percent}% OFF`
})

const decreaseQty = () => {
  if (quantity.value > 1) {
    quantity.value -= 1
  }
}

const increaseQty = () => {
  quantity.value += 1
}
</script>

<style scoped>
.main-image {
  border-radius: 14px;
  overflow: hidden;
  background: #f8f9fa;
}

.main-image img {
  width: 100%;
  height: 100%;
  max-height: 520px;
  object-fit: cover;
}

.thumbnails {
  overflow-x: auto;
}

.thumb-btn {
  border: 1px solid #e5e7eb;
  border-radius: 10px;
  padding: 0;
  background: transparent;
  width: 90px;
  height: 90px;
  overflow: hidden;
}

.thumb-btn img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.thumb-btn.active {
  border-color: #212529;
}

.option-btn {
  border: 1px solid #e5e7eb;
  border-radius: 20px;
  padding: 6px 16px;
  background: white;
  font-size: 14px;
}

.option-btn.active {
  background: #212529;
  color: white;
  border-color: #212529;
}

.quantity-box {
  display: flex;
  align-items: center;
  gap: 12px;
  border: 1px solid #e5e7eb;
  border-radius: 10px;
  padding: 6px 12px;
}

.quantity-box button {
  border: none;
  background: transparent;
  font-weight: 600;
}

.tabs {
  border-top: 1px solid #eee;
  padding-top: 24px;
}

.tab-header {
  display: flex;
  gap: 12px;
  margin-bottom: 16px;
}

.tab-btn {
  border: none;
  background: #f8f9fa;
  padding: 8px 18px;
  border-radius: 20px;
  font-size: 14px;
}

.tab-btn.active {
  background: #212529;
  color: white;
}

.tab-content {
  background: #fff;
  border: 1px solid #eee;
  border-radius: 12px;
  padding: 20px;
}

.review {
  border-bottom: 1px solid #eee;
  padding: 12px 0;
}

.review:last-child {
  border-bottom: none;
}

.related-card {
  border-radius: 14px;
  overflow: hidden;
}
</style>
