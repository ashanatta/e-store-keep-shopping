<template>
  <div class="home">
    <!-- Hero -->
    <section class="hero">
      <div class="hero-glow" aria-hidden="true"></div>
      <div class="container-xxl hero-inner">
        <div class="hero-copy">
          <span class="eyebrow hero-in" style="--d: 0ms">Spring / Summer Collection {{ year }}</span>
          <h1 class="hero-title hero-in" style="--d: 80ms">
            Wear the <em>moment</em>,<br class="d-none d-sm-block" /> not just the trend.
          </h1>
          <p class="hero-sub hero-in" style="--d: 160ms">
            Discover pieces made for everyday life — clean cuts, quality fabrics and up to
            <strong>50% off</strong> on selected styles.
          </p>
          <div class="hero-actions hero-in" style="--d: 240ms">
            <router-link to="/shop" class="btn btn-dark btn-lg">
              Shop the collection <i class="bi bi-arrow-right ms-1"></i>
            </router-link>
            <router-link to="/sales" class="btn btn-outline-dark btn-lg">View Sale</router-link>
          </div>
          <ul class="hero-perks hero-in" style="--d: 320ms">
            <li><i class="bi bi-truck"></i> Free shipping $100+</li>
            <li><i class="bi bi-arrow-repeat"></i> 30-day returns</li>
            <li><i class="bi bi-shield-lock"></i> Secure checkout</li>
          </ul>
        </div>

        <div class="hero-visual">
          <div class="hv-main hero-in" style="--d: 120ms">
            <img :src="heroImages.main" alt="Model wearing the new collection" @error="hideImg" />
          </div>
          <div class="hv-side hero-in" style="--d: 260ms">
            <img :src="heroImages.side" alt="Clothes on a rail" @error="hideImg" />
          </div>
          <div class="hv-card glass hero-in" style="--d: 420ms">
            <span class="hv-icon"><i class="bi bi-stars"></i></span>
            <div>
              <div class="fw-bold">AI Virtual Try-On</div>
              <div class="small text-muted">See it on you before you buy</div>
            </div>
          </div>
          <div class="hv-badge hero-in" style="--d: 520ms">
            <span>Up to</span>
            <strong>50%</strong>
            <span>off</span>
          </div>
        </div>
      </div>
    </section>

    <!-- Marquee -->
    <div class="marquee" aria-hidden="true">
      <div class="marquee-track">
        <span v-for="n in 2" :key="n" class="marquee-group">
          <template v-for="word in marqueeWords" :key="word">
            <span>{{ word }}</span><i>✦</i>
          </template>
        </span>
      </div>
    </div>

    <!-- Categories -->
    <ShopbyCategory />

    <!-- Featured products -->
    <section class="section pt-0">
      <div class="container-xxl px-3 px-lg-5">
        <div class="section-head" v-reveal>
          <div>
            <span class="eyebrow">Handpicked for you</span>
            <h2 class="section-title">Featured <em>pieces</em></h2>
          </div>
          <router-link to="/shop" class="link-arrow">
            View all products <i class="bi bi-arrow-right"></i>
          </router-link>
        </div>

        <div class="product-grid">
          <template v-if="loading">
            <ProductCardSkeleton v-for="n in 8" :key="n" />
          </template>
          <template v-else>
            <div v-for="(product, i) in products" :key="product.id" v-reveal="(i % 4) * 80">
              <ProductCard :product="product" />
            </div>
          </template>
        </div>

        <div v-if="!loading && products.length === 0" class="empty-note">
          New arrivals are on their way — check back soon.
        </div>
      </div>
    </section>

    <!-- Value props -->
    <section class="perks-strip">
      <div class="container-xxl px-3 px-lg-5">
        <div class="perks-grid">
          <div v-for="(perk, i) in perks" :key="perk.title" class="perk" v-reveal="i * 80">
            <span class="perk-icon"><i :class="perk.icon"></i></span>
            <div>
              <div class="perk-title">{{ perk.title }}</div>
              <div class="perk-text">{{ perk.text }}</div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Banner from admin -->
    <WinterSale />

    <!-- AI Try-on -->
    <section class="section tryon">
      <div class="container-xxl px-3 px-lg-5">
        <div class="tryon-card" v-reveal>
          <div class="tryon-copy">
            <span class="eyebrow eyebrow-light">Only at Doller Shope</span>
            <h2 class="section-title text-white">
              Try it on <em>virtually</em> — before it ships.
            </h2>
            <p class="tryon-sub">
              Upload a photo and our AI shows how the garment looks on you. Fewer returns, more
              confidence.
            </p>
            <ol class="tryon-steps">
              <li><span>01</span> Open any product you love</li>
              <li><span>02</span> Tap <strong>“Try it on with AI”</strong></li>
              <li><span>03</span> Upload your photo &amp; see the result</li>
            </ol>
            <router-link to="/shop" class="btn btn-accent btn-lg">
              Start exploring <i class="bi bi-arrow-right ms-1"></i>
            </router-link>
          </div>
          <div class="tryon-visual">
            <div class="tv-frame">
              <img :src="heroImages.tryon" alt="Virtual try-on preview" @error="hideImg" />
              <div class="tv-scan" aria-hidden="true"></div>
              <span class="tv-chip"><i class="bi bi-magic"></i> Generating your look…</span>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Recommendations -->
    <RecommendationProducts />
  </div>
