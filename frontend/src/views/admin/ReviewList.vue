<template>
  <div class="admin-table-page">
    <h2 class="mb-3">Product Reviews</h2>
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
              <th>Rating</th>
              <th>Comment</th>
              <th>Date</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="review in reviews" :key="review.id">
              <td>{{ review.id }}</td>
              <td>{{ review.user?.name || "-" }}</td>
              <td>{{ review.user?.email || "-" }}</td>
              <td>{{ review.product?.name || "-" }}</td>
              <td>{{ review.rating }}</td>
              <td>{{ review.comment || "-" }}</td>
              <td>{{ formatDate(review.created_at) }}</td>
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

const reviews = ref([])
const loading = ref(false)

const formatDate = (value) => {
  if (!value) return "-"
  return new Date(value).toLocaleString()
}

const fetchReviews = async () => {
  loading.value = true
  try {
    const response = await axios.get("/admin/reviews")
    reviews.value = response.data || []
  } finally {
    loading.value = false
  }
}

onMounted(fetchReviews)
</script>
