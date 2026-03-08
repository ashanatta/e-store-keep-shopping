<template>
  <div class="container py-5">
    <h3 class="fw-semibold mb-4">Shopping Cart ({{ count }} items)</h3>
    <div v-if="!isAuthenticated" class="alert alert-warning">
      Please login to view your cart.
    </div>
    <div class="row g-4">
      <div class="col-lg-8">
        <div v-if="isAuthenticated && items.length > 0" class="d-flex flex-column gap-3">
          <CartItem
            v-for="item in items"
            :key="item.id"
            :item="item"
            @increase="increaseQty"
            @decrease="decreaseQty"
            @remove="removeCartItem"
          />
        </div>
        <div v-else-if="isAuthenticated" class="alert alert-info">
          Your cart is empty.
        </div>
        <button v-if="isAuthenticated && items.length > 0" class="btn btn-outline-dark w-100 mt-4" @click="handleClearCart">
          Clear Cart
        </button>
      </div>
      <div class="col-lg-4">
        <OrderSummary :items="items">
          <router-link 
            to="/checkout" 
            class="btn btn-dark w-100 mt-3 py-3 rounded-pill fw-semibold" 
            :class="{ disabled: !isAuthenticated || items.length === 0 }"
          >
            Proceed to Checkout
          </router-link>
          <router-link to="/shop" class="btn btn-outline-dark w-100 mt-2 py-3 rounded-pill fw-semibold">Continue Shopping</router-link>
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
import { onMounted } from "vue"
import CartItem from "@/components/cart/CartItem.vue"
import OrderSummary from "@/components/common/OrderSummary.vue"
import { useCart } from "@/composables/useCart.js"
import { useAuth } from "@/composables/useAuth.js"

const { isAuthenticated } = useAuth()
const { items, count, fetchCart, updateQuantity, removeItem, clearCart } = useCart()

const increaseQty = async (id) => {
  const item = items.value.find((cartItem) => cartItem.id === id)
  if (!item) return
  await updateQuantity(id, item.quantity + 1)
}

const decreaseQty = async (id) => {
  const item = items.value.find((cartItem) => cartItem.id === id)
  if (!item || item.quantity <= 1) {
    return
  }
  await updateQuantity(id, item.quantity - 1)
}

const removeCartItem = async (id) => {
  await removeItem(id)
}

const handleClearCart = async () => {
  await clearCart()
}

onMounted(async () => {
  if (isAuthenticated.value) {
    await fetchCart()
  }
})
</script>