</template>

<script>
import ShopbyCategory from "@/components/home/ShopbyCategory.vue";
import WinterSale from "@/components/home/WinterSale.vue";
import RecommendationProducts from "@/components/home/RecommendationProducts.vue";
import ProductCard from "@/components/common/ProductCard.vue";
import ProductCardSkeleton from "@/components/common/ProductCardSkeleton.vue";
import axios from "axios";
import { normalizeProduct } from "@/utils/product";
import { useAuth } from "@/composables/useAuth.js";
import { useWishlist } from "@/composables/useWishlist.js";

export default {
  name: "Home",
  components: {
    ShopbyCategory,
    WinterSale,
    RecommendationProducts,
    ProductCard,
    ProductCardSkeleton,
  },
  data() {
    return {
      products: [],
      loading: true,
      year: new Date().getFullYear(),
      heroImages: {
        main: "https://images.unsplash.com/photo-1515886657613-9f3515b0c78f?auto=format&fit=crop&w=1000&q=80",
        side: "https://images.unsplash.com/photo-1490481651871-ab68de25d43d?auto=format&fit=crop&w=700&q=80",
        tryon: "https://images.unsplash.com/photo-1469334031218-e382a71b716b?auto=format&fit=crop&w=900&q=80",
      },
      marqueeWords: ["New Arrivals", "Men", "Women", "Kids", "Seasonal Sale", "Everyday Essentials"],
      perks: [
        { icon: "bi bi-truck", title: "Free shipping", text: "On every order over $100" },
        { icon: "bi bi-arrow-repeat", title: "Easy returns", text: "30 days, no questions asked" },
        { icon: "bi bi-shield-lock", title: "Secure payment", text: "Stripe, PayPal or cash" },
        { icon: "bi bi-headset", title: "Live support", text: "Chat with us anytime" },
      ],
    };
  },
  methods: {
    hideImg(e) {
      e.target.style.visibility = "hidden";
    },
    async fetchProducts() {
      this.loading = true;
      try {
        const response = await axios.get("/products", { params: { limit: 8 } });
        this.products = (response.data || []).slice(0, 8).map(normalizeProduct);
      } catch (error) {
        console.error("Error fetching featured products:", error);
      } finally {
        this.loading = false;
      }
    },
  },
  mounted() {
    this.fetchProducts();
    const { isAuthenticated } = useAuth();
    if (isAuthenticated.value) {
      useWishlist().fetchWishlist().catch(() => {});
    }
  },
};
</script>

<style scoped>
/* ---------- Hero ---------- */
.hero {
  position: relative;
  overflow: hidden;
  background: var(--ivory);
}

.hero-glow {
  position: absolute;
  width: 720px;
  height: 720px;
  right: -180px;
  top: -260px;
  border-radius: 50%;
  background: radial-gradient(circle, rgba(255, 90, 54, 0.22), transparent 65%);
  filter: blur(10px);
  pointer-events: none;
}

.hero-inner {
  position: relative;
  display: grid;
  gap: 48px;
  padding: 48px 16px 64px;
  align-items: center;
}

@media (min-width: 992px) {
  .hero-inner {
    grid-template-columns: 1.05fr 1fr;
    gap: 64px;
    padding: 64px 48px 96px;
    min-height: min(calc(100vh - var(--header-h) - 36px), 860px);
  }
}

.hero-title {
  font-size: clamp(44px, 7vw, 96px);
  line-height: 0.98;
  letter-spacing: -0.045em;
  font-weight: 800;
  margin: 20px 0 24px;
}

.hero-title em {
  font-family: var(--font-display);
  font-weight: 400;
  font-style: italic;
  letter-spacing: -0.02em;
  color: var(--accent);
}

