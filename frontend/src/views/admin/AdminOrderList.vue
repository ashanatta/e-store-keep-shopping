<template>
  <div class="admin-table-page">
    <h2 class="mb-3">Orders</h2>
    <div class="card p-3">
      <div v-if="loading" class="text-muted">Loading...</div>
      <div v-else class="table-responsive">
        <table class="table table-striped align-middle">
          <thead>
            <tr>
              <th>ID</th>
              <th>Order #</th>
              <th>Customer</th>
              <th>Total</th>
              <th>Status</th>
              <th>Date</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="order in orders" :key="order.id">
              <td>{{ order.id }}</td>
              <td>{{ order.order_number }}</td>
              <td>{{ order.user?.name || "-" }}<br><small class="text-muted">{{ order.user?.email }}</small></td>
              <td>${{ order.total?.toFixed(2) }}</td>
              <td>
                <select
                  v-model="order.status"
                  class="form-select form-select-sm"
                  style="width: auto;"
                  @change="updateStatus(order)"
                >
                  <option value="pending">Pending</option>
                  <option value="processing">Processing</option>
                  <option value="shipped">Shipped</option>
                  <option value="delivered">Delivered</option>
                  <option value="cancelled">Cancelled</option>
                </select>
              </td>
              <td>{{ formatDate(order.created_at) }}</td>
              <td>
                <router-link :to="{ name: 'OrderDetail', params: { id: order.id } }" class="btn btn-sm btn-outline-primary">
                  View
                </router-link>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
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
  return new Date(value).toLocaleString()
}

const fetchOrders = async () => {
  loading.value = true
  try {
    const response = await axios.get("/admin/orders")
    orders.value = response.data || []
  } finally {
    loading.value = false
  }
}

const updateStatus = async (order) => {
  try {
    await axios.patch(`/admin/orders/${order.id}/status`, { status: order.status })
  } catch (e) {
    order.status = order._prevStatus
  }
}

onMounted(fetchOrders)
</script>
