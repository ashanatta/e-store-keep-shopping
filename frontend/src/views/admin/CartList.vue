<template>
  <div class="admin-table-page">
    <h2 class="mb-3">Cart Items</h2>
    <div class="card p-3">
      <div v-if="loading" class="text-muted">Loading...</div>
      <div v-else class="table-responsive">
        <table class="table table-striped align-middle">
          <thead>
            <tr>
              <th>ID</th>
              <th>User</th>
              <th>Email</th>
              <th>Product</th>
              <th>Variant</th>
              <th>Qty</th>
              <th>Created</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="item in items" :key="item.id">
              <td>{{ item.id }}</td>
              <td>{{ item.user?.name || "-" }}</td>
              <td>{{ item.user?.email || "-" }}</td>
              <td>{{ item.product?.name || "-" }}</td>
              <td>
                <span v-if="item.variant">
                  {{ item.variant.color?.name || "-" }} / {{ item.variant.size?.name || "-" }}
                </span>
                <span v-else>-</span>
              </td>
              <td>{{ item.quantity }}</td>
              <td>{{ formatDate(item.created_at) }}</td>
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

const items = ref([])
const loading = ref(false)

const formatDate = (value) => {
  if (!value) return "-"
  return new Date(value).toLocaleString()
}

const fetchItems = async () => {
  loading.value = true
  try {
    const response = await axios.get("/admin/carts")
    items.value = response.data || []
  } finally {
    loading.value = false
  }
}

onMounted(fetchItems)
</script>
