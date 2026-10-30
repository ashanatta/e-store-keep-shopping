<template>
  <div class="cart-item">
    <img :src="item.image" :alt="item.name" class="cart-image" />
    <div class="ci-body">
      <div class="ci-top">
        <div class="min-w-0">
          <div class="ci-cat">{{ item.category }}</div>
          <div class="ci-name">{{ item.name }}</div>
          <div class="ci-variant">
            <span>Size <strong>{{ item.size }}</strong></span>
            <span>Color <strong>{{ item.color }}</strong></span>
          </div>
        </div>
        <button type="button" class="remove-btn" aria-label="Remove item" @click="$emit('remove', item.id)">
          <i class="bi bi-x-lg"></i>
        </button>
      </div>
      <div class="ci-bottom">
        <div class="quantity-control">
          <button type="button" class="qty-btn" aria-label="Decrease quantity" :disabled="item.quantity <= 1" @click="$emit('decrease', item.id)">
            <i class="bi bi-dash"></i>
          </button>
          <span class="qty-value">{{ item.quantity }}</span>
          <button type="button" class="qty-btn" aria-label="Increase quantity" @click="$emit('increase', item.id)">
            <i class="bi bi-plus"></i>
          </button>
        </div>
        <div class="ci-price">${{ item.price ? parseFloat(item.price).toFixed(2) : '0.00' }}</div>
      </div>
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
  display: flex;
  gap: 16px;
  padding: 14px;
  border: 1px solid var(--line);
  border-radius: var(--radius-lg);
  background: #fff;
  transition: box-shadow 0.3s var(--ease);
}

.cart-item:hover {
  box-shadow: var(--shadow);
}

@media (min-width: 768px) {
  .cart-item {
    gap: 20px;
    padding: 16px;
  }
}

.cart-image {
  width: 96px;
  height: 120px;
  flex-shrink: 0;
  object-fit: cover;
  border-radius: 14px;
  background: var(--ivory);
}

@media (min-width: 768px) {
  .cart-image {
    width: 120px;
    height: 150px;
  }
}

.ci-body {
  flex: 1;
  min-width: 0;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
  gap: 12px;
}

.ci-top {
  display: flex;
  justify-content: space-between;
  gap: 12px;
}

.min-w-0 {
  min-width: 0;
}

.ci-cat {
  font-size: 11px;
  font-weight: 700;
  letter-spacing: 0.12em;
  text-transform: uppercase;
  color: var(--muted);
}

.ci-name {
  font-weight: 700;
  font-size: 16px;
  letter-spacing: -0.01em;
  margin: 2px 0 6px;
}

.ci-variant {
  display: flex;
  flex-wrap: wrap;
  gap: 4px 14px;
  font-size: 13px;
  color: var(--ink-soft);
}

.ci-variant strong {
  color: var(--ink);
  font-weight: 600;
}

.ci-bottom {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 12px;
}

.ci-price {
  font-weight: 800;
  font-size: 17px;
}

.quantity-control {
  display: inline-flex;
  align-items: center;
  border: 1.5px solid var(--line);
  border-radius: 999px;
  padding: 2px;
}

.qty-btn {
  width: 34px;
  height: 34px;
  border: none;
  border-radius: 50%;
  background: transparent;
  display: grid;
  place-items: center;
  font-size: 16px;
}

.qty-btn:not(:disabled):hover {
  background: var(--ivory);
}

.qty-btn:disabled {
  opacity: 0.35;
}

.qty-value {
  min-width: 26px;
  text-align: center;
  font-weight: 700;
  font-size: 14px;
}

.remove-btn {
  width: 34px;
  height: 34px;
  flex-shrink: 0;
  border: none;
  border-radius: 50%;
  background: transparent;
  color: var(--muted);
  display: grid;
  place-items: center;
  transition: all 0.2s;
}

.remove-btn:hover {
  background: #fdecea;
  color: var(--danger);
}
</style>
