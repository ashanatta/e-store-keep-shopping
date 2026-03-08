<template>
  <div class="admin-table-page">
    <h2 class="mb-3">Order Management</h2>
    <div class="card p-3">
      <div v-if="loading" class="text-muted">Loading orders...</div>
      <div v-else class="table-responsive">
        <table class="table table-hover align-middle">
          <thead>
            <tr>
              <th>ID</th>
              <th>Order #</th>
              <th>User</th>
              <th>Total</th>
              <th>Status</th>
              <th>Payment</th>
              <th>Date</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="order in orders" :key="order.id">
              <td>{{ order.id }}</td>
              <td class="fw-semibold">{{ order.order_number }}</td>
              <td>
                <div>{{ order.user?.name || "-" }}</div>
                <div class="small text-muted">{{ order.user?.email }}</div>
              </td>
              <td>${{ parseFloat(order.total).toFixed(2) }}</td>
              <td>
                <select 
                  v-model="order.status" 
                  class="form-select form-select-sm" 
                  @change="updateStatus(order)"
                  :disabled="updating === order.id"
                >
                  <option value="pending">Pending</option>
                  <option value="processing">Processing</option>
                  <option value="shipped">Shipped</option>
                  <option value="delivered">Delivered</option>
                  <option value="cancelled">Cancelled</option>
                </select>
              </td>
              <td>
                <select 
                  v-model="order.payment_status" 
                  class="form-select form-select-sm" 
                  @change="updateStatus(order)"
                  :disabled="updating === order.id"
                >
                  <option value="pending">Pending</option>
                  <option value="paid">Paid</option>
                  <option value="failed">Failed</option>
                </select>
              </td>
              <td class="small">{{ formatDate(order.created_at) }}</td>
              <td>
                <button 
                  class="btn btn-sm btn-outline-primary" 
                  @click="viewDetails(order.id)"
                >
                  View Items
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Order Items Modal (Simple) -->
    <div v-if="selectedOrder" class="modal-backdrop show"></div>
    <div v-if="selectedOrder" class="modal show d-block" tabindex="-1">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Items for Order {{ selectedOrder.order_number }}</h5>
            <button type="button" class="btn-close" @click="selectedOrder = null"></button>
          </div>
          <div class="modal-body">
            <div class="table-responsive">
              <table class="table table-sm">
                <thead>
                  <tr>
                    <th>Product</th>
                    <th>Variant</th>
                    <th>Qty</th>
                    <th>Price</th>
                    <th>Subtotal</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="item in selectedOrder.items" :key="item.id">
                    <td>{{ item.product_name }}</td>
                    <td>{{ item.variant_info || "-" }}</td>
                    <td>{{ item.quantity }}</td>
                    <td>${{ parseFloat(item.price).toFixed(2) }}</td>
                    <td>${{ (item.quantity * item.price).toFixed(2) }}</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" @click="selectedOrder = null">Close</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { onMounted, ref } from "vue"
import axios from "axios"

const orders = ref([])
const loading = ref(false)
const updating = ref(null)
const selectedOrder = ref(null)

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
  updating.value = order.id
  try {
    await axios.patch(`/admin/orders/${order.id}/status`, {
      status: order.status,
      payment_status: order.payment_status
    })
    // Optionally show a toast/success message
  } catch (error) {
    console.error("Error updating order:", error)
    alert("Failed to update order status.")
    // Revert if needed, or re-fetch
    await fetchOrders()
  } finally {
    updating.value = null
  }
}

const viewDetails = (orderId) => {
  selectedOrder.value = orders.value.find(o => o.id === orderId)
}

onMounted(fetchOrders)
</script>

<style scoped>
.modal.show {
  background: rgba(0, 0, 0, 0.5);
}
</style>