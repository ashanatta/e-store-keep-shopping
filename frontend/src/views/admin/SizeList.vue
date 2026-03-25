<template>
  <div class="size-admin">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h2>Size Management</h2>
    </div>

    <div class="card p-3 mb-4">
      <form class="row g-2 align-items-end" @submit.prevent="handleAdd">
        <div class="col-md-5">
          <label class="form-label">Size Name</label>
          <input v-model="newSize.name" type="text" class="form-control" placeholder="e.g. Medium" required />
        </div>
        <div class="col-md-3">
          <label class="form-label">Size Code (Short)</label>
          <input v-model="newSize.code" type="text" class="form-control" placeholder="e.g. M" />
        </div>
        <div class="col-md-4">
          <button class="btn btn-dark w-100" type="submit">Add Size</button>
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
          <tr v-for="size in sizes" :key="size.id">
            <td>
              <input
                v-if="editingId === size.id"
                v-model="editData.name"
                type="text"
                class="form-control form-control-sm"
              />
              <span v-else>{{ size.name }}</span>
            </td>
            <td>
              <input
                v-if="editingId === size.id"
                v-model="editData.code"
                type="text"
                class="form-control form-control-sm"
              />
              <span v-else>{{ size.code || '-' }}</span>
            </td>
            <td>
              <div class="d-flex gap-2">
                <template v-if="editingId === size.id">
                  <button class="btn btn-sm btn-primary" @click="saveEdit(size.id)">Save</button>
                  <button class="btn btn-sm btn-outline-secondary" @click="cancelEdit">Cancel</button>
                </template>
                <template v-else>
                  <button class="btn btn-sm btn-outline-primary" @click="startEdit(size)">Edit</button>
                  <button class="btn btn-sm btn-outline-danger" @click="remove(size.id)">Delete</button>
                </template>
              </div>
            </td>
          </tr>
          <tr v-if="sizes.length === 0">
            <td colspan="3" class="text-center text-muted py-4">No sizes yet</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'
import Swal from 'sweetalert2'

const sizes = ref([])
const newSize = ref({ name: '', code: '' })
const editingId = ref(null)
const editData = ref({ name: '', code: '' })

const fetchSizes = async () => {
  try {
    const response = await axios.get('/sizes')
    sizes.value = response.data
  } catch (error) {
    console.error('Error fetching sizes:', error)
    Swal.fire({
      title: 'Error',
      text: 'Failed to fetch sizes.',
      icon: 'error',
      confirmButtonColor: '#dc3545',
    })
  }
}

const handleAdd = async () => {
  try {
    const response = await axios.post('/sizes', newSize.value)
    sizes.value.push(response.data)
    newSize.value = { name: '', code: '' }
    
    Swal.fire({
      title: 'Success!',
      text: 'Size added successfully!',
      icon: 'success',
      timer: 2000,
      showConfirmButton: false
    })
  } catch (error) {
    console.error('Error adding size:', error)
    Swal.fire({
      title: 'Error',
      text: 'Failed to add size.',
      icon: 'error',
      confirmButtonColor: '#dc3545',
    })
  }
}

const startEdit = (size) => {
  editingId.value = size.id
  editData.value = { ...size }
}

const cancelEdit = () => {
  editingId.value = null
  editData.value = { name: '', code: '' }
}

const saveEdit = async (id) => {
  try {
    await axios.put(`/sizes/${id}`, editData.value)
    const index = sizes.value.findIndex(s => s.id === id)
    if (index !== -1) {
      sizes.value[index] = { ...editData.value, id }
    }
    editingId.value = null
    
    Swal.fire({
      title: 'Updated!',
      text: 'Size updated successfully!',
      icon: 'success',
      timer: 2000,
      showConfirmButton: false
    })
  } catch (error) {
    console.error('Error updating size:', error)
    Swal.fire({
      title: 'Error',
      text: 'Failed to update size.',
      icon: 'error',
      confirmButtonColor: '#dc3545',
    })
  }
}

const remove = async (id) => {
  const result = await Swal.fire({
    title: 'Are you sure?',
    text: "You want to delete this size?",
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#dc3545',
    cancelButtonColor: '#6c757d',
    confirmButtonText: 'Yes, delete it!'
  })

  if (result.isConfirmed) {
    try {
      await axios.delete(`/sizes/${id}`)
      sizes.value = sizes.value.filter(s => s.id !== id)
      
      Swal.fire({
        title: 'Deleted!',
        text: 'Size deleted successfully!',
        icon: 'success',
        timer: 2000,
        showConfirmButton: false
      })
    } catch (error) {
      console.error('Error deleting size:', error)
      Swal.fire({
        title: 'Error',
        text: 'Failed to delete size.',
        icon: 'error',
        confirmButtonColor: '#dc3545',
      })
    }
  }
}

onMounted(fetchSizes)
</script>

<style scoped>
.size-admin {
  padding: 20px;
}
</style>
