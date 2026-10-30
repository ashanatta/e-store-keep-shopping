<template>
  <section v-if="loading || products.length" class="section recommendation-section">
    <div class="container-xxl px-3 px-lg-5">
      <div class="section-head" v-reveal>
        <div>
          <span class="eyebrow">Picked by shoppers like you</span>
          <h2 class="section-title">You might <em>also love</em></h2>
        </div>
        <router-link to="/shop" class="link-arrow">
          Explore more <i class="bi bi-arrow-right"></i>
        </router-link>
      </div>

      <div class="rec-grid">
        <template v-if="loading">
          <ProductCardSkeleton v-for="n in 4" :key="n" />
        </template>
        <template v-else>
          <div v-for="(product, i) in products" :key="product.id" v-reveal="i * 80">
            <ProductCard :product="product" />
          </div>
        </template>
      </div>
    </div>
  </section>
</template>

<script>
import axios from "axios";
import { normalizeProduct } from "@/utils/product";
import ProductCard from "@/components/common/ProductCard.vue";
import ProductCardSkeleton from "@/components/common/ProductCardSkeleton.vue";

export default {
  name: "RecommendationProducts",
  components: { ProductCard, ProductCardSkeleton },

  data() {
    return {
      products: [],
      loading: true,
    };
  },

  methods: {
    async fetchRecommended() {
      this.loading = true;
      try {
        const response = await axios.get("/recommendations/users-like-you", {
          params: { limit: 4 },
        });
        this.products = (response.data || []).map(normalizeProduct);
      } catch (error) {
        console.error("Error fetching recommendations:", error);
      } finally {
        this.loading = false;
      }
    },
  },

  mounted() {
    this.fetchRecommended();
  },
};
</script>

<style scoped>
.recommendation-section {
  background: var(--ivory);
}

.rec-grid {
  display: grid;
  grid-template-columns: repeat(2, minmax(0, 1fr));
  gap: 32px 16px;
}

@media (min-width: 992px) {
  .rec-grid {
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 40px 24px;
  }
}
</style>
