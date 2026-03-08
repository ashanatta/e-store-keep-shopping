<template>
  <div class="container py-5">
    <div class="d-flex align-items-center justify-content-between mb-4">
      <h3 class="fw-semibold mb-0">Checkout</h3>
    </div>
    
    <div class="stepper-container mb-5">
      <CheckoutStepper :step="currentStep" />
    </div>

    <div class="row g-4">
      <div class="col-lg-8">
        <!-- Step 1: Shipping -->
        <CheckoutShippingForm 
          v-if="currentStep === 'shipping'" 
          v-model="shippingDetails"
          @continue="currentStep = 'payment'"
        />

        <!-- Step 2: Payment -->
        <CheckoutPaymentForm 
          v-else-if="currentStep === 'payment'" 
          v-model="paymentDetails"
          @back="currentStep = 'shipping'"
          @continue="currentStep = 'review'"
        />

        <!-- Step 3: Review -->
        <div v-else-if="currentStep === 'review'" class="review-container">
          <div class="summary-card p-4 mb-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
              <h5 class="fw-semibold mb-0">Shipping Address</h5>
              <button class="btn btn-sm btn-outline-dark px-3 rounded-pill" @click="currentStep = 'shipping'">Edit</button>
            </div>
            <div class="text-muted">
              <p class="mb-1">{{ shippingDetails.fullName }}</p>
              <p class="mb-1">{{ shippingDetails.streetAddress }}</p>
              <p class="mb-0">{{ shippingDetails.city }}, {{ shippingDetails.state }} {{ shippingDetails.zipCode }}, {{ shippingDetails.country }}</p>
            </div>
          </div>

          <div class="summary-card p-4 mb-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
              <h5 class="fw-semibold mb-0">Payment Method</h5>
              <button class="btn btn-sm btn-outline-dark px-3 rounded-pill" @click="currentStep = 'payment'">Edit</button>
            </div>
            <div class="text-muted">
              <p class="mb-0" v-if="paymentDetails.method === 'card'">Card ending in {{ paymentDetails.cardNumber.slice(-4) }}</p>
              <p class="mb-0" v-else-if="paymentDetails.method === 'stripe'">Stripe</p>
              <p class="mb-0" v-else-if="paymentDetails.method === 'paypal'">PayPal</p>
              <p class="mb-0" v-else>Cash on Delivery</p>
            </div>
          </div>

          <div class="d-flex gap-3 mt-4">
            <button class="btn btn-outline-dark flex-grow-1 py-3 rounded-pill fw-semibold" @click="currentStep = 'payment'">Back</button>
            <button class="btn btn-dark flex-grow-1 py-3 rounded-pill fw-semibold" @click="handlePlaceOrder">Place Order</button>
          </div>
        </div>
      </div>

      <div class="col-lg-4">
        <OrderSummary :items="items" :show-items="true" />
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from "vue"
import CheckoutStepper from "@/components/checkout/CheckoutStepper.vue"
import CheckoutShippingForm from "@/components/checkout/CheckoutShippingForm.vue"
import CheckoutPaymentForm from "@/components/checkout/CheckoutPaymentForm.vue"
import OrderSummary from "@/components/common/OrderSummary.vue"
import { useCart } from "@/composables/useCart.js"
import { useRouter } from "vue-router"
import { useToast } from "@/composables/useToast.js"
import axios from "axios"

const { items, fetchCart, clearCart } = useCart()
const router = useRouter()
const { success, error: toastError } = useToast()

const currentStep = ref("shipping")

const shippingDetails = ref({
  fullName: "",
  email: "",
  phoneNumber: "",
  streetAddress: "",
  city: "",
  state: "",
  zipCode: "",
  country: "USA"
})

const paymentDetails = ref({
  method: "card",
  cardNumber: "",
  cardName: "",
  expiry: "",
  cvv: ""
})

const subtotal = computed(() =>
  items.value.reduce((sum, item) => sum + (item.price || 0) * item.quantity, 0)
)
const tax = computed(() => subtotal.value * 0.08)
const total = computed(() => subtotal.value + tax.value)

const handlePlaceOrder = async () => {
  try {
    const orderData = {
      shipping: shippingDetails.value,
      payment: paymentDetails.value,
      items: items.value,
      totals: {
        subtotal: subtotal.value,
        shipping: 0, // FREE shipping
        tax: tax.value,
        total: total.value
      }
    }

    const response = await axios.post('/orders', orderData)
    success("Order placed successfully! Thank you for your purchase.")
    await clearCart()
    router.push(`/orders/${response.data.order.id}`)
  } catch (err) {
    console.error('Error placing order:', err)
    toastError(err.response?.data?.message || "Failed to place order. Please try again.")
  }
}

onMounted(async () => {
  await fetchCart()
})
</script>

<style scoped>
.stepper-container {
  max-width: 600px;
  margin: 0 auto;
}

.summary-card {
  border: 1px solid #e9ecef;
  border-radius: 16px;
  background: #fff;
}
</style>
