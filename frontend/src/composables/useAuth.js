import { reactive, computed } from "vue"
import axios from "axios"

axios.defaults.baseURL = import.meta.env.VITE_API_BASE_URL
axios.defaults.withCredentials = true

const state = reactive({
  user: JSON.parse(localStorage.getItem("auth:user") || "null"),
})

function setUser(user) {
  state.user = user
  if (user) {
    localStorage.setItem("auth:user", JSON.stringify(user))
  } else {
    localStorage.removeItem("auth:user")
  }
}

async function login({ email, password }) {
  const response = await axios.post("/login", {
    email,
    password,
    device_name: "web"
  })
  setUser(response.data.user)
  return response.data
}

async function register({ name, email, password, password_confirmation }) {
  const response = await axios.post("/register", {
    name,
    email,
    password,
    password_confirmation,
    device_name: "web"
  })
  setUser(response.data.user)
  return response.data
}

async function logout() {
  try {
    await axios.post("/logout")
  } finally {
    setUser(null)
  }
}

export function useAuth() {
  return {
    user: computed(() => state.user),
    isAuthenticated: computed(() => !!state.user),
    login,
    register,
    logout
  }
}

