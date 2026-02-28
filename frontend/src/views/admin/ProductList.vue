<template>
  <div class="product-list-admin">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h2>Product Management</h2>
      <router-link to="/admin/products/create" class="btn btn-primary">Add New Product</router-link>
    </div>

    <div class="card p-3">
      <table class="table table-striped">
        <thead>
          <tr>
            <th>ID</th>
            <th>Image</th>
            <th>Name</th>
            <th>Category</th>
            <th>Price</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="product in products" :key="product.id">
            <td>{{ product.id }}</td>
            <td><img :src="`http://localhost:8000/storage/${product.image}`" alt="Product Image" style="width: 50px; height: 50px; object-fit: cover;"></td>
            <td>{{ product.name }}</td>
            <td>{{ product.category }}</td>
            <td>${{ product.price.toFixed(2) }}</td>
            <td>
              <router-link :to="`/admin/products/${product.id}/edit`" class="btn btn-sm btn-info me-2">Edit</router-link>
              <button @click="handleDelete(product.id)" class="btn btn-sm btn-danger">Delete</button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'

const products = ref([])

const fetchProducts = async () => {
  try {
    const response = await axios.get('/products')
    products.value = response.data
  } catch (error) {
    console.error('Error fetching products:', error)
    alert('Failed to fetch products.')
  }
}

const handleDelete = async (id) => {
  if (confirm(`Are you sure you want to delete product with ID: ${id}?`)) {
    try {
      await axios.delete(`/products/${id}`)
      products.value = products.value.filter(product => product.id !== id)
      alert('Product deleted successfully!')
    } catch (error) {
      console.error('Error deleting product:', error)
      alert('Failed to delete product.')
    }
  }
}

onMounted(fetchProducts)
</script>

<style scoped>
.product-list-admin {
  padding: 20px;
}
</style>
