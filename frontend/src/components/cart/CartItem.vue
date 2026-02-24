<template>
  <div class="cart-item d-flex flex-column flex-md-row align-items-md-center gap-3">
    <img :src="item.image" :alt="item.name" class="cart-image" />
    <div class="flex-grow-1">
      <div class="fw-semibold">{{ item.name }}</div>
      <div class="text-muted small">{{ item.category }}</div>
      <div class="text-muted small">
        Size: {{ item.size }} • Color: {{ item.color }}
      </div>
      <div class="quantity-control mt-2">
        <button type="button" class="qty-btn" @click="$emit('decrease', item.id)">-</button>
        <span class="qty-value">{{ item.quantity }}</span>
        <button type="button" class="qty-btn" @click="$emit('increase', item.id)">+</button>
      </div>
    </div>
    <div class="d-flex align-items-center gap-3">
      <div class="fw-semibold">${{ item.price.toFixed(2) }}</div>
      <button type="button" class="remove-btn" @click="$emit('remove', item.id)">
        <i class="bi bi-trash3"></i>
      </button>
    </div>
  </div>
</template>

<script setup>
defineProps({
  item: {
    type: Object,
    required: true
  }
})
</script>

<style scoped>
.cart-item {
  padding: 18px;
  border: 1px solid #e9ecef;
  border-radius: 16px;
  background: #fff;
}

.cart-image {
  width: 90px;
  height: 110px;
  object-fit: cover;
  border-radius: 12px;
}

.quantity-control {
  display: inline-flex;
  align-items: center;
  gap: 10px;
  border: 1px solid #e5e7eb;
  border-radius: 12px;
  padding: 4px 10px;
}

.qty-btn {
  border: none;
  background: transparent;
  font-weight: 600;
}

.qty-value {
  min-width: 18px;
  text-align: center;
}

.remove-btn {
  border: none;
  background: transparent;
  color: #dc3545;
}
</style>
