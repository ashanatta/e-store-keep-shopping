<template>
  <section v-if="categories.length" class="section category-section">
    <div class="container-xxl px-3 px-lg-5">
      <div class="section-head" v-reveal>
        <div>
          <span class="eyebrow">Collections</span>
          <h2 class="section-title">Shop by <em>category</em></h2>
        </div>
        <p class="section-sub">
          From weekend basics to statement pieces — find the edit made for you.
        </p>
      </div>

      <div class="category-grid" :class="{ bento: categories.length === 3 }">
        <router-link
          v-for="(category, i) in categories"
          :key="category.id"
          :to="`/${category.name.toLowerCase()}`"
          class="category-card"
          v-reveal="i * 100"
        >
          <img
            v-if="category.image"
            :src="getImageUrl(category.image)"
            :alt="category.name"
            class="category-image"
            loading="lazy"
          />
          <div v-else class="category-image placeholder">
            <i class="bi bi-image"></i>
          </div>

          <div class="overlay"></div>

          <div class="content">
            <span class="count">0{{ i + 1 }}</span>
            <div class="content-row">
              <h3>{{ category.name }}</h3>
              <span class="go"><i class="bi bi-arrow-up-right"></i></span>
            </div>
          </div>
        </router-link>
      </div>
    </div>
  </section>
</template>

<script setup>
import { onMounted } from "vue";
import { useCategories } from "@/composables/useCategories.js";
import { getImageUrl } from "@/utils/imageUrl";

const { categories, fetchCategories } = useCategories();

onMounted(async () => {
  await fetchCategories();
});
</script>

<style scoped>
.category-grid {
  display: grid;
  grid-template-columns: 1fr;
  gap: 16px;
}

@media (min-width: 576px) {
  .category-grid {
    grid-template-columns: repeat(2, 1fr);
  }
}

@media (min-width: 992px) {
  .category-grid {
    grid-template-columns: repeat(3, 1fr);
    gap: 24px;
  }

  .category-grid.bento {
    grid-template-columns: 1.25fr 1fr;
    grid-template-rows: repeat(2, 300px);
  }

  .category-grid.bento .category-card:first-child {
    grid-row: span 2;
    aspect-ratio: auto;
  }

  .category-grid.bento .category-card:not(:first-child) {
    aspect-ratio: auto;
  }
}

.category-card {
  position: relative;
  display: block;
  overflow: hidden;
  border-radius: var(--radius-lg);
  aspect-ratio: 4 / 5;
  background: var(--sand);
  color: #fff;
  text-decoration: none;
  isolation: isolate;
}

.category-image {
  position: absolute;
  inset: 0;
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 1.2s var(--ease);
}

.category-image.placeholder {
  display: grid;
  place-items: center;
  font-size: 40px;
  color: var(--muted);
}

.category-card:hover .category-image {
  transform: scale(1.07);
}

.overlay {
  position: absolute;
  inset: 0;
  background: linear-gradient(to top, rgba(14, 14, 16, 0.7) 0%, rgba(14, 14, 16, 0.1) 50%, transparent 70%);
}

.content {
  position: absolute;
  inset: 0;
  padding: 24px;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
}

.count {
  align-self: flex-start;
  padding: 4px 12px;
  border-radius: 999px;
  background: rgba(255, 255, 255, 0.18);
  backdrop-filter: blur(8px);
  font-size: 12px;
  font-weight: 700;
  letter-spacing: 0.08em;
}

.content-row {
  display: flex;
  align-items: flex-end;
  justify-content: space-between;
  gap: 16px;
}

.content h3 {
  color: #fff;
  margin: 0;
  font-family: var(--font-display);
  font-weight: 400;
  font-size: clamp(36px, 4vw, 56px);
  line-height: 1;
  letter-spacing: -0.01em;
}

.go {
  width: 52px;
  height: 52px;
  flex-shrink: 0;
  border-radius: 50%;
  background: #fff;
  color: var(--ink);
  display: grid;
  place-items: center;
  font-size: 18px;
  transition: transform 0.5s var(--ease), background-color 0.3s;
}

.category-card:hover .go {
  transform: rotate(45deg);
  background: var(--accent);
  color: #fff;
}
</style>
