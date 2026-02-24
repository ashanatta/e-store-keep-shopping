<template>
  <div class="summary-card p-4">
    <h5 class="fw-semibold mb-4">Order Summary</h5>
    <div v-if="showItems" class="summary-items mb-3">
      <div
        v-for="item in items"
        :key="item.id"
        class="d-flex gap-3 align-items-center mb-3"
      >
        <img :src="item.image" :alt="item.name" class="summary-thumb" />
        <div class="flex-grow-1">
          <div class="fw-semibold">{{ item.name }}</div>
          <div class="text-muted small">{{ item.size }} • {{ item.color }}</div>
          <div class="text-muted small">Qty: {{ item.quantity }}</div>
        </div>
        <div class="fw-semibold">${{ item.price.toFixed(2) }}</div>
      </div>
    </div>

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
    <div class="summary-row total-row">
      <span class="fw-semibold">Total</span>
      <span class="fw-semibold">${{ total.toFixed(2) }}</span>
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
  props.items.reduce((sum, item) => sum + item.price * item.quantity, 0)
)
const tax = computed(() => subtotal.value * 0.08)
const total = computed(() => subtotal.value + tax.value)
</script>

<style scoped>
.summary-card {
  border: 1px solid #e9ecef;
  border-radius: 16px;
  background: #fff;
}

.summary-row {
  display: flex;
  justify-content: space-between;
  padding: 8px 0;
  border-bottom: 1px solid #f1f1f1;
}

.total-row {
  border-bottom: none;
  padding-top: 16px;
  font-size: 18px;
}

.summary-thumb {
  width: 56px;
  height: 72px;
  object-fit: cover;
  border-radius: 12px;
}
</style>
