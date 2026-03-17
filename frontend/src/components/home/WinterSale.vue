<template>
  <section v-if="banner" class="winter-sale py-5">
    <div class="container">
      <div class="row align-items-center g-5">

        <!-- Text Side -->
        <div class="col-md-6 sale-text">
          <h2 class="animate-up">{{ banner.title }}</h2>
          <p class="animate-up delay-1">
            {{ banner.description }}
          </p>
          <router-link to="/shop" class="sale-btn animate-up delay-2 text-decoration-none d-inline-block">
            Shop Collection
          </router-link>
        </div>

        <!-- Image Side -->
        <div class="col-md-6">
          <div class="image-wrapper shadow-lg">
            <img
              :src="getImageUrl(banner.image)"
              :alt="banner.title"
            />
          </div>
        </div>

      </div>
    </div>
  </section>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'
import { getImageUrl as resolveImageUrl } from '@/utils/imageUrl'

const banner = ref(null)

const fetchBanner = async () => {
  try {
    const response = await axios.get('/banners')
    // We'll use the most recent active banner for this section
    if (response.data.length > 0) {
      banner.value = response.data[0]
    }
  } catch (error) {
    console.error('Error fetching banner:', error)
  }
}

const getImageUrl = (path) => {
  if (path && (path.startsWith('http://') || path.startsWith('https://'))) {
    return path
  }
  return resolveImageUrl(path)
}

onMounted(fetchBanner)
</script>

<style scoped>
.winter-sale {
  background: #000000;
  color: white;
}

.winter-sale .container {
  padding-left: 1rem;
  padding-right: 1rem;
}

@media (min-width: 768px) {
  .winter-sale .container {
    padding-left: 12px;
    padding-right: 12px;
  }
}

.sale-text h2 {
  font-size: 28px;
  font-weight: 700;
}

.sale-text p {
  font-size: 15px;
  margin: 16px 0;
  opacity: 0.9;
}

@media (min-width: 768px) {
  .sale-text h2 {
    font-size: 42px;
  }

  .sale-text p {
    font-size: 18px;
    margin: 20px 0;
  }
}

.sale-btn {
  padding: 12px 30px;
  background: white;
  color: #050505;
  border: none;
  border-radius: 30px;
  font-weight: 600;
  transition: 0.3s ease;
}

.sale-btn:hover {
  background: black;
  color: white;
}

.image-wrapper {
  aspect-ratio: 4/3;
  border-radius: 15px;
  overflow: hidden;
}

.image-wrapper img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: 0.4s ease;
}

.image-wrapper img:hover {
  transform: scale(1.05);
}

@media (max-width: 576px) {
  .sale-text h2 {
    font-size: 24px;
  }

  .sale-btn {
    padding: 10px 24px;
    font-size: 0.9rem;
  }
}

/* Animations */
.animate-up {
  animation: slideUp 0.8s cubic-bezier(0.23, 1, 0.32, 1) both;
}

.delay-1 { animation-delay: 0.2s; }
.delay-2 { animation-delay: 0.4s; }

@keyframes slideUp {
  from {
    opacity: 0;
    transform: translateY(30px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}
</style>
