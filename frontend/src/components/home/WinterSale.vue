<template>
  <section v-if="banner" class="section promo">
    <div class="container-xxl px-3 px-lg-5">
      <div class="promo-card" v-reveal>
        <div class="promo-media">
          <img :src="getImageUrl(banner.image)" :alt="banner.title" loading="lazy" />
        </div>
        <div class="promo-copy">
          <span class="promo-tag"><i class="bi bi-lightning-charge-fill"></i> Limited time</span>
          <h2 class="promo-title">{{ banner.title }}</h2>
          <p class="promo-text">{{ banner.description }}</p>
          <div class="d-flex flex-wrap gap-2">
            <router-link to="/sales" class="btn btn-dark btn-lg">
              Shop the sale <i class="bi bi-arrow-right ms-1"></i>
            </router-link>
            <router-link to="/shop" class="btn btn-outline-dark btn-lg">Browse all</router-link>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup>
import { ref, onMounted } from "vue";
import axios from "axios";
import { getImageUrl as resolveImageUrl } from "@/utils/imageUrl";

const banner = ref(null);

const fetchBanner = async () => {
  try {
    const response = await axios.get("/banners");
    // We'll use the most recent active banner for this section
    if (response.data.length > 0) {
      banner.value = response.data[0];
    }
  } catch (error) {
    console.error("Error fetching banner:", error);
  }
};

const getImageUrl = (path) => {
  if (path && (path.startsWith("http://") || path.startsWith("https://"))) {
    return path;
  }
  return resolveImageUrl(path);
};

onMounted(fetchBanner);
</script>

<style scoped>
.promo-card {
  display: grid;
  overflow: hidden;
  border-radius: var(--radius-xl);
  background: var(--accent-soft);
}

@media (min-width: 992px) {
  .promo-card {
    grid-template-columns: 1.1fr 1fr;
    min-height: 520px;
  }
}

.promo-media {
  position: relative;
  min-height: 300px;
  overflow: hidden;
}

.promo-media img {
  position: absolute;
  inset: 0;
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 1.4s var(--ease);
}

.promo-card:hover .promo-media img {
  transform: scale(1.04);
}

.promo-copy {
  padding: 40px 24px;
  display: flex;
  flex-direction: column;
  justify-content: center;
}

@media (min-width: 992px) {
  .promo-copy {
    padding: 64px;
  }
}

.promo-tag {
  align-self: flex-start;
  display: inline-flex;
  align-items: center;
  gap: 6px;
  padding: 6px 14px;
  border-radius: 999px;
  background: var(--accent);
  color: #fff;
  font-size: 12px;
  font-weight: 700;
  letter-spacing: 0.08em;
  text-transform: uppercase;
}

.promo-title {
  font-size: clamp(34px, 4.6vw, 64px);
  line-height: 1;
  letter-spacing: -0.035em;
  font-weight: 800;
  margin: 20px 0 16px;
}

.promo-text {
  color: var(--ink-2);
  font-size: 17px;
  max-width: 440px;
  margin-bottom: 32px;
}
</style>
