<template>
  <div class="category-admin">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h2>Category Management</h2>
    </div>

    <div class="card p-3 mb-4">
      <form class="row g-2 align-items-end" @submit.prevent="handleAdd">
        <div class="col-md-8">
          <label class="form-label">Category Name</label>
          <input v-model="newCategory" type="text" class="form-control" placeholder="e.g. Jackets" />
        </div>
        <div class="col-md-4">
          <button class="btn btn-dark w-100" type="submit">Add Category</button>
        </div>
      </form>
    </div>

    <div class="card p-3">
      <table class="table table-striped mb-0">
        <thead>
          <tr>
            <th style="width: 60%">Name</th>
            <th style="width: 40%">Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="category in categories" :key="category.id">
            <td>
              <div v-if="editingId !== category.id" class="fw-semibold">
                {{ category.name }}
              </div>
              <input
                v-else
                v-model="editValue"
                type="text"
                class="form-control form-control-sm"
              />
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
                >
                  Save
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
            <td colspan="2" class="text-center text-muted py-4">No categories yet</td>
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

const { categories, fetchCategories, createCategory, updateCategory, deleteCategory } =
  useCategories()
const { success, error: toastError } = useToast()

const newCategory = ref("")
const editingId = ref(null)
const editValue = ref("")

const handleAdd = async () => {
  const name = newCategory.value.trim()
  if (!name) {
    toastError("Category name is required")
    return
  }
  try {
    await createCategory(name)
    newCategory.value = ""
    success("Category added successfully")
  } catch (err) {
    toastError(err?.response?.data?.message || "Failed to add category")
  }
}

const startEdit = (category) => {
  editingId.value = category.id
  editValue.value = category.name
}

const cancelEdit = () => {
  editingId.value = null
  editValue.value = ""
}

const saveEdit = async (category) => {
  const name = editValue.value.trim()
  if (!name) {
    toastError("Category name is required")
    return
  }
  try {
    await updateCategory(category.id, name)
    cancelEdit()
    success("Category updated successfully")
  } catch (err) {
    toastError(err?.response?.data?.message || "Failed to update category")
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
