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
          <span class="fs-4 fw-bold">${{ product.price.toFixed(2) }}</span>
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

        <div class="mb-4">
          <div class="fw-semibold mb-2">Color: Select a color</div>
          <div class="d-flex flex-wrap gap-2">
            <button
              v-for="color in product.colors"
              :key="color"
              type="button"
              class="option-btn"
              :class="{ active: selectedColor === color }"
              @click="selectedColor = color"
            >
              {{ color }}
            </button>
          </div>
        </div>

        <div class="mb-4">
          <div class="fw-semibold mb-2">Size: Select a size</div>
          <div class="d-flex flex-wrap gap-2">
            <button
              v-for="size in product.sizes"
              :key="size"
              type="button"
              class="option-btn"
              :class="{ active: selectedSize === size }"
              @click="selectedSize = size"
            >
              {{ size }}
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
                <div class="text-muted small">${{ item.price.toFixed(2) }}</div>
              </div>
            </div>
          </router-link>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, ref, watch } from "vue"
import { useRoute } from "vue-router"

const route = useRoute()

const products = [
  {
    id: 1,
    name: "Classic Oxford Shirt",
    category: "Men",
    type: "Shirts",
    images: [
      "https://images.unsplash.com/photo-1521572163474-6864f9cf17ab",
      "https://images.unsplash.com/photo-1542272604-787c3835535d",
      "https://images.unsplash.com/photo-1520975918318-7f2b6a29b6c3"
    ],
    image: "https://images.unsplash.com/photo-1521572163474-6864f9cf17ab",
    price: 49.99,
    originalPrice: 79.99,
    rating: 4.5,
    reviews: 127,
    sizes: ["S", "M", "L", "XL", "XXL"],
    colors: ["White", "Blue", "Light Gray"],
    description:
      "A timeless oxford shirt crafted from premium cotton. Perfect for both casual and formal occasions.",
    details: [
      "Category: Men",
      "Available Sizes: S, M, L, XL, XXL",
      "Available Colors: White, Blue, Light Gray",
      "Material: Premium quality fabric",
      "Care: Machine washable"
    ],
    reviewsSummary: [
      {
        name: "Ali Khan",
        text: "Great fit and premium feel. Works perfectly with suits."
      },
      {
        name: "Sara Ahmed",
        text: "Fabric feels soft and breathable. Highly recommended."
      }
    ]
  },
  {
    id: 2,
    name: "Slim Fit Denim Jeans",
    category: "Men",
    type: "Jeans",
    images: [
      "https://images.unsplash.com/photo-1542272604-787c3835535d",
      "https://images.unsplash.com/photo-1521572163474-6864f9cf17ab",
      "https://images.unsplash.com/photo-1503342217505-b0a15ec3261c"
    ],
    image: "https://images.unsplash.com/photo-1542272604-787c3835535d",
    price: 89.99,
    originalPrice: null,
    rating: 4.7,
    reviews: 203,
    sizes: ["30", "32", "34", "36"],
    colors: ["Blue", "Navy"],
    description:
      "A refined slim-fit jean built for comfort and everyday wear with stretch denim.",
    details: [
      "Category: Men",
      "Available Sizes: 30, 32, 34, 36",
      "Available Colors: Blue, Navy",
      "Material: Stretch denim",
      "Care: Machine washable"
    ],
    reviewsSummary: [
      {
        name: "Imran Ali",
        text: "Excellent quality and great fit."
      },
      {
        name: "Hina Raza",
        text: "Comfortable for all-day wear."
      }
    ]
  },
  {
    id: 3,
    name: "Leather Jacket",
    category: "Men",
    type: "Jackets",
    images: [
      "https://images.unsplash.com/photo-1520975918318-7f2b6a29b6c3",
      "https://images.unsplash.com/photo-1512436991641-6745cdb1723f",
      "https://images.unsplash.com/photo-1542291026-7eec264c27ff"
    ],
    image: "https://images.unsplash.com/photo-1520975918318-7f2b6a29b6c3",
    price: 299.99,
    originalPrice: 359.99,
    rating: 4.6,
    reviews: 89,
    sizes: ["M", "L", "XL"],
    colors: ["Black"],
    description:
      "A premium leather jacket with timeless styling and a tailored silhouette.",
    details: [
      "Category: Men",
      "Available Sizes: M, L, XL",
      "Available Colors: Black",
      "Material: Genuine leather",
      "Care: Dry clean only"
    ],
    reviewsSummary: [
      {
        name: "Omar Qureshi",
        text: "Looks amazing and fits perfectly."
      },
      {
        name: "Fatima Noor",
        text: "Best leather jacket I have bought."
      }
    ]
  },
  {
    id: 4,
    name: "Performance Polo",
    category: "Men",
    type: "Shirts",
    images: [
      "https://images.unsplash.com/photo-1521572163474-6864f9cf17ab",
      "https://images.unsplash.com/photo-1512436991641-6745cdb1723f",
      "https://images.unsplash.com/photo-1503342217505-b0a15ec3261c"
    ],
    image: "https://images.unsplash.com/photo-1521572163474-6864f9cf17ab",
    price: 39.99,
    originalPrice: null,
    rating: 4.4,
    reviews: 156,
    sizes: ["S", "M", "L", "XL"],
    colors: ["Green", "Red"],
    description:
      "Breathable performance polo designed for everyday comfort and smart style.",
    details: [
      "Category: Men",
      "Available Sizes: S, M, L, XL",
      "Available Colors: Green, Red",
      "Material: Performance knit",
      "Care: Machine washable"
    ],
    reviewsSummary: [
      {
        name: "Umar Farooq",
        text: "Comfortable and looks great."
      },
      {
        name: "Anum Tariq",
        text: "Nice colors and soft fabric."
      }
    ]
  }
]

const productId = computed(() => Number(route.params.id))
const product = computed(() => products.find((item) => item.id === productId.value) || products[0])

const selectedImage = ref(product.value.image)
const selectedColor = ref(product.value.colors[0])
const selectedSize = ref(product.value.sizes[0])
const quantity = ref(1)
const activeTab = ref("description")

watch(product, (value) => {
  selectedImage.value = value.image
  selectedColor.value = value.colors[0]
  selectedSize.value = value.sizes[0]
  quantity.value = 1
  activeTab.value = "description"
})

const relatedProducts = computed(() =>
  products.filter((item) => item.id !== product.value.id).slice(0, 3)
)

const discountLabel = computed(() => {
  if (!product.value.originalPrice) {
    return ""
  }
  const percent = Math.round(
    ((product.value.originalPrice - product.value.price) / product.value.originalPrice) * 100
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
