<template>
  <div class="category-admin">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h2>Category Management</h2>
    </div>

    <div class="card p-3 mb-4">
      <form class="row g-3 align-items-end" @submit.prevent="handleAdd">
        <div class="col-md-5">
          <label class="form-label">Category Name</label>
          <input v-model="newCategory" type="text" class="form-control" placeholder="e.g. Jackets" />
        </div>
        <div class="col-md-4">
          <label class="form-label">Category Image</label>
          <input type="file" class="form-control" @change="handleFileChange" accept="image/*" />
        </div>
        <div class="col-md-3">
          <button class="btn btn-dark w-100" type="submit" :disabled="loading">
            {{ loading ? 'Adding...' : 'Add Category' }}
          </button>
        </div>
      </form>
    </div>

    <div class="card p-3">
      <table class="table table-striped align-middle mb-0">
        <thead>
          <tr>
            <th style="width: 15%">Image</th>
            <th style="width: 45%">Name</th>
            <th style="width: 40%">Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="category in categories" :key="category.id">
            <td>
              <img
                v-if="category.image"
                :src="getImageUrl(category.image)"
                class="rounded"
                style="width: 50px; height: 50px; object-fit: cover;"
              />
              <div v-else class="bg-light rounded d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                <i class="bi bi-image text-muted"></i>
              </div>
            </td>
            <td>
              <div v-if="editingId !== category.id" class="fw-semibold">
                {{ category.name }}
              </div>
              <div v-else class="d-flex flex-column gap-2">
                <input
                  v-model="editValue"
                  type="text"
                  class="form-control form-control-sm"
                />
                <input type="file" class="form-control form-control-sm" @change="handleEditFileChange" accept="image/*" />
              </div>
            </td>
            <td>
              <div class="d-flex gap-2">
                <button
                  v-if="editingId !== category.id"
                  class="btn btn-sm btn-outline-primary"
                  type="button"
                  @click="startEdit(category)"
                >
                  Edit
                </button>
                <button
                  v-else
                  class="btn btn-sm btn-primary"
                  type="button"
                  @click="saveEdit(category)"
                  :disabled="loading"
                >
                  {{ loading ? 'Saving...' : 'Save' }}
                </button>
                <button
                  v-if="editingId === category.id"
                  class="btn btn-sm btn-outline-secondary"
                  type="button"
                  @click="cancelEdit"
                >
                  Cancel
                </button>
                <button
                  class="btn btn-sm btn-outline-danger"
                  type="button"
                  @click="remove(category)"
                >
                  Delete
                </button>
              </div>
            </td>
          </tr>
          <tr v-if="categories.length === 0">
            <td colspan="3" class="text-center text-muted py-4">No categories yet</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script setup>
import { computed, onMounted, ref } from "vue"
import { useCategories } from "@/composables/useCategories.js"
import { useToast } from "@/composables/useToast.js"
import { getImageUrl } from "@/utils/imageUrl"

const { categories, fetchCategories, createCategory, updateCategory, deleteCategory } =
  useCategories()
const { success, error: toastError } = useToast()

const newCategory = ref("")
const newCategoryImage = ref(null)
const editingId = ref(null)
const editValue = ref("")
const editCategoryImage = ref(null)
const loading = ref(false)

const handleFileChange = (e) => {
  newCategoryImage.value = e.target.files[0]
}

const handleEditFileChange = (e) => {
  editCategoryImage.value = e.target.files[0]
}

const handleAdd = async () => {
  const name = newCategory.value.trim()
  if (!name) {
    toastError("Category name is required")
    return
  }
  try {
    loading.value = true
    await createCategory(name, newCategoryImage.value)
    newCategory.value = ""
    newCategoryImage.value = null
    // Reset file input
    const fileInput = document.querySelector('input[type="file"]')
    if (fileInput) fileInput.value = ""
    success("Category added successfully")
  } catch (err) {
    toastError(err?.response?.data?.message || "Failed to add category")
  } finally {
    loading.value = false
  }
}

const startEdit = (category) => {
  editingId.value = category.id
  editValue.value = category.name
  editCategoryImage.value = null
}

const cancelEdit = () => {
  editingId.value = null
  editValue.value = ""
  editCategoryImage.value = null
}

const saveEdit = async (category) => {
  const name = editValue.value.trim()
  if (!name) {
    toastError("Category name is required")
    return
  }
  try {
    loading.value = true
    await updateCategory(category.id, name, editCategoryImage.value)
    cancelEdit()
    success("Category updated successfully")
  } catch (err) {
    toastError(err?.response?.data?.message || "Failed to update category")
  } finally {
    loading.value = false
  }
}

const remove = async (category) => {
  if (!confirm(`Delete category: ${category.name}?`)) return
  try {
    await deleteCategory(category.id)
    if (editingId.value === category.id) {
      cancelEdit()
    }
    success("Category deleted successfully")
  } catch (err) {
    toastError(err?.response?.data?.message || "Failed to delete category")
  }
}

onMounted(async () => {
  try {
    await fetchCategories()
  } catch (err) {
    toastError(err?.response?.data?.message || "Failed to load categories")
  }
})
</script>

<style scoped>
.category-admin {
  padding: 20px;
}
</style>
