<template>
  <section class="category-section">
    <div class="container">
      <h2 class="title text-center">Shop by Category</h2>

      <div class="category-grid">
        <div
          v-for="(category, index) in categories"
          :key="category.name"
          class="category-card"
        >
          <router-link :to="category.path">
            <div class="image-wrapper">
              <img
                :src="category.image"
                :alt="category.name"
                class="category-image"
              />

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
const categories = [
  {
    name: "Men",
    path: "/men",
    image: "https://images.unsplash.com/photo-1520975918318-7f2b6a29b6c3"
  },
  {
    name: "Women",
    path: "/women",
    image: "https://images.unsplash.com/photo-1490481651871-ab68de25d43d"
  },
  {
    name: "Kids",
    path: "/kids",
    image: "https://images.unsplash.com/photo-1503454537195-1dcabb73ffb9"
  }
]
</script>

<style scoped>
.category-section {
  padding: 80px 0;
  background: #f8f9fa;
}

.title {
  margin-bottom: 50px;
  font-weight: 600;
}

.category-grid {
  display: grid;
  grid-template-columns: repeat(1, 1fr);
  gap: 30px;
}

@media (min-width: 768px) {
  .category-grid {
    grid-template-columns: repeat(3, 1fr);
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
