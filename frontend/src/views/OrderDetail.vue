<template>
  <div class="container py-5">
    <div v-if="loading" class="text-center py-5">
      <div class="spinner-border text-dark" role="status">
        <span class="visually-hidden">Loading...</span>
      </div>
    </div>

    <div v-else-if="!order" class="text-center py-5">
      <div class="mb-4">
        <i class="bi bi-exclamation-circle fs-1 text-muted"></i>
      </div>
      <h5>Order not found</h5>
      <p class="text-muted">The order you're looking for doesn't exist.</p>
      <router-link to="/orders" class="btn btn-dark px-4 py-2 rounded-pill mt-3">
        Go Back to Orders
      </router-link>
    </div>

    <div v-else class="order-detail">
      <div class="d-flex align-items-center gap-3 mb-4">
        <router-link to="/orders" class="btn btn-outline-dark rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
          <i class="bi bi-arrow-left fs-5"></i>
        </router-link>
        <h3 class="fw-semibold mb-0">Order #{{ order.order_number }}</h3>
      </div>

      <div class="row g-4">
        <!-- Main Content -->
        <div class="col-lg-8">
          <!-- Order Items -->
          <div class="card p-4 mb-4 border-0 shadow-sm rounded-4">
            <h5 class="fw-semibold mb-4">Order Items</h5>
            <div v-for="item in order.items" :key="item.id" class="d-flex gap-4 align-items-center mb-4 pb-4 border-bottom-light">
              <img :src="getDisplayImage(item.product?.image)" :alt="item.product_name" class="order-item-image rounded-3 shadow-sm" />
              <div class="flex-grow-1 min-w-0">
                <div class="fw-bold fs-5 text-dark text-truncate">{{ item.product_name }}</div>
                <div class="text-muted small mt-1">{{ item.variant_info }}</div>
                <div class="text-muted small">Qty: {{ item.quantity }}</div>
              </div>
              <div class="text-end">
                <div class="fw-bold fs-5 text-dark">${{ parseFloat(item.price).toFixed(2) }}</div>
                <div v-if="item.quantity > 1" class="text-muted small">${{ (parseFloat(item.price) * item.quantity).toFixed(2) }} total</div>
              </div>
            </div>
          </div>

          <!-- Order Status / Tracking -->
          <div class="card p-4 mb-4 border-0 shadow-sm rounded-4">
            <h5 class="fw-semibold mb-4">Tracking Details</h5>
            <div class="tracking-stepper mt-3">
              <div v-for="(step, index) in trackingSteps" :key="index" class="tracking-step" :class="{ active: isStepActive(step.status), current: order.status === step.status }">
                <div class="step-icon">
                  <i :class="step.icon"></i>
                </div>
                <div class="step-content">
                  <div class="fw-bold mb-1">{{ step.label }}</div>
                  <div class="text-muted small">{{ step.description }}</div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
          <!-- Order Summary -->
          <div class="card p-4 mb-4 border-0 shadow-sm rounded-4">
            <h5 class="fw-semibold mb-4">Order Summary</h5>
            <div class="summary-row mb-3 d-flex justify-content-between">
              <span class="text-muted">Subtotal</span>
              <span class="fw-bold">${{ parseFloat(order.subtotal).toFixed(2) }}</span>
            </div>
            <div class="summary-row mb-3 d-flex justify-content-between">
              <span class="text-muted">Shipping</span>
              <span class="text-success fw-bold">FREE</span>
            </div>
            <div class="summary-row mb-3 d-flex justify-content-between">
              <span class="text-muted">Tax</span>
              <span class="fw-bold">${{ parseFloat(order.tax).toFixed(2) }}</span>
            </div>
            <div class="summary-row pt-3 mt-3 border-top d-flex justify-content-between align-items-center">
              <span class="fw-bold fs-4">Total</span>
              <span class="fw-bold fs-4 text-dark">${{ parseFloat(order.total).toFixed(2) }}</span>
            </div>
          </div>

          <!-- Shipping Info -->
          <div class="card p-4 mb-4 border-0 shadow-sm rounded-4">
            <h5 class="fw-semibold mb-4">Shipping Information</h5>
            <div class="text-muted">
              <p class="mb-1 fw-bold text-dark">{{ order.full_name }}</p>
              <p class="mb-1">{{ order.street_address }}</p>
              <p class="mb-1">{{ order.city }}, {{ order.state }} {{ order.zip_code }}</p>
              <p class="mb-0">{{ order.country }}</p>
            </div>
          </div>

          <!-- Payment Info -->
          <div class="card p-4 border-0 shadow-sm rounded-4">
            <h5 class="fw-semibold mb-4">Payment Details</h5>
            <div class="d-flex align-items-center gap-3">
              <div class="payment-icon bg-light rounded-3 p-2 d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                <i :class="getPaymentIcon(order.payment_method)" class="fs-4 text-dark"></i>
              </div>
              <div>
                <div class="fw-bold text-dark">{{ getPaymentMethodLabel(order.payment_method) }}</div>
                <div class="badge rounded-pill" :class="order.payment_status === 'paid' ? 'bg-success' : 'bg-warning text-dark'">
                  {{ order.payment_status.toUpperCase() }}
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'
import { useRoute } from 'vue-router'
import { getImageUrl } from '@/utils/imageUrl'