.hero-sub {
  font-size: clamp(16px, 1.4vw, 19px);
  color: var(--ink-soft);
  max-width: 500px;
  margin-bottom: 32px;
}

.hero-sub strong {
  color: var(--ink);
}

.hero-actions {
  display: flex;
  flex-wrap: wrap;
  gap: 12px;
}

.hero-perks {
  list-style: none;
  padding: 0;
  margin: 40px 0 0;
  display: flex;
  flex-wrap: wrap;
  gap: 10px 24px;
  font-size: 13.5px;
  font-weight: 600;
  color: var(--ink-2);
}

.hero-perks i {
  color: var(--accent);
  margin-right: 6px;
}

/* Visual collage */
.hero-visual {
  position: relative;
  height: clamp(420px, 60vw, 640px);
}

.hv-main,
.hv-side {
  position: absolute;
  overflow: hidden;
  background: var(--sand);
  box-shadow: var(--shadow-lg);
}

.hv-main img,
.hv-side img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.hv-main {
  right: 0;
  top: 0;
  width: 72%;
  height: 100%;
  border-radius: 200px 200px var(--radius-xl) var(--radius-xl);
}

.hv-side {
  left: 0;
  bottom: 8%;
  width: 40%;
  aspect-ratio: 3 / 4;
  border-radius: var(--radius-lg);
  border: 6px solid var(--ivory);
}

.glass {
  background: rgba(255, 255, 255, 0.78);
  backdrop-filter: blur(14px) saturate(160%);
  -webkit-backdrop-filter: blur(14px) saturate(160%);
  border: 1px solid rgba(255, 255, 255, 0.7);
}

.hv-card {
  position: absolute;
  left: 6%;
  top: 10%;
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 12px 18px 12px 12px;
  border-radius: 18px;
  box-shadow: var(--shadow);
  font-size: 14px;
  animation: float 6s ease-in-out infinite;
}

.hv-icon {
  width: 42px;
  height: 42px;
  border-radius: 12px;
  display: grid;
  place-items: center;
  background: var(--ink);
  color: #fff;
  font-size: 18px;
}

.hv-badge {
  position: absolute;
  right: -6px;
  bottom: 12%;
  width: 112px;
  height: 112px;
  border-radius: 50%;
  background: var(--accent);
  color: #fff;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  line-height: 1;
  box-shadow: 0 20px 40px -14px rgba(255, 90, 54, 0.7);
  animation: spinSlow 16s linear infinite;
}

.hv-badge span {
  font-size: 11px;
  font-weight: 700;
  letter-spacing: 0.12em;
  text-transform: uppercase;
}

.hv-badge strong {
  font-size: 34px;
  font-weight: 800;
  margin: 4px 0;
  letter-spacing: -0.03em;
}

@keyframes float {
  50% { transform: translateY(-10px); }
}

@keyframes spinSlow {
  0%, 100% { transform: rotate(-8deg); }
  50% { transform: rotate(8deg); }
}

@media (max-width: 575px) {
  .hv-card {
    left: 0;
    top: 4%;
    font-size: 13px;
  }

  .hv-badge {
    width: 88px;
    height: 88px;
    right: 0;
  }

  .hv-badge strong {
    font-size: 26px;
  }
}

/* Load-in */
.hero-in {
  animation: heroIn 1s var(--ease) both;
  animation-delay: var(--d, 0ms);
}

.hv-card.hero-in {
  animation: heroIn 1s var(--ease) both, float 6s ease-in-out 1.4s infinite;
  animation-delay: var(--d), 1.4s;
}

.hv-badge.hero-in {
  animation: heroIn 1s var(--ease) both, spinSlow 16s linear 1.5s infinite;
  animation-delay: var(--d), 1.5s;
}

@keyframes heroIn {
  from {
    opacity: 0;
    transform: translateY(24px);
  }
}

/* ---------- Marquee ---------- */
.marquee {
  overflow: hidden;
  background: var(--ink);
  color: #fff;
  padding: 22px 0;
  white-space: nowrap;
}

.marquee-track {
  display: inline-flex;
  animation: marquee 40s linear infinite;
}

.marquee-group {
  display: inline-flex;
  align-items: center;
  gap: 36px;
  padding-right: 36px;
  font-family: var(--font-display);
  font-size: clamp(28px, 4vw, 48px);
  line-height: 1;
}

.marquee-group span:nth-child(4n + 1) {
  font-style: italic;
}

