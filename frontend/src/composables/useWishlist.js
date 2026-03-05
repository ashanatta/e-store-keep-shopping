import { computed, reactive } from "vue"
import axios from "axios"
import { useAuth } from "@/composables/useAuth"

const state = reactive({
  items: [],
  initialized: false,
})

async function fetchWishlist() {
  const { isAuthenticated } = useAuth()
  if (!isAuthenticated.value) {
    state.items = []
    state.initialized = true
    return
  }

  const response = await axios.get("/wishlist")
  state.items = response.data.items || []
  state.initialized = true
}

async function add(productId) {
  await axios.post("/wishlist", { product_id: productId })
  await fetchWishlist()
}

async function remove(productId) {
  await axios.delete(`/wishlist/${productId}`)
  await fetchWishlist()
}

async function toggle(productId) {
  if (isInWishlist(productId).value) {
    await remove(productId)
  } else {
    await add(productId)
  }
}

function isInWishlist(productId) {
  return computed(() =>
    state.items.some((item) => Number(item.product_id) === Number(productId))
  )
}

export function useWishlist() {
  return {
    items: computed(() => state.items),
    initialized: computed(() => state.initialized),
    fetchWishlist,
    add,
    remove,
    toggle,
    isInWishlist,
  }
}
