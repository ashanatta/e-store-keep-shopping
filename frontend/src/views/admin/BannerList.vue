<template>
  <div class="admin-table-page">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h2 class="fw-bold mb-0">Banners</h2>
      <router-link to="/admin/banners/create" class="btn btn-dark rounded-pill px-4">
        <i class="bi bi-plus-lg me-1"></i> Add Banner
      </router-link>
    </div>

    <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
      <div class="card-body p-0">
        <div v-if="loading" class="p-5 text-center text-muted">
          <div class="spinner-border spinner-border-sm me-2"></div>
          Loading banners...
        </div>
        <div v-else-if="banners.length === 0" class="p-5 text-center text-muted">
          No banners found. Click "Add Banner" to create one.
        </div>
        <div v-else class="table-responsive">
          <table class="table table-hover align-middle mb-0">
            <thead class="bg-light">
              <tr>
                <th class="ps-4 py-3">Image</th>
                <th class="py-3">Title</th>
                <th class="py-3">Status</th>
                <th class="py-3">Created</th>
                <th class="py-3 pe-4 text-end">Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="banner in banners" :key="banner.id">
                <td class="ps-4">
                  <img 
                    :src="getImageUrl(banner.image)" 
                    class="banner-thumb rounded-3 shadow-sm" 
                    :alt="banner.title"
                  />
                </td>
                <td>
                  <div class="fw-semibold text-dark">{{ banner.title }}</div>
                  <div class="text-muted small text-truncate" style="max-width: 250px;">
                    {{ banner.description }}
                  </div>
                </td>
                <td>
                  <span :class="['badge rounded-pill', banner.is_active ? 'bg-success-subtle text-success' : 'bg-secondary-subtle text-secondary']">
                    {{ banner.is_active ? 'Active' : 'Inactive' }}
                  </span>
                </td>
                <td class="text-muted small">{{ formatDate(banner.created_at) }}</td>
                <td class="pe-4 text-end">
                  <div class="d-flex gap-2 justify-content-end">
                    <router-link :to="`/admin/banners/${banner.id}/edit`" class="btn btn-sm btn-light border">
                      <i class="bi bi-pencil"></i>
                    </router-link>
                    <button @click="deleteBanner(banner.id)" class="btn btn-sm btn-light border text-danger">
                      <i class="bi bi-trash"></i>
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'
import { getImageUrl as resolveImageUrl } from '@/utils/imageUrl'
import { useToast } from '@/composables/useToast'

const { success, error: toastError } = useToast()
const banners = ref([])
const loading = ref(true)

const fetchBanners = async () => {
  try {
    const res = await axios.get('/banners?all=true')
    banners.value = res.data
  } catch (err) {
    console.error('Failed to fetch banners:', err)
  } finally {
    loading.value = false
  }
}

const getImageUrl = (path) => {
  if (path && (path.startsWith('http://') || path.startsWith('https://'))) {
    return path
  }
  return resolveImageUrl(path)
}

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('en-US', {
    month: 'short',
    day: 'numeric',
    year: 'numeric'
  })
}

const deleteBanner = async (id) => {
  if (!confirm('Are you sure you want to delete this banner?')) return
  
  try {
    await axios.delete(`/admin/banners/${id}`)
    banners.value = banners.value.filter(b => b.id !== id)
    success('Banner deleted successfully')
  } catch (err) {
    toastError('Failed to delete banner')
  }
}

onMounted(fetchBanners)
</script>

<style scoped>
.banner-thumb {
  width: 100px;
  height: 50px;
  object-fit: cover;
}
.badge {
  font-weight: 500;
  padding: 6px 12px;
}
.bg-success-subtle { background-color: #dcfce7 !important; }
.bg-secondary-subtle { background-color: #f3f4f6 !important; }
</style>
