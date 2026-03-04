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
          <label class="form-label">Color Code (Hex/Text)</label>
          <input v-model="newColor.code" type="text" class="form-control" placeholder="e.g. #FF0000" />
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
                type="text"
                class="form-control form-control-sm"
              />
              <span v-else>
                <span 
                  v-if="color.code" 
                  class="d-inline-block border" 
                  :style="{ backgroundColor: color.code, width: '16px', height: '16px', verticalAlign: 'middle', marginRight: '5px' }"
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

const colors = ref([])
const newColor = ref({ name: '', code: '' })
const editingId = ref(null)
const editData = ref({ name: '', code: '' })

const fetchColors = async () => {
  try {
    const response = await axios.get('/colors')
    colors.value = response.data
  } catch (error) {
    console.error('Error fetching colors:', error)
    alert('Failed to fetch colors.')
  }
}

const handleAdd = async () => {
  try {
    const response = await axios.post('/colors', newColor.value)
    colors.value.push(response.data)
    newColor.value = { name: '', code: '' }
    alert('Color added successfully!')
  } catch (error) {
    console.error('Error adding color:', error)
    alert('Failed to add color.')
  }
}

const startEdit = (color) => {
  editingId.value = color.id
  editData.value = { ...color }
}

const cancelEdit = () => {
  editingId.value = null
  editData.value = { name: '', code: '' }
}

const saveEdit = async (id) => {
  try {
    await axios.put(`/colors/${id}`, editData.value)
    const index = colors.value.findIndex(c => c.id === id)
    if (index !== -1) {
      colors.value[index] = { ...editData.value, id }
    }
    editingId.value = null
    alert('Color updated successfully!')
  } catch (error) {
    console.error('Error updating color:', error)
    alert('Failed to update color.')
  }
}

const remove = async (id) => {
  if (confirm('Are you sure you want to delete this color?')) {
    try {
      await axios.delete(`/colors/${id}`)
      colors.value = colors.value.filter(c => c.id !== id)
      alert('Color deleted successfully!')
    } catch (error) {
      console.error('Error deleting color:', error)
      alert('Failed to delete color.')
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
