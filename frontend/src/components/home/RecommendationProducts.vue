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
          <img :src="product.image" class="card-img-top" alt="product" />

          <div class="card-body">
            <h6 class="card-title">{{ product.name }}</h6>

            <!-- Price Section -->
            <p class="card-text">
              <span class="fw-bold text-dark">
                Rs. {{ product.base_price }}
              </span>

              <span
                v-if="product.originalPrice"
                class="text-muted text-decoration-line-through ms-2"
              >
                Rs. {{ product.originalPrice }}
              </span>
            </p>

            <!-- Rating -->
            <p class="text-warning mb-1">
              ⭐ {{ product.rating }} ({{ product.reviews }} reviews)
            </p>

            <!-- New Badge -->
            <span
              v-if="product.new"
              class="badge bg-success"
            >
              NEW
            </span>
          </div>

          <div class="card-footer text-center">
            <button
              class="btn btn-primary btn-sm"
              @click="addToCart(product)"
            >
              Add to Cart
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios'

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
          image: p.image ? `http://localhost:8000/storage/${p.image}` : 'https://via.placeholder.com/300x400',
          originalPrice: null,
          rating: 4.5,
          reviews: 5,
          new: false
        }))
      } catch (error) {
        console.error('Error fetching recommendations:', error)
      }
    },
    addToCart(product) {
      // Basic add to cart logic, can be enhanced
      console.log('Add to cart:', product)
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
