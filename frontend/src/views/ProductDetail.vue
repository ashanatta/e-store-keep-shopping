<template>
  <div class="pd">
    <div class="container-xxl px-3 px-lg-5 py-4 py-lg-5">
      <nav class="crumbs" aria-label="Breadcrumb">
        <router-link to="/">Home</router-link>
        <i class="bi bi-chevron-right"></i>
        <router-link to="/shop">Shop</router-link>
        <template v-if="product.category?.name">
          <i class="bi bi-chevron-right"></i>
          <span class="text-muted">{{ product.category.name }}</span>
        </template>
        <i class="bi bi-chevron-right"></i>
        <span class="current">{{ product.name }}</span>
      </nav>

      <div class="pd-grid">
        <!-- Gallery -->
        <div class="gallery">
          <div class="thumbs" v-if="displayImages.length > 1">
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
          <div class="main-image" :class="{ loading }">
            <Transition name="img-fade" mode="out-in">
              <img v-if="selectedImage" :key="selectedImage" :src="selectedImage" :alt="product.name" />
            </Transition>
            <span v-if="product.is_on_sale && discountLabel" class="sale-flag">{{ discountLabel }}</span>
            <button
              v-if="selectedGarmentPath"
              type="button"
              class="tryon-float"
              @click="openTryOn"
            >
              <i class="bi bi-stars"></i> Try it on
            </button>
          </div>
        </div>

        <!-- Info -->
        <div class="info">
          <div class="info-sticky">
            <div class="d-flex align-items-center justify-content-between gap-3 mb-2">
              <span class="pd-cat">{{ product.category?.name || product.type }}</span>
              <span :class="displayStockStatus === 'In Stock' ? 'stock in' : 'stock out'">
                <span class="dot"></span>{{ displayStockStatus }}
              </span>
            </div>

            <h1 class="pd-title">{{ product.name }}</h1>

            <button type="button" class="rating-line" @click="activeTab = 'reviews'; scrollToTabs()">
              <span class="stars">
                <i
                  v-for="s in 5"
                  :key="s"
                  :class="s <= Math.round(product.rating) ? 'bi bi-star-fill' : 'bi bi-star'"
                ></i>
              </span>
              <span class="fw-semibold">{{ product.rating.toFixed(1) }}</span>
              <span class="text-muted">· {{ product.reviews }} reviews</span>
            </button>

            <div class="price-row">
              <span class="price-now" :class="{ sale: product.is_on_sale && product.original_base_price > 0 }">
                ${{ parseFloat(product.final_price).toFixed(2) }}
              </span>
              <span
                v-if="product.is_on_sale && product.original_base_price > 0"
                class="price-was"
              >
                ${{ product.original_base_price.toFixed(2) }}
              </span>
              <span v-if="product.is_on_sale && product.original_base_price > 0 && discountLabel" class="save-pill">
                Save {{ product.discount_percentage }}%
              </span>
            </div>

            <div class="option-block" v-if="displayColors.length > 0">
              <div class="option-label">
                Color
                <span>{{ selectedColorName || "Select a color" }}</span>
              </div>
              <div class="d-flex flex-wrap gap-2">
                <button
                  v-for="color in displayColors"
                  :key="color.id"
                  type="button"
                  class="option-btn"
                  :class="{ active: toId(selectedColorId) === toId(color.id) }"
                  @click="selectedColorId = toId(color.id)"
                >
                  <span class="swatch" :style="{ background: (color.code || color.hex || color.name || '').toLowerCase() }"></span>
                  {{ color.name }}
                </button>
              </div>
            </div>

            <div class="option-block" v-if="displaySizes.length > 0">
              <div class="option-label">
                Size
                <span>{{ selectedSizeName || "Select a size" }}</span>
              </div>
              <div class="d-flex flex-wrap gap-2">
                <button
                  v-for="size in displaySizes"
                  :key="size.id"
                  type="button"
                  class="option-btn size"
                  :class="{ active: toId(selectedSizeId) === toId(size.id) }"
                  @click="selectedSizeId = toId(size.id)"
                >
                  {{ size.name }}
                </button>
              </div>
            </div>

            <div class="buy-row">
              <div class="qty" :class="{ disabled: !showQuantityCounter }">
                <button type="button" aria-label="Decrease quantity" :disabled="!showQuantityCounter || quantity <= 1" @click="decreaseQty">
                  <i class="bi bi-dash"></i>
                </button>
                <span>{{ quantity }}</span>
                <button type="button" aria-label="Increase quantity" :disabled="!showQuantityCounter || quantity >= currentStock" @click="increaseQty">
                  <i class="bi bi-plus"></i>
                </button>
              </div>
              <button
                class="btn btn-dark btn-lg flex-grow-1 add-to-cart-btn"
                :class="{ 'add-to-cart-success': addToCartSuccess }"
                :disabled="addingToCart"
                @click="addSelectedToCart"
              >
                <template v-if="!addToCartSuccess">
                  <span v-if="addingToCart" class="spinner-border spinner-border-sm me-2"></span>
                  <i v-else class="bi bi-bag-plus me-2"></i>
                  {{ addingToCart ? 'Adding…' : 'Add to Cart' }}
                </template>
                <span v-else><i class="bi bi-check2 me-2"></i>Added!</span>
              </button>
              <button
                class="wishlist-heart-btn"
                :class="{ active: inWishlist }"
                :aria-label="inWishlist ? 'Remove from wishlist' : 'Add to wishlist'"
                @click="toggleWishlist"
              >
                <i :class="inWishlist ? 'bi bi-heart-fill' : 'bi bi-heart'"></i>
              </button>
            </div>
            <div v-if="selectedVariant && currentStock > 0 && currentStock <= 5" class="low-stock">
              <i class="bi bi-fire"></i> Only {{ currentStock }} left in this option
            </div>

            <button
              v-if="selectedGarmentPath"
              type="button"
              class="tryon-card"
              @click="openTryOn"
            >
              <span class="tryon-icon"><i class="bi bi-stars"></i></span>
              <span class="flex-grow-1 text-start">
                <span class="d-block fw-bold">Try it on with AI</span>
                <span class="d-block small opacity-75">Upload a photo and see how it looks on you</span>
              </span>
              <i class="bi bi-arrow-right"></i>
            </button>

            <ul class="assurances">
              <li><i class="bi bi-truck"></i><span><strong>Free shipping</strong> on orders over $100</span></li>
              <li><i class="bi bi-arrow-repeat"></i><span><strong>30-day</strong> free returns</span></li>
              <li><i class="bi bi-shield-lock"></i><span><strong>Secure</strong> payment</span></li>
            </ul>
          </div>
        </div>
      </div>

      <!-- Tabs -->
      <section ref="tabsEl" class="tabs">
        <div class="tab-header" role="tablist">
          <button
            type="button"
            role="tab"
            class="tab-btn"
            :class="{ active: activeTab === 'description' }"
            @click="activeTab = 'description'"
          >
            Description
          </button>
          <button
            type="button"
            role="tab"
            class="tab-btn"
            :class="{ active: activeTab === 'reviews' }"
            @click="activeTab = 'reviews'"
          >
            Reviews <span class="tab-count">{{ reviews.length }}</span>
          </button>
        </div>

        <div class="tab-panel">
          <div v-if="activeTab === 'description'" class="desc">
            <p>{{ product.description }}</p>
            <ul>
              <li v-for="detail in product.details" :key="detail">{{ detail }}</li>
            </ul>
          </div>

          <div v-else class="reviews-layout">
            <div class="review-summary">
              <div class="big-score">{{ Number(reviewSummary.average || 0).toFixed(1) }}</div>
              <div class="stars mb-1">
                <i
                  v-for="s in 5"
                  :key="s"
                  :class="s <= Math.round(reviewSummary.average || 0) ? 'bi bi-star-fill' : 'bi bi-star'"
                ></i>
              </div>
              <div class="text-muted small">Based on {{ reviewSummary.count || 0 }} reviews</div>

              <div v-if="isAuthenticated && !hasUserReviewed" class="review-form">
                <div class="fw-bold mb-2">Write a review</div>
                <div class="star-input mb-3" role="radiogroup" aria-label="Rating">
                  <button
                    v-for="s in 5"
                    :key="s"
                    type="button"
                    role="radio"
                    :aria-checked="reviewForm.rating === s"
                    :aria-label="`${s} star${s > 1 ? 's' : ''}`"
                    @click="reviewForm.rating = s"
                  >
                    <i :class="s <= reviewForm.rating ? 'bi bi-star-fill' : 'bi bi-star'"></i>
                  </button>
                </div>
                <textarea v-model="reviewForm.comment" class="form-control mb-3" rows="3" placeholder="Share your experience"></textarea>
                <button class="btn btn-dark w-100" :disabled="savingReview" @click="submitReview">
                  {{ savingReview ? "Submitting…" : "Submit Review" }}
                </button>
              </div>
              <div v-else-if="isAuthenticated && hasUserReviewed" class="review-note">
                <i class="bi bi-check-circle me-1"></i> You have already reviewed this product.
              </div>
              <div v-else class="review-note">
                <router-link to="/login">Login</router-link> to write a review.
              </div>
            </div>

            <div class="review-list">
              <div v-if="reviews.length === 0" class="no-reviews">
                <i class="bi bi-chat-square-heart"></i>
                <p class="mb-0">No reviews yet — be the first to share your thoughts.</p>
              </div>
              <article v-for="review in reviews" :key="review.id" class="review">
                <span class="r-avatar">{{ (review.user?.name || "U").charAt(0).toUpperCase() }}</span>
                <div class="flex-grow-1">
                  <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
                    <span class="fw-semibold">{{ review.user?.name || "User" }}</span>
                    <span class="stars small">
                      <i
                        v-for="s in 5"
                        :key="s"
                        :class="s <= Number(review.rating || 0) ? 'bi bi-star-fill' : 'bi bi-star'"
                      ></i>
                    </span>
                  </div>
                  <p class="r-text">{{ review.comment || "No comment" }}</p>
                </div>
              </article>
            </div>
          </div>
        </div>
      </section>

      <section v-if="relatedProducts.length" class="mt-5">
        <h2 class="section-title mb-4">You may <em>also like</em></h2>
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
      </section>
    </div>

    <VirtualTryOn
      v-if="showTryOn"
      :product-id="product.id"
      :product-name="product.name"
      :garment-path="selectedGarmentPath"
      :garment-url="selectedImage"
      @close="showTryOn = false"
    />
  </div>