.marquee-group i {
  font-style: normal;
  font-size: 0.5em;
  color: var(--accent);
}

@keyframes marquee {
  to { transform: translateX(-50%); }
}

/* ---------- Products ---------- */
.product-grid {
  display: grid;
  grid-template-columns: repeat(2, minmax(0, 1fr));
  gap: 32px 16px;
}

@media (min-width: 768px) {
  .product-grid {
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 40px 24px;
  }
}

@media (min-width: 1200px) {
  .product-grid {
    grid-template-columns: repeat(4, minmax(0, 1fr));
  }
}

.empty-note {
  text-align: center;
  padding: 48px 16px;
  border-radius: var(--radius-lg);
  background: var(--ivory);
  color: var(--ink-soft);
}

/* ---------- Perks ---------- */
.perks-strip {
  border-top: 1px solid var(--line);
  border-bottom: 1px solid var(--line);
}

.perks-grid {
  display: grid;
  grid-template-columns: repeat(2, minmax(0, 1fr));
}

@media (min-width: 992px) {
  .perks-grid {
    grid-template-columns: repeat(4, minmax(0, 1fr));
  }
}

.perk {
  display: flex;
  align-items: center;
  gap: 14px;
  padding: 28px 12px;
}

@media (min-width: 992px) {
  .perk {
    padding: 36px 24px;
  }

  .perk + .perk {
    border-left: 1px solid var(--line);
  }
}

.perk-icon {
  width: 48px;
  height: 48px;
  flex-shrink: 0;
  border-radius: 14px;
  background: var(--ivory);
  display: grid;
  place-items: center;
  font-size: 20px;
}

.perk-title {
  font-weight: 700;
  font-size: 15px;
}

.perk-text {
  color: var(--ink-soft);
  font-size: 13.5px;
}

/* ---------- Try-on ---------- */
.tryon-card {
  position: relative;
  overflow: hidden;
  display: grid;
  gap: 40px;
  padding: 40px 24px;
  border-radius: var(--radius-xl);
  background:
    radial-gradient(600px 400px at 85% 20%, rgba(255, 90, 54, 0.35), transparent 60%),
    radial-gradient(500px 400px at 0% 100%, rgba(120, 90, 255, 0.25), transparent 60%),
    var(--ink);
  color: rgba(255, 255, 255, 0.75);
}

@media (min-width: 992px) {
  .tryon-card {
    grid-template-columns: 1.1fr 1fr;
    align-items: center;
    padding: 72px;
    gap: 64px;
  }
}

.eyebrow-light {
  color: rgba(255, 255, 255, 0.6);
}

.tryon-sub {
  font-size: 17px;
  max-width: 460px;
  margin: 20px 0 28px;
}

.tryon-steps {
  list-style: none;
  padding: 0;
  margin: 0 0 32px;
  display: grid;
  gap: 12px;
}

.tryon-steps li {
  display: flex;
  align-items: center;
  gap: 14px;
  color: #fff;
  font-weight: 500;
}

.tryon-steps span {
  width: 40px;
  height: 40px;
  flex-shrink: 0;
  border-radius: 50%;
  border: 1px solid rgba(255, 255, 255, 0.2);
  display: grid;
  place-items: center;
  font-size: 12px;
  font-weight: 700;
  color: var(--accent);
}

.tv-frame {
  position: relative;
  aspect-ratio: 4 / 5;
  max-width: 440px;
  margin: 0 auto;
  border-radius: var(--radius-lg);
  overflow: hidden;
  background: #222;
  border: 1px solid rgba(255, 255, 255, 0.15);
  box-shadow: 0 40px 80px -30px rgba(0, 0, 0, 0.8);
}

.tv-frame img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.tv-scan {
  position: absolute;
  left: 0;
  right: 0;
  height: 30%;
  background: linear-gradient(to bottom, transparent, rgba(255, 90, 54, 0.35), transparent);
  border-bottom: 2px solid rgba(255, 140, 110, 0.9);
  animation: scan 3.2s ease-in-out infinite;
}

@keyframes scan {
  0% { top: -30%; }
  100% { top: 100%; }
}

.tv-chip {
  position: absolute;
  left: 50%;
  bottom: 18px;
  transform: translateX(-50%);
  white-space: nowrap;
  padding: 10px 16px;
  border-radius: 999px;
  background: rgba(14, 14, 16, 0.7);
  backdrop-filter: blur(10px);
  color: #fff;
  font-size: 13px;
  font-weight: 600;
}

.tv-chip i {
  color: var(--accent);
  margin-right: 6px;
}
</style>
