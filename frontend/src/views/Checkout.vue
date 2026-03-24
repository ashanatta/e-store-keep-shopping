<template>
  <div class="container py-3 py-md-5 checkout-page px-3 px-md-0">
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
              <div v-else-if="paymentDetails.method === 'stripe'">
                <p class="mb-2">Stripe Payment</p>
                <div class="stripe-review-container p-3 rounded-3 border bg-light mb-3">
                  <label class="form-label small mb-3 d-block">Stripe Card Details</label>
                  <div id="stripe-card-element" class="bg-white p-3 rounded-3 border shadow-sm">
                    <!-- Stripe Card Element will be mounted here -->
                  </div>
                  <div id="card-errors" class="text-danger small mt-2" role="alert"></div>
                </div>
              </div>
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
import { ref, onMounted, computed, watch, nextTick } from "vue"
import CheckoutStepper from "@/components/checkout/CheckoutStepper.vue"
import CheckoutShippingForm from "@/components/checkout/CheckoutShippingForm.vue"
import CheckoutPaymentForm from "@/components/checkout/CheckoutPaymentForm.vue"
import OrderSummary from "@/components/common/OrderSummary.vue"
import { useCart } from "@/composables/useCart.js"
import { useRouter } from "vue-router"
import { useToast } from "@/composables/useToast.js"
import axios from "axios"
import { loadStripe } from "@stripe/stripe-js"

const { items, fetchCart, clearCart } = useCart()
const router = useRouter()
const { success, error: toastError, info } = useToast()

const stripe = ref(null)
const elements = ref(null)
const cardElement = ref(null)
const isProcessing = ref(false)

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

// Initialize Stripe
const initStripe = async () => {
  const publishableKey = import.meta.env.VITE_STRIPE_PUBLISHABLE_KEY
  if (!publishableKey) {
    console.error("Stripe Publishable Key is missing from .env")
    toastError("Payment system configuration error.")
    return
  }

  if (!stripe.value) {
    stripe.value = await loadStripe(publishableKey)
  }
  
  if (stripe.value) {
    // Always create a new elements instance and card element to ensure fresh mount
    if (cardElement.value) {
      cardElement.value.unmount()
    }
    
    elements.value = stripe.value.elements()
    cardElement.value = elements.value.create("card", {
      style: {
        base: {
          fontSize: "16px",
          color: "#32325d",
          "::placeholder": {
            color: "#aab7c4"
          }
        },
        invalid: {
          color: "#fa755a",
          iconColor: "#fa755a"
        }
      }
    })
    
    // Wait for DOM to update
    await nextTick()
    const element = document.getElementById("stripe-card-element")
    if (element) {
      cardElement.value.mount("#stripe-card-element")
    } else {
      console.error("Stripe card element mount point not found")
    }
  }
}

// Watch for step and method changes to init Stripe
watch([currentStep, () => paymentDetails.value.method], async ([newStep, newMethod]) => {
  if (newStep === "review" && newMethod === "stripe") {
    // Small delay to ensure the DOM element is rendered
    setTimeout(initStripe, 100)
  }
})

const handlePlaceOrder = async () => {
  if (isProcessing.value) return
  
  // Frontend Validation
  if (!items.value || items.value.length === 0) {
    toastError("Your cart is empty.")
    return
  }

  const s = shippingDetails.value
   if (!s.fullName || !s.email || !s.phoneNumber || !s.streetAddress || !s.city || !s.state || !s.zipCode || !s.country) {
     toastError("Please fill in all shipping details.")
     currentStep.value = "shipping"
     return
   }
   
   // Basic email validation
   const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
   if (!emailRegex.test(s.email)) {
     toastError("Please enter a valid email address.")
     currentStep.value = "shipping"
     return
   }
  
  const p = paymentDetails.value
  if (p.method === 'card' && (!p.cardNumber || !p.cardName || !p.expiry || !p.cvv)) {
    toastError("Please fill in all card details.")
    currentStep.value = "payment"
    return
  }

  isProcessing.value = true
  console.log('handlePlaceOrder started with data:', { shipping: shippingDetails.value, payment: paymentDetails.value })

  try {
    const orderData = {
      shipping: shippingDetails.value,
      payment: paymentDetails.value,
      items: items.value.map(item => ({
        productId: item.productId,
        variantId: item.variantId,
        quantity: item.quantity,
        price: item.price,
        name: item.name,
        size: item.size !== '-' ? item.size : null,
        color: item.color !== '-' ? item.color : null
      })),
      totals: {
        subtotal: subtotal.value,
        shipping: 0,
        tax: tax.value,
        total: total.value
      }
    }

    console.log('Sending order data to backend:', orderData)
    const response = await axios.post('/orders', orderData)
    const order = response.data.order
    console.log('Backend order response received:', order)
    
    if (!order || !order.id) {
      throw new Error("Order creation failed: No order ID received from server.")
    }

    if (paymentDetails.value.method === 'stripe') {
      info("Processing secure payment...")
      console.log('Stripe method selected, initializing confirmCardPayment...')
      
      const { error: stripeError, paymentIntent } = await stripe.value.confirmCardPayment(
        order.stripe_client_secret,
        {
          payment_method: {
            card: cardElement.value,
            billing_details: {
              name: shippingDetails.value.fullName,
              email: shippingDetails.value.email,
              address: {
                line1: shippingDetails.value.streetAddress,
                city: shippingDetails.value.city,
                state: shippingDetails.value.state,
                postal_code: shippingDetails.value.zipCode,
                country: 'US'
              }
            }
          }
        }
      )

      if (stripeError) {
        console.error('Stripe Error:', stripeError)
        toastError(stripeError.message || "Stripe payment failed.")
        isProcessing.value = false
        return
      }

      console.log('Stripe payment intent result:', paymentIntent)
      if (paymentIntent.status === 'succeeded') {
        // Confirm payment with backend
        console.log('Payment succeeded, confirming with backend...')
        await axios.post(`/orders/${order.id}/confirm-payment`)
        success("Payment successful! Order placed.")
      } else if (paymentIntent.status === 'processing') {
        info("Your payment is processing.")
      } else {
        toastError(`Payment status: ${paymentIntent.status}. Please check your order status.`)
      }
    } else {
      success("Order placed successfully!")
    }

    // Navigation should happen after order is confirmed/processed
    console.log('Clearing cart and navigating to order detail page for ID:', order.id)
    await fetchCart() // Refresh cart state (should be empty)
    router.push({ name: 'OrderDetail', params: { id: order.id.toString() } })
  } catch (err) {
    console.error('Full Error Object in handlePlaceOrder:', err)
    if (err.response) {
      console.error('Error Response Data:', err.response.data)
      console.error('Error Response Status:', err.response.status)
      console.error('Error Response Headers:', err.response.headers)
    }
    
    // Extract validation errors if they exist
    if (err.response?.status === 422) {
      if (err.response.data?.errors) {
        const firstError = Object.values(err.response.data.errors)[0][0]
        toastError(firstError)
      } else if (err.response.data?.message) {
        toastError(err.response.data.message)
      } else {
        toastError("Invalid order details. Please check your information.")
      }
    } else {
      toastError(err.response?.data?.message || "Failed to place order. Please try again.")
    }
  } finally {
    isProcessing.value = false
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

.checkout-page h3 {
  font-size: 1.25rem;
}

@media (min-width: 768px) {
  .checkout-page h3 {
    font-size: 1.5rem;
  }
}

@media (max-width: 576px) {
  .review-container .d-flex.gap-3 {
    flex-direction: column;
  }

  .review-container .d-flex.gap-3 .btn {
    width: 100%;
  }
}
</style>
