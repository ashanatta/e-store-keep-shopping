<template>
  <div class="container py-5">
    <div class="row justify-content-center">
      <div class="col-md-6">
        <div class="card shadow-sm">
          <div class="card-body p-4">
            <div class="text-center mb-4">
              <div class="avatar-lg mx-auto mb-3">{{ initial }}</div>
              <h4 class="fw-bold mb-0">{{ user?.name }}</h4>
              <p class="text-muted small">{{ user?.email }}</p>
            </div>

            <form @submit.prevent="handleSubmit">
              <div class="mb-3">
                <label class="form-label fw-semibold">Full Name</label>
                <input
                  v-model="form.name"
                  type="text"
                  class="form-control"
                  required
                />
              </div>

              <div class="mb-3">
                <label class="form-label fw-semibold">Email Address</label>
                <input
                  v-model="form.email"
                  type="email"
                  class="form-control"
                  required
                />
              </div>

              <hr class="my-4" />
              <p class="text-muted small mb-3">Leave password fields blank to keep your current password.</p>

              <div class="mb-3">
                <label class="form-label fw-semibold">New Password</label>
                <input
                  v-model="form.password"
                  type="password"
                  class="form-control"
                  placeholder="Min. 8 characters"
                />
              </div>

              <div class="mb-4">
                <label class="form-label fw-semibold">Confirm New Password</label>
                <input
                  v-model="form.password_confirmation"
                  type="password"
                  class="form-control"
                />
              </div>

              <div v-if="errorMsg" class="alert alert-danger py-2 small">{{ errorMsg }}</div>

              <div class="d-flex gap-2">
                <button type="submit" class="btn btn-dark flex-grow-1" :disabled="saving">
                  <span v-if="saving" class="spinner-border spinner-border-sm me-2"></span>
                  Save Changes
                </button>
                <router-link to="/" class="btn btn-outline-secondary">Cancel</router-link>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import axios from 'axios'
import { useAuth } from '@/composables/useAuth.js'
import { useToast } from '@/composables/useToast.js'

const { user, refreshUser } = useAuth()
const { success, error: toastError } = useToast()

const saving = ref(false)
const errorMsg = ref('')

const form = ref({
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
})

const initial = computed(() => {
  const name = user.value?.name || ''
  return name.trim().charAt(0).toUpperCase() || 'U'
})

onMounted(() => {
  form.value.name = user.value?.name || ''
  form.value.email = user.value?.email || ''
})

const handleSubmit = async () => {
  errorMsg.value = ''
  if (form.value.password && form.value.password !== form.value.password_confirmation) {
    errorMsg.value = 'Passwords do not match.'
    return
  }
  saving.value = true
  try {
    const payload = {
      name: form.value.name,
      email: form.value.email,
    }
    if (form.value.password) {
      payload.password = form.value.password
      payload.password_confirmation = form.value.password_confirmation
    }
    await axios.put('/user', payload)
    if (refreshUser) await refreshUser()
    form.value.password = ''
    form.value.password_confirmation = ''
    success('Profile updated successfully!')
  } catch (err) {
    const msg = err.response?.data?.message || err.response?.data?.errors
    if (typeof msg === 'object') {
      errorMsg.value = Object.values(msg).flat().join(' ')
    } else {
      errorMsg.value = msg || 'Failed to update profile.'
    }
    toastError(errorMsg.value)
  } finally {
    saving.value = false
  }
}
</script>

<style scoped>
.avatar-lg {
  width: 72px;
  height: 72px;
  border-radius: 50%;
  background: #111827;
  color: #fff;
  display: grid;
  place-items: center;
  font-size: 28px;
  font-weight: 700;
}
</style>
