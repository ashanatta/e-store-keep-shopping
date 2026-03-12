<template>
  <div class="container py-5">
    <div v-if="loading" class="text-muted">Loading...</div>
    <div v-else-if="order" class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="mb-0">Order #{{ order.order_number }}</h4>
        <span class="badge" :class="statusBadgeClass(order.status)">{{ order.status }}</span>
      </div>
      <div class="card-body">
        <div class="row mb-4">
          <div class="col-md-6">
            <h6>Shipping</h6>
            <p class="mb-0">{{ order.full_name }}</p>
            <p class="mb-0">{{ order.street_address }}, {{ order.city }}, {{ order.state }} {{ order.zip_code }}</p>
            <p class="mb-0">{{ order.country }}</p>
          </div>
          <div class="col-md-6">
            <h6>Contact</h6>
            <p class="mb-0">{{ order.email }}</p>
            <p class="mb-0">{{ order.phone_number }}</p>
          </div>
        </div>
        <table class="table">
          <thead>
            <tr>
              <th>Product</th>
              <th>Qty</th>
              <th>Price</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="item in order.items" :key="item.id">
              <td>{{ item.product_name || item.product?.name }}</td>
              <td>{{ item.quantity }}</td>
              <td>${{ (item.price * item.quantity).toFixed(2) }}</td>
            </tr>
          </tbody>
        </table>
        <div class="text-end">
          <p class="mb-0"><strong>Total: ${{ order.total?.toFixed(2) }}</strong></p>
        </div>
      </div>
    </div>
    <router-link to="/orders" class="btn btn-outline-dark mt-3">Back to Orders</router-link>
  </div>
</template>

<script setup>
import { onMounted, ref } from "vue"
import { useRoute } from "vue-router"
import axios from "axios"

const route = useRoute()
const order = ref(null)
const loading = ref(false)

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

const fetchOrder = async () => {
  loading.value = true
  try {
    const response = await axios.get(`/orders/${route.params.id}`)
    order.value = response.data
  } finally {
    loading.value = false
  }
}

onMounted(fetchOrder)
</script>
