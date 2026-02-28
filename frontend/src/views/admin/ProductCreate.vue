<template>
  <div class="product-create-admin">
    <h2>Add New Product</h2>
    <form @submit.prevent="handleSubmit">
      <div class="mb-3">
        <label for="name" class="form-label">Product Name</label>
        <input type="text" class="form-control" id="name" v-model="product.name" required>
      </div>
      <div class="mb-3">
        <label for="description" class="form-label">Description</label>
        <textarea class="form-control" id="description" v-model="product.description" rows="3"></textarea>
      </div>
      <div class="mb-3">
        <label for="price" class="form-label">Price</label>
        <input type="number" class="form-control" id="price" v-model="product.price" required min="0" step="0.01">
      </div>
      <div class="mb-3">
        <label for="category" class="form-label">Category</label>
        <input type="text" class="form-control" id="category" v-model="product.category">
      </div>
      <div class="mb-3">
        <label for="image" class="form-label">Product Image</label>
        <input type="file" class="form-control" id="image" @change="handleImageChange" accept="image/*">
      </div>
      <button type="submit" class="btn btn-primary">Add Product</button>
      <router-link to="/admin/products" class="btn btn-secondary ms-2">Cancel</router-link>
    </form>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'

const router = useRouter()

const product = ref({
  name: '',
  description: '',
  price: 0,
  category: '',
})

const imageFile = ref(null)

const handleImageChange = (event) => {
  imageFile.value = event.target.files[0]
}

const handleSubmit = async () => {
  try {
    const formData = new FormData()
    formData.append('name', product.value.name)
    formData.append('description', product.value.description)
    formData.append('price', product.value.price)
    formData.append('category', product.value.category)
    if (imageFile.value) {
      formData.append('image', imageFile.value)
    }

    await axios.post('/products', formData, {
      headers: {
        'Content-Type': 'multipart/form-data',
      },
    })
    alert('Product added successfully!')
    router.push('/admin/products')
  } catch (error) {
    console.error('Error adding product:', error)
    alert('Failed to add product.')
  }
}
</script>

<style scoped>
.product-create-admin {
  max-width: 600px;
  margin: 0 auto;
  padding: 20px;
  background-color: #fff;
  border-radius: 8px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}
</style>
