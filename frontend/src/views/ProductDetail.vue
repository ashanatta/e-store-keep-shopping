<template>
  <div class="container py-5">
    <div class="row g-5">
      <div class="col-lg-6">
        <div class="main-image mb-3">
          <img :src="selectedImage" :alt="product.name" />
        </div>
        <div class="d-flex gap-3 thumbnails">
          <button
            v-for="image in displayImages"
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
          <span v-if="currentPrice !== null" class="fs-4 fw-bold">${{ parseFloat(currentPrice).toFixed(2) }}</span>
          <span v-else class="fs-6 fw-semibold text-muted">Select color and size to see price</span>
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

        <div class="mb-4" v-if="displayColors.length > 0">
          <div class="fw-semibold mb-2">Color: Select a color</div>
          <div class="d-flex flex-wrap gap-2">
            <button
              v-for="color in displayColors"
              :key="color.id"
              type="button"
              class="option-btn"
              :class="{ active: toId(selectedColorId) === toId(color.id) }"
              @click="selectedColorId = toId(color.id)"
            >
              {{ color.name }}
            </button>
          </div>
        </div>

        <div class="mb-4" v-if="displaySizes.length > 0">
          <div class="fw-semibold mb-2">Size: Select a size</div>
          <div class="d-flex flex-wrap gap-2">
            <button
              v-for="size in displaySizes"
              :key="size.id"
              type="button"
              class="option-btn"
              :class="{ active: toId(selectedSizeId) === toId(size.id) }"
              @click="selectedSizeId = toId(size.id)"
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
          <button class="btn btn-dark flex-grow-1" @click="addSelectedToCart">Add to Cart</button>
          <button class="wishlist-heart-btn" :class="{ active: inWishlist }" @click="toggleWishlist">♥</button>
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
          Reviews ({{ reviews.length }})
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
          <div class="mb-3 small text-muted">
            Average rating: {{ reviewSummary.average || 0 }} ({{ reviewSummary.count || 0 }} reviews)
          </div>
          <div v-if="isAuthenticated" class="mb-4 border rounded p-3">
            <div class="fw-semibold mb-2">Write a Review</div>
            <div class="mb-2">
              <label class="form-label small">Rating</label>
              <select v-model.number="reviewForm.rating" class="form-select form-select-sm">
                <option :value="5">5</option>
                <option :value="4">4</option>
                <option :value="3">3</option>
                <option :value="2">2</option>
                <option :value="1">1</option>
              </select>
            </div>
            <div class="mb-2">
              <label class="form-label small">Comment</label>
              <textarea v-model="reviewForm.comment" class="form-control" rows="3" placeholder="Share your experience"></textarea>
            </div>
            <button class="btn btn-sm btn-dark" :disabled="savingReview" @click="submitReview">
              {{ savingReview ? "Submitting..." : "Submit Review" }}
            </button>
          </div>
          <div v-if="reviews.length === 0" class="text-muted small">No reviews yet.</div>
          <div v-for="review in reviews" :key="review.id" class="review">
            <div class="fw-semibold">{{ review.user?.name || "User" }}</div>
            <div class="text-warning small">{{ "★".repeat(Number(review.rating || 0)) }}</div>
            <div class="text-muted small">{{ review.comment || "No comment" }}</div>
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
                <div class="text-muted small">Select options</div>
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
import { useAuth } from "@/composables/useAuth.js"
import { useCart } from "@/composables/useCart.js"
import { useWishlist } from "@/composables/useWishlist.js"

const FILE_BASE_URL = "http://localhost:8000/api/files/"

const buildFileUrl = (path) => {
  if (!path) return ""
  return `${FILE_BASE_URL}${encodeURI(path)}`
}

const route = useRoute()
const { isAuthenticated } = useAuth()
const { addToCart, fetchCart } = useCart()
const { toggle: toggleWishlistState, isInWishlist, fetchWishlist } = useWishlist()
const product = ref({
  id: null,
  name: '',
  description: '',
  images: [],
  variants: [],
  rating: 0,
  reviews: 0,
  reviewsSummary: [],
  details: []
})
const loading = ref(true)
const savingReview = ref(false)
const reviews = ref([])
const reviewSummary = ref({ count: 0, average: 0 })
const reviewForm = ref({
  rating: 5,
  comment: "",
})

const selectedImage = ref('')
const selectedColorId = ref(null)
const selectedSizeId = ref(null)
const quantity = ref(1)
const activeTab = ref("description")

const toId = (value) => {
  if (value === null || value === undefined || value === '') {
    return null
  }
  const parsed = Number(value)
  return Number.isNaN(parsed) ? value : parsed
}

const fetchProduct = async () => {
  try {
    const productRes = await axios.get(`/products/${route.params.id}`)
    const data = productRes.data
    
    // Transform data to match component structure
    product.value = {
      ...data,
      // Ensure variants is an array
      variants: (data.variants || []).map((variant) => ({
        ...variant,
        color_id: toId(variant.color_id),
        size_id: toId(variant.size_id),
        image_urls: (variant.images || []).map((img) => buildFileUrl(img.path)),
      })),
      // Mock data for missing fields
      images: data.image ? [buildFileUrl(data.image)] : [],
      rating: Number(data.reviews_avg_rating || 0),
      reviews: Number(data.reviews_count || 0),
      reviewsSummary: [],
      details: [
        `Category: ${data.category?.name || 'Uncategorized'}`
      ]
    }
    
    if (displayImages.value.length > 0) {
      selectedImage.value = displayImages.value[0]
    }
  } catch (error) {
    console.error("Error fetching product:", error)
  } finally {
    loading.value = false
  }
}

