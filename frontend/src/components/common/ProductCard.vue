<template>
  <article class="p-card">
    <router-link :to="`/product/${product.id}`" class="p-media" :aria-label="product.name">
      <img :src="product.image" :alt="product.name" loading="lazy" />

      <div class="p-badges">
        <span v-if="isOnSale" class="p-badge sale">
          -{{ product.discount_percentage }}%
        </span>
        <span v-else-if="isNew" class="p-badge new">New</span>
      </div>

      <span class="p-cta">
        View product <i class="bi bi-arrow-right"></i>
      </span>
    </router-link>

    <button
      type="button"
      class="p-wish"
      :class="{ active: wished }"
      :aria-pressed="wished"
      :aria-label="wished ? 'Remove from wishlist' : 'Add to wishlist'"
      @click="onWish"
    >
      <i :class="wished ? 'bi bi-heart-fill' : 'bi bi-heart'"></i>
    </button>

    <router-link :to="`/product/${product.id}`" class="p-body">
      <div class="p-meta">
        <span class="p-cat">{{ categoryLabel }}</span>
        <span v-if="product.reviews > 0" class="p-rating">
          <i class="bi bi-star-fill"></i>
          {{ product.rating.toFixed(1) }}
          <span class="text-muted">({{ product.reviews }})</span>
        </span>
      </div>
      <h3 class="p-name">{{ product.name }}</h3>
      <div class="p-price">
        <template v-if="product.displayPrice !== null">
          <span class="now" :class="{ sale: hasStrike }">${{ product.displayPrice.toFixed(2) }}</span>
          <span v-if="hasStrike" class="was">${{ product.originalPrice.toFixed(2) }}</span>
        </template>
        <span v-else class="now">Select options</span>
      </div>
    </router-link>
  </article>
</template>

<script setup>
import { computed, ref } from "vue"
import { useWishlist } from "@/composables/useWishlist.js"
import { useAuth } from "@/composables/useAuth.js"
import { useToast } from "@/composables/useToast.js"

const props = defineProps({
  product: { type: Object, required: true },
})

const { toggle, isInWishlist } = useWishlist()
const { isAuthenticated } = useAuth()
const { warning } = useToast()

const busy = ref(false)
const wished = computed(() => isInWishlist(props.product.id).value)

const hasStrike = computed(
  () =>
    props.product.originalPrice &&
    props.product.displayPrice !== null &&
    props.product.displayPrice !== props.product.originalPrice
)

const isOnSale = computed(() => props.product.discount_percentage > 0 && props.product.originalPrice)

const isNew = computed(() => {
  if (!props.product.created_at) return false
  const days = (Date.now() - new Date(props.product.created_at).getTime()) / 86400000
  return days >= 0 && days < 21
})

const categoryLabel = computed(() => props.product.category?.name || props.product.type || "Collection")

const onWish = async () => {
  if (!isAuthenticated.value) {
    warning("Please login to use wishlist.")
    return
  }
  if (busy.value) return
  busy.value = true
  try {
    await toggle(props.product.id)
  } finally {
    busy.value = false
  }
}
</script>

<style scoped>
.p-card {
  position: relative;
  height: 100%;
  display: flex;
  flex-direction: column;
}

.p-media {
  position: relative;
  display: block;
  aspect-ratio: 3 / 4;
  border-radius: var(--radius);
  overflow: hidden;
  background: var(--ivory);
  isolation: isolate;
}

.p-media img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 1s var(--ease);
}

.p-card:hover .p-media img {
  transform: scale(1.06);
}

.p-media::after {
  content: "";
  position: absolute;
  inset: 0;
  background: linear-gradient(to top, rgba(14, 14, 16, 0.35), transparent 45%);
  opacity: 0;
  transition: opacity 0.4s var(--ease);
}

.p-card:hover .p-media::after {
  opacity: 1;
}

.p-badges {
  position: absolute;
  top: 12px;
  left: 12px;
  z-index: 1;
  display: flex;
  gap: 6px;
}

.p-badge {
  font-size: 11.5px;
  font-weight: 700;
  letter-spacing: 0.02em;
  padding: 5px 10px;
  border-radius: 999px;
}

.p-badge.sale {
  background: var(--accent);
  color: #fff;
}

.p-badge.new {
  background: #fff;
  color: var(--ink);
}

.p-cta {
  position: absolute;
  left: 12px;
  right: 12px;
  bottom: 12px;
  z-index: 1;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  height: 44px;
  border-radius: 999px;
  background: rgba(255, 255, 255, 0.94);
  backdrop-filter: blur(8px);
  color: var(--ink);
  font-weight: 600;
  font-size: 14px;
  opacity: 0;
  transform: translateY(12px);
  transition: all 0.45s var(--ease);
}

.p-card:hover .p-cta {
  opacity: 1;
  transform: none;
}

@media (hover: none) {
  .p-cta {
    display: none;
  }
}

.p-wish {
  position: absolute;
  top: 10px;
  right: 10px;
  z-index: 2;
  width: 38px;
  height: 38px;
  border-radius: 50%;
  border: 0;
  background: rgba(255, 255, 255, 0.92);
  backdrop-filter: blur(8px);
  color: var(--ink);
  display: grid;
  place-items: center;
  font-size: 16px;
  box-shadow: var(--shadow-sm);
  transition: transform 0.25s var(--ease), color 0.2s;
}

.p-wish:hover {
  transform: scale(1.1);
}

.p-wish.active {
  color: var(--accent);
  animation: pop 0.4s var(--ease);
}

@keyframes pop {
  50% { transform: scale(1.25); }
}

.p-body {
  display: block;
  padding: 14px 2px 4px;
  color: var(--ink);
  text-decoration: none;
}

.p-meta {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 8px;
  margin-bottom: 4px;
  font-size: 12.5px;
}

.p-cat {
  color: var(--muted);
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.08em;
  font-size: 11px;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.p-rating {
  display: inline-flex;
  align-items: center;
  gap: 4px;
  font-weight: 600;
  white-space: nowrap;
}

.p-rating i {
  color: var(--star);
  font-size: 11px;
}

.p-name {
  font-size: 15px;
  font-weight: 600;
  letter-spacing: -0.01em;
  line-height: 1.35;
  margin: 0 0 6px;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.p-price {
  display: flex;
  align-items: baseline;
  gap: 8px;
}

.p-price .now {
  font-weight: 700;
  font-size: 15.5px;
}

.p-price .now.sale {
  color: var(--accent-ink);
}

.p-price .was {
  color: var(--muted);
  text-decoration: line-through;
  font-size: 13.5px;
}
</style>
