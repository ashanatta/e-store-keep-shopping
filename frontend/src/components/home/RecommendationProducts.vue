<template>
  <div class="container mt-4">
    <h4>Recommended Products</h4>

    <div class="row">
      <div
        class="col-md-3 mb-3"
        v-for="product in products"
        :key="product.id"
      >
        <div class="card h-100">
          <router-link :to="`/product/${product.id}`" class="text-decoration-none">
            <img :src="product.image" class="card-img-top" alt="product" />

            <div class="card-body">
              <h6 class="card-title text-dark">{{ product.name }}</h6>

              <!-- Price Section -->
              <p class="card-text">
                <span class="fw-bold text-dark">
                  <template v-if="product.displayPrice !== null">Rs. {{ product.displayPrice.toFixed(2) }}</template>
                  <template v-else>Select options</template>
                </span>

                <span
                  v-if="product.originalPrice && product.displayPrice !== product.originalPrice"
                  class="text-muted text-decoration-line-through ms-2"
                >
                  Rs. {{ product.originalPrice.toFixed(2) }}
                </span>
              </p>

              <!-- Rating -->
              <p class="text-warning mb-1">
                <span class="text-warning">{{ renderStars(product.rating) }}</span> {{ product.rating.toFixed(1) }} ({{ product.reviews }} reviews)
              </p>

            </div>
          </router-link>

          <div class="card-footer text-center bg-transparent border-0 pb-3">
            <router-link
              :to="`/product/${product.id}`"
              class="btn btn-dark btn-sm w-100 rounded-pill"
            >
              View Product
            </router-link>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios'
import { getImageUrl } from '@/utils/imageUrl'

export default {
  name: "RecommendationProducts",

  data() {
    return {
      products: []
    }
  },
  
  methods: {
    async fetchRecommended() {
      try {
        const response = await axios.get('/products')
        // Take random 4 products or specific ones
        this.products = response.data.slice(0, 4).map(p => ({
          ...p,
          image: getImageUrl(p.image),
          displayPrice: Number(p.final_price || p.min_variant_price || 0) || null,
          originalPrice: Boolean(p.is_on_sale) ? (Number(p.min_variant_price || 0) || null) : null,
          rating: Number(p.reviews_avg_rating || 0),
          reviews: Number(p.reviews_count || 0)
        }))
      } catch (error) {
        console.error('Error fetching recommendations:', error)
      }
    },
    addToCart(product) {
      // Basic add to cart logic, can be enhanced
      console.log('Add to cart:', product)
    },
    renderStars(rating) {
      const filledStars = "★".repeat(Math.floor(rating))
      const emptyStars = "☆".repeat(5 - Math.floor(rating))
      return filledStars + emptyStars
    }
  },
  
  mounted() {
    this.fetchRecommended()
  }
}
</script>

<style scoped>
.card-img-top {
  height: 180px;
  object-fit: cover;
}
</style>
