<template>
  <div class="summary-card">
    <h5 class="summary-title">Order summary</h5>
    <div v-if="showItems" class="summary-items">
      <div
        v-for="item in items"
        :key="item.id"
        class="summary-item"
      >
        <div class="thumb-wrap">
          <img :src="item.image" :alt="item.name" class="summary-thumb" />
          <span class="qty-badge">{{ item.quantity }}</span>
        </div>
        <div class="flex-grow-1 min-w-0">
          <div class="fw-semibold text-truncate">{{ item.name }}</div>
          <div class="text-muted small">{{ item.size }} · {{ item.color }}</div>
        </div>
        <div class="fw-semibold">${{ item.price ? parseFloat(item.price).toFixed(2) : '0.00' }}</div>
      </div>
    </div>

    <div class="summary-row">
      <span class="text-muted">Subtotal</span>
      <span class="fw-semibold">${{ subtotal.toFixed(2) }}</span>
    </div>
    <div class="summary-row">
      <span class="text-muted">Shipping</span>
      <span class="free">FREE</span>
    </div>
    <div class="summary-row">
      <span class="text-muted">Tax (8%)</span>
      <span class="fw-semibold">${{ tax.toFixed(2) }}</span>
    </div>
    <div class="summary-row total-row">
      <span>Total</span>
      <span>${{ total.toFixed(2) }}</span>
    </div>

    <slot />
  </div>
</template>

<script setup>
import { computed } from "vue"

const props = defineProps({
  items: {
    type: Array,
    default: () => []
  },
  showItems: {
    type: Boolean,
    default: true
  }
})

const subtotal = computed(() =>
  props.items.reduce((sum, item) => sum + (item.price || 0) * item.quantity, 0)
)
const tax = computed(() => subtotal.value * 0.08)
const total = computed(() => subtotal.value + tax.value)
</script>

<style scoped>
.summary-card {
  padding: 28px;
  border: 1px solid var(--line);
  border-radius: var(--radius-lg);
  background: #fff;
}

.summary-title {
  font-size: 20px;
  font-weight: 800;
  letter-spacing: -0.02em;
  margin-bottom: 20px;
}

.summary-items {
  display: grid;
  gap: 16px;
  padding-bottom: 20px;
  margin-bottom: 8px;
  border-bottom: 1px solid var(--line);
}

.summary-item {
  display: flex;
  align-items: center;
  gap: 14px;
  font-size: 14px;
}

.min-w-0 {
  min-width: 0;
}

.thumb-wrap {
  position: relative;
  flex-shrink: 0;
}

.summary-thumb {
  width: 56px;
  height: 70px;
  object-fit: cover;
  border-radius: 12px;
  background: var(--ivory);
}

.qty-badge {
  position: absolute;
  top: -6px;
  right: -6px;
  min-width: 20px;
  height: 20px;
  padding: 0 6px;
  border-radius: 999px;
  background: var(--ink);
  color: #fff;
  font-size: 11px;
  font-weight: 700;
  display: grid;
  place-items: center;
}

.summary-row {
  display: flex;
  justify-content: space-between;
  padding: 10px 0;
  font-size: 14.5px;
}

.free {
  font-weight: 700;
  color: var(--success);
  font-size: 13px;
  letter-spacing: 0.06em;
}

.total-row {
  margin-top: 8px;
  padding-top: 18px;
  border-top: 1px dashed var(--line-strong);
  font-size: 20px;
  font-weight: 800;
  letter-spacing: -0.02em;
}
</style>
