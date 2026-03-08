<template>
  <div class="summary-card p-4">
    <h5 class="fw-semibold mb-4">Order Summary</h5>
    <div v-if="showItems && items.length > 0" class="summary-items mb-4">
      <div
        v-for="item in items"
        :key="item.id"
        class="d-flex gap-3 align-items-center mb-3 pb-3 border-bottom-light"
      >
        <div class="summary-thumb-container">
          <img :src="item.image" :alt="item.name" class="summary-thumb" />
          <span class="qty-badge">{{ item.quantity }}</span>
        </div>
        <div class="flex-grow-1 min-w-0">
          <div class="fw-semibold text-truncate">{{ item.name }}</div>
          <div class="text-muted small">{{ item.size }} • {{ item.color }}</div>
          <div class="text-muted small">Qty: {{ item.quantity }}</div>
        </div>
        <div class="fw-semibold">${{ item.price ? parseFloat(item.price).toFixed(2) : '0.00' }}</div>
      </div>
    </div>
    <div v-else-if="showItems" class="text-muted small mb-4">No items in cart.</div>

    <div class="summary-details">
      <div class="summary-row">
        <span class="text-muted">Subtotal</span>
        <span class="fw-semibold">${{ subtotal.toFixed(2) }}</span>
      </div>
      <div class="summary-row">
        <span class="text-muted">Shipping</span>
        <span class="text-success fw-semibold">FREE</span>
      </div>
      <div class="summary-row">
        <span class="text-muted">Tax (8%)</span>
        <span class="fw-semibold">${{ tax.toFixed(2) }}</span>
      </div>
      <div class="summary-row total-row mt-3">
        <span class="fw-bold fs-5">Total</span>
        <span class="fw-bold fs-5">${{ total.toFixed(2) }}</span>
      </div>
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
  border: 1px solid #e9ecef;
  border-radius: 20px;
  background: #fff;
  position: sticky;
  top: 2rem;
}

.summary-items {
  max-height: 400px;
  overflow-y: auto;
}

.border-bottom-light {
  border-bottom: 1px solid #f8f9fa;
}

.summary-thumb-container {
  position: relative;
}

.summary-thumb {
  width: 64px;
  height: 80px;
  object-fit: cover;
  border-radius: 12px;
  background: #f8f9fa;
}

.qty-badge {
  position: absolute;
  top: -8px;
  right: -8px;
  background: #111827;
  color: white;
  width: 20px;
  height: 20px;
  border-radius: 50%;
  display: grid;
  place-items: center;
  font-size: 11px;
  font-weight: 600;
}

.summary-row {
  display: flex;
  justify-content: space-between;
  padding: 10px 0;
}

.total-row {
  border-top: 1px solid #e9ecef;
  padding-top: 20px;
  color: #111827;
}

.min-w-0 {
  min-width: 0;
}
</style>
