<template>
  <div class="container-xxl px-3 px-lg-5 py-4 py-lg-5 cart-page">
    <div class="cart-head">
      <h1 class="cart-title">Your bag</h1>
      <span v-if="isAuthenticated" class="cart-count">{{ count }} {{ count === 1 ? "item" : "items" }}</span>
    </div>

    <!-- Logged out -->
    <div v-if="!isAuthenticated" class="cart-empty">
      <span class="empty-icon"><i class="bi bi-person-lock"></i></span>
      <h2>Login to see your bag</h2>
      <p>Your cart is saved to your account so it follows you on every device.</p>
      <div class="d-flex flex-wrap gap-2 justify-content-center">
        <router-link :to="{ name: 'Login', query: { redirect: '/cart' } }" class="btn btn-dark btn-lg">Login</router-link>
        <router-link to="/register" class="btn btn-outline-dark btn-lg">Create account</router-link>
      </div>
    </div>

    <!-- Empty -->
    <div v-else-if="items.length === 0" class="cart-empty">
      <span class="empty-icon"><i class="bi bi-bag"></i></span>
      <h2>Your bag is empty</h2>
      <p>Looks like you haven't added anything yet. Let's fix that.</p>
      <router-link to="/shop" class="btn btn-dark btn-lg">
        Start shopping <i class="bi bi-arrow-right ms-1"></i>
      </router-link>
    </div>

    <div v-else class="cart-grid">
      <div>
        <div class="ship-progress">
          <template v-if="remainingForFreeShip > 0">
            <span>Add <strong>${{ remainingForFreeShip.toFixed(2) }}</strong> more for free shipping</span>
          </template>
          <template v-else>
            <span><i class="bi bi-check-circle-fill"></i> You've unlocked <strong>free shipping</strong></span>
          </template>
          <div class="bar"><span :style="{ width: `${shipProgress}%` }"></span></div>
        </div>

        <TransitionGroup name="list" tag="div" class="d-flex flex-column gap-3">
          <CartItem
            v-for="item in items"
            :key="item.id"
            :item="item"
            @increase="increaseQty"
            @decrease="decreaseQty"
            @remove="removeCartItem"
          />
        </TransitionGroup>

        <div class="cart-actions">
          <router-link to="/shop" class="link-arrow">
            <i class="bi bi-arrow-left"></i> Continue shopping
          </router-link>
          <button class="clear-btn" @click="handleClearCart">
            <i class="bi bi-trash3 me-1"></i> Clear cart
          </button>
        </div>
      </div>

      <div class="summary-col">
        <OrderSummary :items="items" :show-items="false">
          <button
            class="btn btn-dark btn-lg w-100 mt-4"
            :disabled="items.length === 0"
            @click="handleCheckout"
          >
            Checkout <i class="bi bi-arrow-right ms-1"></i>
          </button>
          <div class="summary-benefits">
            <div><i class="bi bi-shield-lock"></i> Secure checkout</div>
            <div><i class="bi bi-arrow-repeat"></i> 30-day returns</div>
            <div><i class="bi bi-truck"></i> Fast delivery</div>
          </div>
        </OrderSummary>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, onMounted } from "vue"
import { useRouter } from "vue-router"
import CartItem from "@/components/cart/CartItem.vue"
import OrderSummary from "@/components/common/OrderSummary.vue"
import { useCart } from "@/composables/useCart.js"
import { useAuth } from "@/composables/useAuth.js"

const FREE_SHIPPING_THRESHOLD = 100

const router = useRouter()
const { isAuthenticated } = useAuth()
const { items, count, fetchCart, updateQuantity, removeItem, clearCart } = useCart()

const subtotal = computed(() =>
  items.value.reduce((sum, item) => sum + (item.price || 0) * item.quantity, 0)
)
const remainingForFreeShip = computed(() => Math.max(0, FREE_SHIPPING_THRESHOLD - subtotal.value))
const shipProgress = computed(() => Math.min(100, (subtotal.value / FREE_SHIPPING_THRESHOLD) * 100))

const handleCheckout = () => {
  router.push("/checkout")
}

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

<style scoped>
.cart-head {
  display: flex;
  align-items: baseline;
  gap: 14px;
  margin-bottom: 32px;
}

.cart-title {
  font-size: clamp(34px, 4.6vw, 56px);
  letter-spacing: -0.04em;
  font-weight: 800;
  margin: 0;
}

.cart-count {
  color: var(--ink-soft);
  font-weight: 600;
}

.cart-grid {
  display: grid;
  gap: 32px;
}

@media (min-width: 992px) {
  .cart-grid {
    grid-template-columns: 1fr 400px;
    gap: 48px;
    align-items: start;
  }

  .summary-col {
    position: sticky;
    top: calc(var(--header-h) + 24px);
  }
}

.ship-progress {
  padding: 16px 20px;
  margin-bottom: 20px;
  border-radius: var(--radius);
  background: var(--ivory);
  font-size: 14px;
}

.ship-progress i {
  color: var(--success);
}

.ship-progress .bar {
  height: 6px;
  margin-top: 10px;
  border-radius: 6px;
  background: var(--sand);
  overflow: hidden;
}

.ship-progress .bar span {
  display: block;
  height: 100%;
  border-radius: inherit;
  background: linear-gradient(90deg, var(--ink), var(--accent));
  transition: width 0.6s var(--ease);
}

.cart-actions {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 16px;
  margin-top: 24px;
}

.clear-btn {
  border: 0;
  background: transparent;
  color: var(--ink-soft);
  font-weight: 600;
  font-size: 14px;
}

.clear-btn:hover {
  color: var(--danger);
}

.summary-benefits {
  display: grid;
  gap: 10px;
  margin-top: 24px;
  padding-top: 20px;
  border-top: 1px solid var(--line);
  font-size: 13.5px;
  color: var(--ink-soft);
}

.summary-benefits i {
  color: var(--ink);
  margin-right: 8px;
}

.cart-empty {
  text-align: center;
  padding: 80px 24px;
  border-radius: var(--radius-xl);
  background: var(--ivory);
}

.cart-empty h2 {
  font-size: 26px;
  margin-bottom: 8px;
}

.cart-empty p {
  color: var(--ink-soft);
  max-width: 380px;
  margin: 0 auto 28px;
}

.empty-icon {
  width: 72px;
  height: 72px;
  margin: 0 auto 20px;
  border-radius: 50%;
  background: #fff;
  display: grid;
  place-items: center;
  font-size: 28px;
  box-shadow: var(--shadow-sm);
}

.list-enter-active,
.list-leave-active {
  transition: all 0.4s var(--ease);
}

.list-enter-from,
.list-leave-to {
  opacity: 0;
  transform: translateX(-20px);
}
</style>
