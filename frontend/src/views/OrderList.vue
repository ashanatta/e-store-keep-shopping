<template>
  <div class="container py-5">
    <h3 class="fw-semibold mb-4">My Orders</h3>
    <div v-if="loading" class="text-muted">Loading...</div>
    <div v-else-if="orders.length === 0" class="alert alert-info">
      You have no orders yet.
    </div>
    <div v-else class="list-group">
      <router-link
        v-for="order in orders"
        :key="order.id"
        :to="{ name: 'OrderDetail', params: { id: order.id } }"
        class="list-group-item list-group-item-action d-flex justify-content-between align-items-center"
      >
        <div>
          <strong>#{{ order.order_number }}</strong>
          <span class="ms-2 badge" :class="statusBadgeClass(order.status)">{{ order.status }}</span>
        </div>
        <div class="text-muted small">
          {{ formatDate(order.created_at) }} · ${{ (parseFloat(order.total) || 0).toFixed(2) }}
        </div>
      </router-link>
    </div>
  </div>
</template>

<script setup>
import { onMounted, ref } from "vue"
import axios from "axios"

const orders = ref([])
const loading = ref(false)

const formatDate = (value) => {
  if (!value) return "-"
  return new Date(value).toLocaleDateString()
}

const statusBadgeClass = (status) => {
  const map = {
    pending: "bg-secondary",
    processing: "bg-primary",
    shipped: "bg-info",
    delivered: "bg-success",
    cancelled: "bg-danger",
  }
  return map[status] || "bg-secondary"
}

const fetchOrders = async () => {
  loading.value = true
  try {
    const response = await axios.get("/orders")
    orders.value = response.data || []
  } finally {
    loading.value = false
  }
}

onMounted(fetchOrders)
</script>