const fetchReviews = async () => {
  try {
    const response = await axios.get(`/products/${route.params.id}/reviews`)
    reviews.value = response.data.reviews || []
    reviewSummary.value = response.data.summary || { count: 0, average: 0 }
  } catch (error) {
    console.error("Error fetching reviews:", error)
  }
}

onMounted(async () => {
  await fetchProduct()
  await fetchReviews()
  if (isAuthenticated.value) {
    await fetchWishlist()
  }
})

// Get unique colors from variants
const uniqueColors = computed(() => {
  if (!product.value.variants) return []
  const colors = new Map()
  product.value.variants.forEach(v => {
    const colorId = toId(v.color?.id ?? v.color_id)
    if (v.color && colorId !== null && !colors.has(colorId)) {
      colors.set(colorId, { ...v.color, id: colorId })
    }
  })
  return Array.from(colors.values())
})

const displayColors = computed(() => {
  return uniqueColors.value
})

// Get available sizes for selected color
const availableSizes = computed(() => {
  if (!product.value.variants) return []

  const hasColorSelection = selectedColorId.value !== null
  return product.value.variants
    .filter(v => {
      if (!v.size) return false
      if (!hasColorSelection) return true
      return toId(v.color_id) === toId(selectedColorId.value)
    })
    .map(v => v.size)
    // Remove duplicates
    .filter((size, index, self) => 
      index === self.findIndex(s => toId(s.id) === toId(size.id))
    )
})

const displaySizes = computed(() => {
  return availableSizes.value
})

const displayImages = computed(() => {
  const fallbackProductImages = product.value.images || []
  if (!product.value.variants?.length || selectedColorId.value === null) {
    if (fallbackProductImages.length > 0) {
      return fallbackProductImages
    }

    const allVariantImageUrls = product.value.variants
      .flatMap((variant) => variant.image_urls || [])
    if (allVariantImageUrls.length > 0) {
      return Array.from(new Set(allVariantImageUrls))
    }

    return fallbackProductImages
  }

  const variantImageUrls = product.value.variants
    .filter((variant) => toId(variant.color_id) === toId(selectedColorId.value))
    .flatMap((variant) => variant.image_urls || [])

  if (variantImageUrls.length > 0) {
    return Array.from(new Set(variantImageUrls))
  }

  return fallbackProductImages
})

// Watch for color change to reset size if not available
watch(selectedColorId, (newColorId) => {
  if (!product.value.variants?.length) return

  if (newColorId === null) {
    selectedSizeId.value = null
    return
  }
  
  // Check if current selected size is available for new color
  const sizeAvailable = product.value.variants.some(v => 
    toId(v.color_id) === toId(newColorId) &&
    toId(v.size_id) === toId(selectedSizeId.value)
  )
  
  if (!sizeAvailable) {
    selectedSizeId.value = null
  }
})

watch(displayImages, (images) => {
  if (!images?.length) {
    selectedImage.value = ''
    return
  }
  if (!images.includes(selectedImage.value)) {
    selectedImage.value = images[0]
  }
})

// Calculate current price based on selection
const currentPrice = computed(() => {
  if (selectedColorId.value !== null && selectedSizeId.value !== null) {
    const variant = product.value.variants.find(v => 
      toId(v.color_id) === toId(selectedColorId.value) &&
      toId(v.size_id) === toId(selectedSizeId.value)
    )
    if (variant && variant.price) {
      return variant.price
    }
  }
  return null
})

const selectedVariant = computed(() => {
  if (selectedColorId.value === null || selectedSizeId.value === null) {
    return null
  }
  return product.value.variants.find((variant) =>
    toId(variant.color_id) === toId(selectedColorId.value) &&
    toId(variant.size_id) === toId(selectedSizeId.value)
  ) || null
})

const inWishlist = computed(() => {
  if (!product.value.id) return false
  return isInWishlist(product.value.id).value
})

const addSelectedToCart = async () => {
  if (!isAuthenticated.value) {
    alert("Please login to add items to cart.")
    return
  }
  if (!selectedVariant.value) {
    alert("Please select color and size before adding to cart.")
    return
  }
  await addToCart({
    productId: product.value.id,
    variantId: selectedVariant.value.id,
    quantity: quantity.value,
  })
  await fetchCart()
  alert("Added to cart successfully!")
}

const toggleWishlist = async () => {
  if (!isAuthenticated.value) {
    alert("Please login to use wishlist.")
    return
  }
  await toggleWishlistState(product.value.id)
}

const submitReview = async () => {
  if (!isAuthenticated.value) {
    alert("Please login to write a review.")
    return
  }
  try {
    savingReview.value = true
    await axios.post(`/products/${product.value.id}/reviews`, {
      rating: reviewForm.value.rating,
      comment: reviewForm.value.comment,
    })
    reviewForm.value.comment = ""
    await fetchReviews()
    await fetchProduct()
  } catch (error) {
    console.error("Error saving review:", error)
    alert("Unable to submit review.")
  } finally {
    savingReview.value = false
  }
}

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

watch(() => route.params.id, () => {
  selectedColorId.value = null
  selectedSizeId.value = null
  quantity.value = 1
  fetchProduct()
  fetchReviews()
})
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

.wishlist-heart-btn {
  border: 1px solid #212529;
  background: #fff;
  color: #212529;
  border-radius: 0.375rem;
  min-width: 44px;
  font-size: 20px;
  line-height: 1;
}

.wishlist-heart-btn.active {
  border-color: #dc3545;
  color: #dc3545;
}
</style>
