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
                Rs. {{ product.price }}
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
export default {
  name: "RecommendationProducts",

  data() {
    return {
      products: [
        {
          id: 1,
          name: "Men Casual Shirt",
          image:
            "https://images.unsplash.com/photo-1521572163474-6864f9cf17ab",
          price: 29.99,
          originalPrice: 49.99,
          rating: 4.5,
          reviews: 120,
          new: true,
        },
        {
          id: 2,
          name: "Women Summer Dress",
          image:
            "https://images.unsplash.com/photo-1521572163474-6864f9cf17ab",
          price: 39.99,
          originalPrice: null,
          rating: 4.2,
          reviews: 90,
          new: false,
        },
        {
          id: 3,
          name: "Sports Sneakers",
          image:
            "https://images.unsplash.com/photo-1542291026-7eec264c27ff",
          price: 59.99,
          originalPrice: 79.99,
          rating: 4.7,
          reviews: 210,
          new: true,
        },
        {
          id: 4,
          name: "Leather Handbag",
          image:
            "https://images.unsplash.com/photo-1584917865442-de89df76afd3",
          price: 89.99,
          originalPrice: null,
          rating: 4.3,
          reviews: 65,
          new: false,
        }
      ]
    };
  },

  methods: {
    addToCart(product) {        
      this.$emit("add-to-cart", product);
    }
  }
};
</script>

<style scoped>
.card-img-top {
  height: 180px;
  object-fit: cover;
}
</style>
