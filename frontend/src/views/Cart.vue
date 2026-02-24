<template>
  <div class="container py-5">
    <h3 class="fw-semibold mb-4">Shopping Cart ({{ items.length }} items)</h3>
    <div class="row g-4">
      <div class="col-lg-8">
        <div class="d-flex flex-column gap-3">
          <CartItem
            v-for="item in items"
            :key="item.id"
            :item="item"
            @increase="increaseQty"
            @decrease="decreaseQty"
            @remove="removeItem"
          />
        </div>
        <button class="btn btn-outline-dark w-100 mt-4" @click="clearCart">
          Clear Cart
        </button>
      </div>
      <div class="col-lg-4">
        <OrderSummary :items="items">
          <button class="btn btn-dark w-100 mt-3">Proceed to Checkout</button>
          <button class="btn btn-outline-dark w-100 mt-2">Continue Shopping</button>
          <div class="summary-benefits mt-4">
            <div class="d-flex align-items-center gap-2 mb-2">
              <i class="bi bi-shield-check text-success"></i>
              <span class="small">Secure checkout</span>
            </div>
            <div class="d-flex align-items-center gap-2 mb-2">
              <i class="bi bi-arrow-repeat text-success"></i>
              <span class="small">30-day returns</span>
            </div>
            <div class="d-flex align-items-center gap-2">
              <i class="bi bi-truck text-success"></i>
              <span class="small">Fast delivery</span>
            </div>
          </div>
        </OrderSummary>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from "vue"
import CartItem from "@/components/cart/CartItem.vue"
import OrderSummary from "@/components/common/OrderSummary.vue"

const items = ref([
  {
    id: 1,
    name: "Elegant Blazer",
    category: "Blazers",
    size: "S",
    color: "Black",
    quantity: 1,
    price: 149.99,
    image: "https://images.unsplash.com/photo-1524504388940-b1c1722653e1"
  }
])

const increaseQty = (id) => {
  const item = items.value.find((cartItem) => cartItem.id === id)
  if (item) {
    item.quantity += 1
  }
}

const decreaseQty = (id) => {
  const item = items.value.find((cartItem) => cartItem.id === id)
  if (item && item.quantity > 1) {
    item.quantity -= 1
  }
}

const removeItem = (id) => {
  items.value = items.value.filter((cartItem) => cartItem.id !== id)
}

const clearCart = () => {
  items.value = []
}
</script>