</template>

<script setup>
import { computed, ref, watch, onMounted } from "vue"
import { useRoute, useRouter } from "vue-router"
import axios from "axios"
import { useAuth } from "@/composables/useAuth.js"
import { useCart } from "@/composables/useCart.js"
import { useWishlist } from "@/composables/useWishlist.js"
import { useToast } from "@/composables/useToast.js"
import VirtualTryOn from "@/components/product/VirtualTryOn.vue"

const { success, error, warning } = useToast()

import { getImageUrl } from "@/utils/imageUrl"

const buildFileUrl = (path) => getImageUrl(path) || ""

const route = useRoute()
const router = useRouter()
const { isAuthenticated, user } = useAuth()
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
  details: [],
  discount_percentage: 0,
  sale_start: null,
  sale_end: null,
  is_on_sale: false,
  original_base_price: 0, // New field to store the original price from variants
  final_price: 0,
})
const loading = ref(true)
const savingReview = ref(false)
const reviews = ref([])
const reviewSummary = ref({ count: 0, average: 0 })
const reviewForm = ref({
  rating: 5,
  comment: "",
})
const hasUserReviewed = ref(false)

const selectedImage = ref('')
const selectedColorId = ref(null)
const selectedSizeId = ref(null)
const addingToCart = ref(false)
const addToCartSuccess = ref(false)
const quantity = ref(1)
const activeTab = ref("description")
const showTryOn = ref(false)
// Maps a displayed image URL back to the path stored in the database (needed by the try-on API)
const imagePathByUrl = ref(new Map())

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

    const pathMap = new Map()
    if (data.image) pathMap.set(buildFileUrl(data.image), data.image)
    ;(data.variants || []).forEach((variant) => {
      (variant.images || []).forEach((img) => {
        if (img.path) pathMap.set(buildFileUrl(img.path), img.path)
      })
    })
    imagePathByUrl.value = pathMap

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
      ],
      discount_percentage: Number(data.discount_percentage || 0),
      sale_start: data.sale_start || null,
      sale_end: data.sale_end || null,
      is_on_sale: Boolean(data.is_on_sale),
      original_base_price: Number(data.min_variant_price || data.final_price || 0),
      final_price: Number(data.final_price || 0),
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
    if (user.value) {
      hasUserReviewed.value = reviews.value.some(review => review.user_id === user.value.id)
    }
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
  
  const currentStock = computed(() => {
    return selectedVariant.value ? selectedVariant.value.stock : 0
  })

