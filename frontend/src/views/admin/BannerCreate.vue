<template>
  <div class="admin-form-page">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h2 class="fw-bold mb-0">Add Banner</h2>
      <router-link to="/admin/banners" class="btn btn-outline-dark rounded-pill px-4">
        Back to Banners
      </router-link>
    </div>

    <div class="card shadow-sm border-0 rounded-4 p-4">
      <form @submit.prevent="handleSubmit">
        <div class="row g-4">
          <!-- Title -->
          <div class="col-md-12">
            <label class="form-label fw-semibold">Title</label>
            <input 
              v-model="form.title" 
              type="text" 
              class="form-control" 
              placeholder="Banner title" 
              required
            />
          </div>

          <!-- Description -->
          <div class="col-md-12">
            <label class="form-label fw-semibold">Description</label>
            <textarea 
              v-model="form.description" 
              class="form-control" 
              rows="3" 
              placeholder="Banner description"
            ></textarea>
          </div>

          <!-- Image -->
          <div class="col-md-12">
            <label class="form-label fw-semibold">Banner Image</label>
            <input 
              type="file" 
              class="form-control" 
              @change="handleFileChange" 
              accept="image/*"
              required
            />
            <div v-if="previewUrl" class="mt-3">
              <img :src="previewUrl" class="img-thumbnail rounded-3" style="max-height: 200px" alt="Preview" />
            </div>
          </div>

          <!-- Active Status -->
          <div class="col-md-12">
            <div class="form-check form-switch">
              <input 
                v-model="form.is_active" 
                class="form-check-input" 
                type="checkbox" 
                id="is_active"
              />
              <label class="form-check-label" for="is_active">Is Active</label>
            </div>
          </div>

          <div class="col-md-12 mt-5">
            <button 
              type="submit" 
              class="btn btn-dark rounded-pill px-5 py-2" 
              :disabled="loading"
            >
              <span v-if="loading" class="spinner-border spinner-border-sm me-2"></span>
              Create Banner
            </button>
          </div>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'
import { useToast } from '@/composables/useToast'

const router = useRouter()
const { success, error: toastError } = useToast()
const loading = ref(false)
const previewUrl = ref(null)

const form = ref({
  title: '',
  description: '',
  is_active: true,
  image: null
})

const handleFileChange = (e) => {
  const file = e.target.files[0]
  if (file) {
    form.value.image = file
    previewUrl.value = URL.createObjectURL(file)
  }
}

const handleSubmit = async () => {
  loading.value = true
  
  const formData = new FormData()
  formData.append('title', form.value.title)
  formData.append('description', form.value.description || '')
  formData.append('is_active', form.value.is_active ? '1' : '0')
  formData.append('image', form.value.image)

  try {
    await axios.post('/admin/banners', formData, {
      headers: { 'Content-Type': 'multipart/form-data' }
    })
    success('Banner created successfully')
    router.push('/admin/banners')
  } catch (err) {
    console.error('Failed to create banner:', err)
    toastError(err.response?.data?.message || 'Failed to create banner')
  } finally {
    loading.value = false
  }
}
</script>
