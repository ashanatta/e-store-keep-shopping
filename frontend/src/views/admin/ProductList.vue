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
            <th>From Price</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="product in products" :key="product.id">
            <td>{{ product.id }}</td>
            <td><img :src="getImageUrl(product.image)" alt="Product Image" style="width: 50px; height: 50px; object-fit: cover;"></td>
            <td>{{ product.name }}</td>
            <td>{{ product.category?.name || '-' }}</td>
            <td>
              <span class="fw-bold">${{ product.final_price.toFixed(2) }}</span>
              <span
                v-if="product.sale_price && product.final_price !== product.price"
                class="text-muted text-decoration-line-through ms-2"
              >
                ${{ product.price.toFixed(2) }}
              </span>
            </td>
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
import { getImageUrl } from '@/utils/imageUrl'
import Swal from 'sweetalert2'

const products = ref([])

const fetchProducts = async () => {
  try {
    const response = await axios.get('/products')
    products.value = response.data.map(p => ({
      ...p,
      price: Number(p.price || 0),
      sale_price: p.sale_price ? Number(p.sale_price) : null,
      sale_start: p.sale_start || null,
      sale_end: p.sale_end || null,
      final_price: Number(p.final_price || p.price || 0),
    }))
  } catch (error) {
    console.error('Error fetching products:', error)
    Swal.fire({
      title: 'Error',
      text: 'Failed to fetch products.',
      icon: 'error',
      confirmButtonColor: '#dc3545',
    })
  }
}

const handleDelete = async (id) => {
  const result = await Swal.fire({
    title: 'Are you sure?',
    text: `You are about to delete product with ID: ${id}. This action cannot be undone!`,
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#dc3545',
    cancelButtonColor: '#6c757d',
    confirmButtonText: 'Yes, delete it!'
  })

  if (result.isConfirmed) {
    try {
      await axios.delete(`/products/${id}`)
      products.value = products.value.filter(product => product.id !== id)
      
      Swal.fire({
        title: 'Deleted!',
        text: 'Product deleted successfully!',
        icon: 'success',
        timer: 2000,
        showConfirmButton: false
      })
    } catch (error) {
      console.error('Error deleting product:', error)
      Swal.fire({
        title: 'Error',
        text: 'Failed to delete product.',
        icon: 'error',
        confirmButtonColor: '#dc3545',
      })
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