const overallProductInStock = computed(() => {
    return product.value.variants.some(variant => variant.stock > 0)
  })

  const displayStockStatus = computed(() => {
    if (selectedVariant.value) {
      return currentStock.value > 0 ? 'In Stock' : 'Out of Stock'
    }
    return overallProductInStock.value ? 'In Stock' : 'Out of Stock'
  })

  const showQuantityCounter = computed(() => {
    return selectedVariant.value && currentStock.value > 0
  })

const inWishlist = computed(() => {
  if (!product.value.id) return false
  return isInWishlist(product.value.id).value
})

const addSelectedToCart = async () => {
  if (!isAuthenticated.value) {
    warning("Please login to add items to cart.")
    return
  }
  if (!selectedVariant.value) {
    warning("Please select color and size before adding to cart.")
    return
  }
  if (currentStock.value === 0) {
    error("This product is out of stock.")
    return
  }
  addingToCart.value = true
  addToCartSuccess.value = false
  try {
    await addToCart({
      productId: product.value.id,
      variantId: selectedVariant.value.id,
      quantity: quantity.value,
    })
    await fetchCart()
    addToCartSuccess.value = true
    success("Added to cart successfully!")
    setTimeout(() => { addToCartSuccess.value = false }, 1500)
  } catch (err) {
    error("Failed to add to cart.")
  } finally {
    addingToCart.value = false
  }
}

