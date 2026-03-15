import { computed, reactive } from "vue"
import axios from "axios"

const state = reactive({
  categories: [],
  loaded: false
})

async function fetchCategories() {
  const response = await axios.get("/categories")
  state.categories = response.data || []
  state.loaded = true
}

async function createCategory(name, imageFile = null) {
  const formData = new FormData()
  formData.append('name', name)
  if (imageFile) {
    formData.append('image', imageFile)
  }
  const response = await axios.post("/categories", formData, {
    headers: { 'Content-Type': 'multipart/form-data' }
  })
  state.categories = [...state.categories, response.data]
  return response.data
}

async function updateCategory(id, name, imageFile = null) {
  const formData = new FormData()
  formData.append('name', name)
  if (imageFile) {
    formData.append('image', imageFile)
  }
  // Use POST with _method=PUT for Laravel to handle multipart/form-data updates
  formData.append('_method', 'PUT')
  const response = await axios.post(`/categories/${id}`, formData, {
    headers: { 'Content-Type': 'multipart/form-data' }
  })
  state.categories = state.categories.map((item) =>
    item.id === id ? response.data : item
  )
  return response.data
}

async function deleteCategory(id) {
  await axios.delete(`/categories/${id}`)
  state.categories = state.categories.filter((item) => item.id !== id)
}

export function useCategories() {
  return {
    categories: computed(() => state.categories),
    loaded: computed(() => state.loaded),
    fetchCategories,
    createCategory,
    updateCategory,
    deleteCategory
  }
}
