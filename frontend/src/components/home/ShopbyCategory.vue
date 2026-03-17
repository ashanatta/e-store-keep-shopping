<template>
  <section class="category-section">
    <div class="container">
      <h2 class="title text-center">Shop by Category</h2>

      <div class="category-grid">
        <div
          v-for="category in categories"
          :key="category.id"
          class="category-card"
        >
          <router-link :to="`/${category.name.toLowerCase()}`">
            <div class="image-wrapper">
              <img
                v-if="category.image"
                :src="getImageUrl(category.image)"
                :alt="category.name"
                class="category-image"
              />
              <div v-else class="category-image bg-light d-flex align-items-center justify-content-center">
                <i class="bi bi-image text-muted fs-1"></i>
              </div>

              <div class="overlay"></div>

              <div class="content">
                <h3>{{ category.name }}</h3>
                <button class="shop-btn">
                  Shop Now →
                </button>
              </div>
            </div>
          </router-link>
        </div>
      </div>

    </div>
  </section>
</template>

<script setup>
import { onMounted } from "vue"
import { useCategories } from "@/composables/useCategories.js"
import { getImageUrl } from "@/utils/imageUrl"

const { categories, fetchCategories } = useCategories()

onMounted(async () => {
  await fetchCategories()
})
</script>

<style scoped>
.category-section {
  padding: 40px 0;
  background: #f8f9fa;
}

@media (min-width: 768px) {
  .category-section {
    padding: 80px 0;
  }
}

.title {
  margin-bottom: 30px;
  font-weight: 600;
  font-size: 1.5rem;
}

@media (min-width: 768px) {
  .title {
    margin-bottom: 50px;
    font-size: inherit;
  }
}

.category-grid {
  display: grid;
  grid-template-columns: repeat(1, 1fr);
  gap: 16px;
}

@media (min-width: 576px) {
  .category-grid {
    gap: 20px;
  }
}

@media (min-width: 768px) {
  .category-grid {
    grid-template-columns: repeat(3, 1fr);
    gap: 30px;
  }
}

.category-card {
  overflow: hidden;
  border-radius: 12px;
  cursor: pointer;
}

.image-wrapper {
  position: relative;
  overflow: hidden;
  aspect-ratio: 4/5;
}

.category-image {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.5s ease;
}

.category-card:hover .category-image {
  transform: scale(1.1);
}

.overlay {
  position: absolute;
  inset: 0;
  background: linear-gradient(to top, rgba(0,0,0,0.6), transparent);
}

.content {
  position: absolute;
  bottom: 0;
  left: 0;
  right: 0;
  padding: 20px;
  color: white;
}

.shop-btn {
  margin-top: 10px;
  background: white;
  color: black;
  border: none;
  padding: 6px 14px;
  border-radius: 6px;
  font-size: 14px;
  cursor: pointer;
}

.shop-btn:hover {
  background: black;
  color: white;
}
</style>