const route = useRoute()
const order = ref(null)
const loading = ref(true)

const trackingSteps = [
  { status: 'pending', label: 'Order Placed', description: 'Your order has been placed successfully.', icon: 'bi bi-clipboard-check' },
  { status: 'processing', label: 'Processing', description: 'We are preparing your order.', icon: 'bi bi-gear' },
  { status: 'shipped', label: 'Shipped', description: 'Your order is on the way.', icon: 'bi bi-truck' },
  { status: 'delivered', label: 'Delivered', description: 'Your order has been delivered.', icon: 'bi bi-house-check' }
]

const fetchOrder = async () => {
  loading.value = true
  try {
    const response = await axios.get(`/orders/${route.params.id}`)
    order.value = response.data
  } catch (error) {
    console.error('Error fetching order:', error)
  } finally {
    loading.value = false
  }
}

const getDisplayImage = (path) => getImageUrl(path)

const getPaymentIcon = (method) => {
  switch (method) {
    case 'card': return 'bi bi-credit-card'
    case 'stripe': return 'bi bi-stripe'
    case 'paypal': return 'bi bi-paypal'
    case 'cod': return 'bi bi-box-seam'
    default: return 'bi bi-cash'
  }
}

const getPaymentMethodLabel = (method) => {
  switch (method) {
    case 'card': return 'Credit/Debit Card'
    case 'stripe': return 'Stripe'
    case 'paypal': return 'PayPal'
    case 'cod': return 'Cash on Delivery'
    default: return method
  }
}

const isStepActive = (status) => {
  if (!order.value) return false
  const statusOrder = ['pending', 'processing', 'shipped', 'delivered']
  const orderIdx = statusOrder.indexOf(order.value.status)
  const stepIdx = statusOrder.indexOf(status)
  return stepIdx <= orderIdx
}

onMounted(fetchOrder)
</script>

<style scoped>
.order-item-image {
  width: 80px;
  height: 100px;
  object-fit: cover;
}

.border-bottom-light {
  border-bottom: 1px solid #f8f9fa;
}

.tracking-stepper {
  display: flex;
  flex-direction: column;
  gap: 24px;
}

.tracking-step {
  display: flex;
  gap: 20px;
  position: relative;
}

.tracking-step:not(:last-child)::after {
  content: '';
  position: absolute;
  left: 22px;
  top: 44px;
  bottom: -24px;
  width: 2px;
  background: #f1f3f5;
}

.step-icon {
  width: 44px;
  height: 44px;
  border-radius: 50%;
  background: #f8f9fa;
  color: #adb5bd;
  display: grid;
  place-items: center;
  font-size: 20px;
  z-index: 1;
  transition: all 0.3s ease;
}

.tracking-step.active .step-icon {
  background: #111827;
  color: white;
}

.tracking-step.active:not(:last-child)::after {
  background: #111827;
}

.tracking-step.current .step-icon {
  box-shadow: 0 0 0 4px rgba(17, 24, 39, 0.1);
}

.border-top {
  border-top: 1px solid #f1f3f5 !important;
}

.min-w-0 {
  min-width: 0;
}
</style>