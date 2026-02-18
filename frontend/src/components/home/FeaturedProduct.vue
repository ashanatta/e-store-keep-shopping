<template>
  <div class="card product-card h-100">

    <router-link :to="`/product/${product.id}`">
      <div class="image-wrapper">
        <img :src="product.image" class="card-img-top" />

        <span
          v-if="product.originalPrice"
          class="badge bg-danger position-absolute top-0 start-0 m-2"
        >
          Sale
        </span>

        <span
          v-if="product.new"
          class="badge bg-primary position-absolute top-0 start-0 m-2"
        >
          New
        </span>

        <button
          class="wishlist-btn"
          @click.prevent="$emit('toggle-wishlist', product.id)"
        >
          ♥
        </button>
      </div>
    </router-link>

    <div class="card-body">
      <router-link :to="`/product/${product.id}`" class="text-dark text-decoration-none">
        <h6 class="mb-1">{{ product.name }}</h6>

        <div class="mb-2 small text-muted">
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
</template>

<script setup>
defineProps({
  product: Object
})
</script>

<style scoped>
.product-card {
  transition: 0.3s ease;
  cursor: pointer;
}

.product-card:hover {
  transform: translateY(-8px);
}

.image-wrapper {
  position: relative;
  overflow: hidden;
  aspect-ratio: 3/4;
}

.card-img-top {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: 0.4s ease;
}

.product-card:hover .card-img-top {
  transform: scale(1.1);
}

.wishlist-btn {
  position: absolute;
  top: 10px;
  right: 10px;
  background: white;
  border: none;
  border-radius: 50%;
  padding: 6px 10px;
  cursor: pointer;
}
</style>
