<template>
  <div class="admin-form-page">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h2 class="fw-bold mb-0">Edit Banner</h2>
      <router-link to="/admin/banners" class="btn btn-outline-dark rounded-pill px-4">
        Back to Banners
      </router-link>
    </div>

    <div class="card shadow-sm border-0 rounded-4 p-4">
      <form v-if="banner" @submit.prevent="handleSubmit">
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
            <label class="form-label fw-semibold">Banner Image (Leave blank to keep current)</label>
            <input 
              type="file" 
              class="form-control" 
              @change="handleFileChange" 
              accept="image/*"
            />
            <div v-if="previewUrl || banner.image" class="mt-3">
              <img :src="previewUrl || getImageUrl(banner.image)" class="img-thumbnail rounded-3" style="max-height: 200px" alt="Preview" />
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
              :disabled="saving"
            >
              <span v-if="saving" class="spinner-border spinner-border-sm me-2"></span>
              Update Banner
            </button>
          </div>
        </div>
      </form>
      <div v-else class="text-center py-5">
        <div class="spinner-border text-dark"></div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import axios from 'axios'
import { getImageUrl as resolveImageUrl } from '@/utils/imageUrl'
import { useToast } from '@/composables/useToast'

const router = useRouter()
const route = useRoute()
const { success, error: toastError } = useToast()
const saving = ref(false)
const banner = ref(null)
const previewUrl = ref(null)

const form = ref({
  title: '',
  description: '',
  is_active: true,
  image: null
})

const fetchBanner = async () => {
  try {
    const res = await axios.get('/banners?all=true')
    const b = res.data.find(b => b.id == route.params.id)
    if (b) {
      banner.value = b
      form.value.title = b.title
      form.value.description = b.description
      form.value.is_active = Boolean(b.is_active)
    }
  } catch (err) {
    toastError('Failed to fetch banner details')
  }
}

const getImageUrl = (path) => {
  if (path && (path.startsWith('http://') || path.startsWith('https://'))) {
    return path
  }
  return resolveImageUrl(path)
}

const handleFileChange = (e) => {
  const file = e.target.files[0]
  if (file) {
    form.value.image = file
    previewUrl.value = URL.createObjectURL(file)
  }
}

const handleSubmit = async () => {
  saving.value = true
  
  const formData = new FormData()
  formData.append('title', form.value.title)
  formData.append('description', form.value.description || '')
  formData.append('is_active', form.value.is_active ? '1' : '0')
  if (form.value.image) {
    formData.append('image', form.value.image)
  }
  formData.append('_method', 'PUT')

  try {
    await axios.post(`/admin/banners/${route.params.id}`, formData, {
      headers: { 'Content-Type': 'multipart/form-data' }
    })
    success('Banner updated successfully')
    router.push('/admin/banners')
  } catch (err) {
    console.error('Failed to update banner:', err)
    toastError(err.response?.data?.message || 'Failed to update banner')
  } finally {
    saving.value = false
  }
}

onMounted(fetchBanner)
</script>
