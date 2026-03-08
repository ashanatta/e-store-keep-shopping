<template>
  <div class="color-admin">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h2>Color Management</h2>
    </div>

    <div class="card p-3 mb-4">
      <form class="row g-2 align-items-end" @submit.prevent="handleAdd">
        <div class="col-md-5">
          <label class="form-label">Color Name</label>
          <input v-model="newColor.name" type="text" class="form-control" placeholder="e.g. Red" required />
        </div>
        <div class="col-md-3">
          <label class="form-label">Select Color</label>
          <div class="d-flex align-items-center gap-2">
            <input v-model="newColor.code" type="color" class="form-control form-control-color" />
            <span class="small text-muted">{{ newColor.code }}</span>
          </div>
        </div>
        <div class="col-md-4">
          <button class="btn btn-dark w-100" type="submit">Add Color</button>
        </div>
      </form>
    </div>

    <div class="card p-3">
      <table class="table table-striped mb-0">
        <thead>
          <tr>
            <th>Name</th>
            <th>Code</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="color in colors" :key="color.id">
            <td>
              <input
                v-if="editingId === color.id"
                v-model="editData.name"
                type="text"
                class="form-control form-control-sm"
              />
              <span v-else>{{ color.name }}</span>
            </td>
            <td>
              <input
                v-if="editingId === color.id"
                v-model="editData.code"
                type="color"
                class="form-control form-control-color form-control-sm"
              />
              <span v-if="editingId === color.id" class="small text-muted ms-2">{{ editData.code }}</span>
              <span v-else>
                <span 
                  v-if="color.code" 
                  class="d-inline-block border" 
                  :style="{ backgroundColor: getSafeColorCode(color.code), width: '16px', height: '16px', verticalAlign: 'middle', marginRight: '5px' }"
                ></span>
                {{ color.code || '-' }}
              </span>
            </td>
            <td>
              <div class="d-flex gap-2">
                <template v-if="editingId === color.id">
                  <button class="btn btn-sm btn-primary" @click="saveEdit(color.id)">Save</button>
                  <button class="btn btn-sm btn-outline-secondary" @click="cancelEdit">Cancel</button>
                </template>
                <template v-else>
                  <button class="btn btn-sm btn-outline-primary" @click="startEdit(color)">Edit</button>
                  <button class="btn btn-sm btn-outline-danger" @click="remove(color.id)">Delete</button>
                </template>
              </div>
            </td>
          </tr>
          <tr v-if="colors.length === 0">
            <td colspan="3" class="text-center text-muted py-4">No colors yet</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'
import { useToast } from '@/composables/useToast.js'

const colors = ref([])
const newColor = ref({ name: '', code: '#000000' })
const editingId = ref(null)
const editData = ref({ name: '', code: '' })
const { success, error: toastError } = useToast()

const normalizeHexColor = (value) => {
  if (!value) return '#000000'
  const code = value.trim()
  if (/^#[0-9a-fA-F]{6}$/.test(code)) {
    return code.toUpperCase()
  }
  if (/^#[0-9a-fA-F]{3}$/.test(code)) {
    const [r, g, b] = code.slice(1).split('')
    return `#${r}${r}${g}${g}${b}${b}`.toUpperCase()
  }
  return '#000000'
}

const getSafeColorCode = (value) => normalizeHexColor(value)

const fetchColors = async () => {
  try {
    const response = await axios.get('/colors')
    colors.value = response.data
  } catch (err) {
    console.error('Error fetching colors:', err)
    toastError('Failed to fetch colors.')
  }
}

const handleAdd = async () => {
  try {
    const payload = {
      name: newColor.value.name,
      code: normalizeHexColor(newColor.value.code),
    }
    const response = await axios.post('/colors', payload)
    colors.value.push(response.data)
    newColor.value = { name: '', code: '#000000' }
    success('Color added successfully!')
  } catch (err) {
    console.error('Error adding color:', err)
    toastError('Failed to add color.')
  }
}

const startEdit = (color) => {
  editingId.value = color.id
  editData.value = {
    ...color,
    code: normalizeHexColor(color.code),
  }
}

const cancelEdit = () => {
  editingId.value = null
  editData.value = { name: '', code: '' }
}

const saveEdit = async (id) => {
  try {
    const payload = {
      ...editData.value,
      code: normalizeHexColor(editData.value.code),
    }
    await axios.put(`/colors/${id}`, payload)
    const index = colors.value.findIndex(c => c.id === id)
    if (index !== -1) {
      colors.value[index] = { ...payload, id }
    }
    editingId.value = null
    success('Color updated successfully!')
  } catch (err) {
    console.error('Error updating color:', err)
    toastError('Failed to update color.')
  }
}

const remove = async (id) => {
  if (confirm('Are you sure you want to delete this color?')) {
    try {
      await axios.delete(`/colors/${id}`)
      colors.value = colors.value.filter(c => c.id !== id)
      success('Color deleted successfully!')
    } catch (err) {
      console.error('Error deleting color:', err)
      toastError('Failed to delete color.')
    }
  }
}

onMounted(fetchColors)
</script>

<style scoped>
.color-admin {
  padding: 20px;
}
</style>
