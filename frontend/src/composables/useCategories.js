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

async function createCategory(name) {
  const response = await axios.post("/categories", { name })
  state.categories = [...state.categories, response.data]
  return response.data
}

async function updateCategory(id, name) {
  const response = await axios.put(`/categories/${id}`, { name })
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