const selectedGarmentPath = computed(() => imagePathByUrl.value.get(selectedImage.value) || "")

const openTryOn = () => {
  if (!isAuthenticated.value) {
    warning("Please login to use virtual try-on.")
    return
  }
  showTryOn.value = true
}

const toggleWishlist = async () => {
  if (!isAuthenticated.value) {
    warning("Please login to use wishlist.")
    return
  }
  await toggleWishlistState(product.value.id)
}

const submitReview = async () => {
  if (!isAuthenticated.value) {
    warning("Please login to write a review.")
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
    success("Review submitted successfully!")
  } catch (err) {
    console.error("Error saving review:", err)
    if (err.sessionExpired) {
      warning("Your session has expired. Please login again.")
      router.push({ name: "Login", query: { redirect: route.fullPath } })
      return
    }
    // Surface the server's reason (validation message, etc.) instead of a generic error
    const data = err?.response?.data
    const firstFieldError = data?.errors ? Object.values(data.errors).flat()[0] : null
    error(firstFieldError || data?.message || "Unable to submit review.")
  } finally {
    savingReview.value = false
  }
}

const relatedProducts = computed(() => []) // TODO: Implement related products

const discountLabel = computed(() => {
  if (!product.value.is_on_sale || product.value.discount_percentage === 0) {
    return ""
  }
  return `${product.value.discount_percentage}% OFF`
})

const renderStars = (rating) => {
  const filledStars = "★".repeat(Math.floor(rating))
  const emptyStars = "☆".repeat(5 - Math.floor(rating))
  return filledStars + emptyStars
}

const decreaseQty = () => {
  if (quantity.value > 1) {
    quantity.value -= 1
  }
}

const increaseQty = () => {
  if (quantity.value < currentStock.value) {
    quantity.value += 1
  }
}

const selectedColorName = computed(() =>
  displayColors.value.find((c) => toId(c.id) === toId(selectedColorId.value))?.name || ""
)

const selectedSizeName = computed(() =>
  displaySizes.value.find((s) => toId(s.id) === toId(selectedSizeId.value))?.name || ""
)

// Keep quantity within the stock of whichever variant is selected
watch(selectedVariant, () => {
  quantity.value = 1
})

const tabsEl = ref(null)
const scrollToTabs = () => {
  tabsEl.value?.scrollIntoView({ behavior: "smooth", block: "start" })
}

