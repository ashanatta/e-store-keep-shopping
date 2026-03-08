<template>
  <div class="container py-5">
    <div class="d-flex align-items-center justify-content-between mb-4">
      <h3 class="fw-semibold mb-0">My Orders</h3>
    </div>

    <div v-if="loading" class="text-center py-5">
      <div class="spinner-border text-dark" role="status">
        <span class="visually-hidden">Loading...</span>
      </div>
    </div>

    <div v-else-if="orders.length === 0" class="text-center py-5">
      <div class="mb-4">
        <i class="bi bi-bag-x fs-1 text-muted"></i>
      </div>
      <h5>No orders yet</h5>
      <p class="text-muted">You haven't placed any orders yet.</p>
      <router-link to="/shop" class="btn btn-dark px-4 py-2 rounded-pill mt-3">
        Start Shopping
      </router-link>
    </div>

    <div v-else class="orders-list">
      <div v-for="order in orders" :key="order.id" class="order-card mb-4 p-4 shadow-sm border-0">
        <div class="row align-items-center g-3">
          <div class="col-md-3">
            <div class="text-muted small mb-1">Order Number</div>
            <div class="fw-bold text-dark">{{ order.order_number }}</div>
          </div>
          <div class="col-md-2 text-md-center">
            <div class="text-muted small mb-1">Date</div>
            <div class="fw-semibold">{{ formatDate(order.created_at) }}</div>
          </div>
          <div class="col-md-2 text-md-center">
            <div class="text-muted small mb-1">Total</div>
            <div class="fw-bold text-dark">${{ parseFloat(order.total).toFixed(2) }}</div>
          </div>
          <div class="col-md-2 text-md-center">
            <div class="text-muted small mb-1">Status</div>
            <span :class="getStatusBadgeClass(order.status)">
              {{ capitalize(order.status) }}
            </span>
          </div>
          <div class="col-md-3 text-md-end">
            <button @click="viewOrder(order.id)" class="btn btn-outline-dark rounded-pill px-4">
              View Details
            </button>
          </div>
        </div>
        
        <div class="order-items mt-4 pt-4 border-top">
          <div class="d-flex gap-2 flex-wrap">
            <div v-for="item in order.items.slice(0, 4)" :key="item.id" class="order-item-thumb">
              <img :src="getDisplayImage(item.product?.image)" :alt="item.product_name" class="rounded border" />
            </div>
            <div v-if="order.items.length > 4" class="more-items-count">
              +{{ order.items.length - 4 }} more
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
import { useRouter } from 'vue-router'

const orders = ref([])
const loading = ref(true)
const router = useRouter()

const fetchOrders = async () => {
  loading.value = true
  try {
    const response = await axios.get('/orders')
    orders.value = response.data
  } catch (error) {
    console.error('Error fetching orders:', error)
  } finally {
    loading.value = false
  }
}

const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  })
}

const getStatusBadgeClass = (status) => {
  const base = 'badge rounded-pill px-3 py-2 '
  switch (status) {
    case 'pending': return base + 'bg-warning text-dark'
    case 'processing': return base + 'bg-info text-dark'
    case 'shipped': return base + 'bg-primary'
    case 'delivered': return base + 'bg-success'
    case 'cancelled': return base + 'bg-danger'
    default: return base + 'bg-secondary'
  }
}

const capitalize = (s) => s.charAt(0).toUpperCase() + s.slice(1)

const getDisplayImage = (path) => {
  return path ? `http://localhost:8000/api/files/${path}` : 'https://via.placeholder.com/100x120'
}

const viewOrder = (orderId) => {
  router.push(`/orders/${orderId}`)
}

onMounted(fetchOrders)
</script>

<style scoped>
.order-card {
  background: #fff;
  border-radius: 16px;
  transition: transform 0.2s ease;
}

.order-card:hover {
  transform: translateY(-2px);
}

.order-item-thumb img {
  width: 50px;
  height: 60px;
  object-fit: cover;
}

.more-items-count {
  width: 50px;
  height: 60px;
  display: flex;
  align-items: center;
  justify-content: center;
  background: #f8f9fa;
  border: 1px solid #dee2e6;
  border-radius: 4px;
  font-size: 12px;
  color: #6c757d;
  font-weight: 600;
}

.border-top {
  border-top: 1px solid #f1f3f5 !important;
}
</style>