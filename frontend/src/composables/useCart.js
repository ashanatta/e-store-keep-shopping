import { computed, reactive } from "vue"
import axios from "axios"
import { useAuth } from "@/composables/useAuth"

const state = reactive({
  items: [],
  loading: false,
  initialized: false,
})

const getDisplayImage = (item) => {
  const path = item?.product?.image
  return path ? `http://localhost:8000/api/files/${path}` : "https://via.placeholder.com/300x400"
}

const normalizeItem = (item) => {
  const variant = item.variant || null
  return {
    id: item.id,
    quantity: item.quantity,
    productId: item.product_id,
    variantId: item.product_variant_id,
    name: item.product?.name || "Product",
    category: item.product?.category?.name || "-",
    size: variant?.size?.name || "-",
    color: variant?.color?.name || "-",
    price: Number(variant?.price || 0),
    image: getDisplayImage(item),
  }
}

async function fetchCart() {
  const { isAuthenticated } = useAuth()
  if (!isAuthenticated.value) {
    state.items = []
    state.initialized = true
    return
  }

  state.loading = true
  try {
    const response = await axios.get("/cart")
    state.items = (response.data.items || []).map(normalizeItem)
  } finally {
    state.loading = false
    state.initialized = true
  }
}

async function addToCart({ productId, variantId = null, quantity = 1 }) {
  await axios.post("/cart", {
    product_id: productId,
    product_variant_id: variantId,
    quantity,
  })
  await fetchCart()
}

async function updateQuantity(cartItemId, quantity) {
  await axios.patch(`/cart/${cartItemId}`, { quantity })
  await fetchCart()
}

async function removeItem(cartItemId) {
  await axios.delete(`/cart/${cartItemId}`)
  await fetchCart()
}

async function clearCart() {
  await axios.delete("/cart")
  state.items = []
}

export function useCart() {
  const count = computed(() =>
    state.items.reduce((sum, item) => sum + Number(item.quantity || 0), 0)
  )

  return {
    items: computed(() => state.items),
    count,
    loading: computed(() => state.loading),
    initialized: computed(() => state.initialized),
    fetchCart,
    addToCart,
    updateQuantity,
    removeItem,
    clearCart,
  }
}