watch(() => route.params.id, () => {
  showTryOn.value = false
  selectedColorId.value = null
  selectedSizeId.value = null
  quantity.value = 1
  fetchProduct()
  fetchReviews()
})
</script>

<style scoped>
.crumbs {
  display: flex;
  flex-wrap: wrap;
  align-items: center;
  gap: 8px;
  font-size: 13px;
  color: var(--ink-soft);
  margin-bottom: 24px;
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

.crumbs .current {
  color: var(--ink);
  font-weight: 600;
  max-width: 260px;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.pd-grid {
  display: grid;
  gap: 32px;
}

@media (min-width: 992px) {
  .pd-grid {
    grid-template-columns: 1.15fr 1fr;
    gap: 64px;
    align-items: start;
  }
}

/* Gallery */
.gallery {
  display: flex;
  flex-direction: column-reverse;
  gap: 12px;
}

@media (min-width: 768px) {
  .gallery {
    flex-direction: row;
    gap: 16px;
  }
}

.thumbs {
  display: flex;
  gap: 10px;
  overflow-x: auto;
  -webkit-overflow-scrolling: touch;
  scrollbar-width: none;
}

@media (min-width: 768px) {
  .thumbs {
    flex-direction: column;
    overflow-x: visible;
    overflow-y: auto;
    max-height: 640px;
    flex-shrink: 0;
  }
}

.thumb-btn {
  width: 72px;
  height: 90px;
  flex-shrink: 0;
  padding: 0;
  border: 2px solid transparent;
  border-radius: 12px;
  overflow: hidden;
  background: var(--ivory);
  opacity: 0.6;
  transition: all 0.25s var(--ease);
}

@media (min-width: 768px) {
  .thumb-btn {
    width: 84px;
    height: 104px;
  }
}

.thumb-btn:hover {
  opacity: 1;
}

.thumb-btn.active {
  border-color: var(--ink);
  opacity: 1;
}

.thumb-btn img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.main-image {
  position: relative;
  flex: 1;
  aspect-ratio: 4 / 5;
  border-radius: var(--radius-lg);
  overflow: hidden;
  background: var(--ivory);
}

.main-image.loading {
  animation: pulse 1.4s ease-in-out infinite;
}

@keyframes pulse {
  50% { opacity: 0.6; }
}

.main-image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.6s var(--ease);
}

@media (hover: hover) {
  .main-image:hover img {
    transform: scale(1.04);
  }
}

.img-fade-enter-active,
.img-fade-leave-active {
  transition: opacity 0.25s ease;
}

.img-fade-enter-from,
.img-fade-leave-to {
  opacity: 0;
}

.sale-flag {
  position: absolute;
  top: 16px;
  left: 16px;
  padding: 6px 12px;
  border-radius: 999px;
  background: var(--accent);
  color: #fff;
  font-size: 12.5px;
  font-weight: 700;
}

.tryon-float {
  position: absolute;
  right: 16px;
  bottom: 16px;
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 10px 16px;
  border: 0;
  border-radius: 999px;
  background: rgba(14, 14, 16, 0.75);
  backdrop-filter: blur(10px);
  color: #fff;
  font-weight: 600;
  font-size: 14px;
  transition: background-color 0.2s, transform 0.25s var(--ease);
}

.tryon-float:hover {
  background: var(--accent);
  transform: translateY(-2px);
}

/* Info */
@media (min-width: 992px) {
  .info-sticky {
    position: sticky;
    top: calc(var(--header-h) + 24px);
  }
}

.pd-cat {
  font-size: 12px;
  font-weight: 700;
  letter-spacing: 0.14em;
  text-transform: uppercase;
  color: var(--muted);
}

.stock {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  font-size: 13px;
  font-weight: 600;
  padding: 4px 12px;
  border-radius: 999px;
}

.stock .dot {
  width: 7px;
  height: 7px;
  border-radius: 50%;
  background: currentColor;
}

.stock.in {
  color: var(--success);
  background: var(--success-soft);
}

.stock.out {
  color: var(--danger);
  background: #fdecea;
}

.pd-title {
  font-size: clamp(30px, 3.4vw, 44px);
  line-height: 1.08;
  letter-spacing: -0.035em;
  font-weight: 800;
  margin: 6px 0 14px;
}

.rating-line {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 0;
  border: 0;
  background: transparent;
  font-size: 14px;
  color: var(--ink);
}

.stars {
  display: inline-flex;
  gap: 2px;
  color: var(--star);
}

.price-row {
  display: flex;
  flex-wrap: wrap;
  align-items: center;
  gap: 12px;
  margin: 24px 0 8px;
  padding-bottom: 24px;
  border-bottom: 1px solid var(--line);
}

.price-now {
  font-size: 32px;
  font-weight: 800;
  letter-spacing: -0.03em;
}

.price-now.sale {
  color: var(--accent-ink);
}

.price-was {
  font-size: 18px;
  color: var(--muted);
  text-decoration: line-through;
}

.save-pill {
  padding: 4px 12px;
  border-radius: 999px;
  background: var(--accent-soft);
  color: var(--accent-ink);
  font-size: 13px;
  font-weight: 700;
}

.option-block {
  margin-top: 24px;
}

.option-label {
  font-weight: 700;
  font-size: 14px;
  margin-bottom: 12px;
}

.option-label span {
  font-weight: 500;
  color: var(--ink-soft);
  margin-left: 6px;
}

.option-btn {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  min-height: 44px;
  padding: 8px 18px;
  border: 1.5px solid var(--line);
  border-radius: 12px;
  background: #fff;
  font-size: 14px;
  font-weight: 600;
  color: var(--ink);
  transition: all 0.2s var(--ease);
}

.option-btn.size {
  min-width: 56px;
  justify-content: center;
}

.option-btn:hover {
  border-color: var(--ink);
}

.option-btn.active {
  background: var(--ink);
  border-color: var(--ink);
  color: #fff;
}

.swatch {
  width: 16px;
  height: 16px;
  border-radius: 50%;
  background: var(--sand);
  box-shadow: inset 0 0 0 1px rgba(14, 14, 16, 0.15);
}

.option-btn.active .swatch {
  box-shadow: 0 0 0 2px #fff;
}

.buy-row {
  display: flex;
  gap: 10px;
  margin-top: 32px;
}

.qty {
  display: flex;
  align-items: center;
  border: 1.5px solid var(--line);
  border-radius: 999px;
  padding: 4px;
}

.qty.disabled {
  opacity: 0.55;
}

.qty button {
  width: 40px;
  height: 40px;
  border: 0;
  border-radius: 50%;
  background: transparent;
  font-size: 18px;
  display: grid;
  place-items: center;
}

.qty button:not(:disabled):hover {
  background: var(--ivory);
}

.qty span {
  min-width: 24px;
  text-align: center;
  font-weight: 700;
}

.add-to-cart-btn {
  white-space: nowrap;
}

.add-to-cart-success {
  --bs-btn-bg: var(--success);
  --bs-btn-border-color: var(--success);
  --bs-btn-disabled-bg: var(--success);
  animation: addSuccessPulse 0.5s ease;
}

@keyframes addSuccessPulse {
  50% { transform: scale(1.03); }
}

.wishlist-heart-btn {
  width: 56px;
  flex-shrink: 0;
  border: 1.5px solid var(--line);
  border-radius: 50%;
  background: #fff;
  color: var(--ink);
  font-size: 20px;
  display: grid;
  place-items: center;
  transition: all 0.2s var(--ease);
}

.wishlist-heart-btn:hover {
  border-color: var(--ink);
}

.wishlist-heart-btn.active {
  color: var(--accent);
  border-color: var(--accent);
  background: var(--accent-soft);
}

@media (max-width: 420px) {
  .buy-row {
    flex-wrap: wrap;
  }

  .add-to-cart-btn {
    order: 3;
    flex-basis: 100%;
  }

  .wishlist-heart-btn {
    height: 50px;
    width: 50px;
    margin-left: auto;
  }
}

.low-stock {
  margin-top: 12px;
  font-size: 13px;
  font-weight: 600;
  color: var(--accent-ink);
}

.tryon-card {
  width: 100%;
  margin-top: 16px;
  display: flex;
  align-items: center;
  gap: 14px;
  padding: 14px 18px 14px 14px;
  border: 0;
  border-radius: var(--radius);
  color: #fff;
  background:
    radial-gradient(300px 120px at 100% 0%, rgba(255, 90, 54, 0.55), transparent 70%),
    var(--ink);
  transition: transform 0.3s var(--ease), box-shadow 0.3s;
}

.tryon-card:hover {
  transform: translateY(-2px);
  box-shadow: var(--shadow);
}

.tryon-icon {
  width: 44px;
  height: 44px;
  flex-shrink: 0;
  border-radius: 12px;
  background: rgba(255, 255, 255, 0.12);
  display: grid;
  place-items: center;
  font-size: 20px;
  color: var(--accent);
}

.assurances {
  list-style: none;
  padding: 0;
  margin: 24px 0 0;
  border: 1px solid var(--line);
  border-radius: var(--radius);
}

.assurances li {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 14px 16px;
  font-size: 14px;
  color: var(--ink-soft);
}

.assurances li + li {
  border-top: 1px solid var(--line);
}

.assurances i {
  font-size: 18px;
  color: var(--ink);
}

.assurances strong {
  color: var(--ink);
}

/* Tabs */
.tabs {
  margin-top: 72px;
  scroll-margin-top: calc(var(--header-h) + 16px);
}

.tab-header {
  display: flex;
  gap: 32px;
  border-bottom: 1px solid var(--line);
  margin-bottom: 32px;
}

.tab-btn {
  position: relative;
  padding: 0 0 16px;
  border: 0;
  background: transparent;
  font-size: 18px;
  font-weight: 700;
  color: var(--muted);
  transition: color 0.2s;
}

.tab-btn::after {
  content: "";
  position: absolute;
  left: 0;
  right: 0;
  bottom: -1px;
  height: 2px;
  background: var(--ink);
  transform: scaleX(0);
  transition: transform 0.35s var(--ease);
}

.tab-btn.active {
  color: var(--ink);
}

.tab-btn.active::after {
  transform: scaleX(1);
}

.tab-count {
  display: inline-grid;
  place-items: center;
  min-width: 24px;
  height: 22px;
  margin-left: 6px;
  padding: 0 7px;
  border-radius: 999px;
  background: var(--ivory);
  font-size: 12px;
  color: var(--ink);
}

.desc {
  max-width: 760px;
  color: var(--ink-soft);
  font-size: 16px;
  line-height: 1.75;
}

.reviews-layout {
  display: grid;
  gap: 32px;
}

@media (min-width: 992px) {
  .reviews-layout {
    grid-template-columns: 340px 1fr;
    gap: 56px;
    align-items: start;
  }
}

.review-summary {
  padding: 28px;
  border-radius: var(--radius-lg);
  background: var(--ivory);
}

.big-score {
  font-size: 56px;
  font-weight: 800;
  letter-spacing: -0.04em;
  line-height: 1;
  margin-bottom: 8px;
}

.review-form {
  margin-top: 24px;
  padding-top: 24px;
  border-top: 1px solid var(--line-strong);
}

.star-input {
  display: flex;
  gap: 4px;
}

.star-input button {
  border: 0;
  background: transparent;
  padding: 2px;
  font-size: 24px;
  color: var(--star);
  transition: transform 0.2s var(--ease);
}

.star-input button:hover {
  transform: scale(1.15);
}

.review-note {
  margin-top: 24px;
  padding-top: 20px;
  border-top: 1px solid var(--line-strong);
  font-size: 14px;
  color: var(--ink-soft);
}

.no-reviews {
  text-align: center;
  padding: 48px 16px;
  color: var(--ink-soft);
}

.no-reviews i {
  font-size: 32px;
  display: block;
  margin-bottom: 12px;
  color: var(--muted);
}

.review {
  display: flex;
  gap: 16px;
  padding: 22px 0;
  border-bottom: 1px solid var(--line);
}

.review:first-child {
  padding-top: 0;
}

.review:last-child {
  border-bottom: none;
}

.r-avatar {
  width: 44px;
  height: 44px;
  flex-shrink: 0;
  border-radius: 50%;
  background: var(--sand);
  display: grid;
  place-items: center;
  font-weight: 700;
}

.r-text {
  margin: 6px 0 0;
  color: var(--ink-soft);
}

.related-card {
  border-radius: var(--radius);
  overflow: hidden;
}
</style>
