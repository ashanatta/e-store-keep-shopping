import { reactive, computed } from "vue"
import axios from "axios"

axios.defaults.baseURL = import.meta.env.VITE_API_BASE_URL

const state = reactive({
  user: JSON.parse(localStorage.getItem("auth:user") || "null"),
  token: localStorage.getItem("auth:token") || "",
  isAdmin: JSON.parse(localStorage.getItem("auth:isAdmin") || "false")
})

function setAuthHeader(token) {
  if (token) {
    axios.defaults.headers.common.Authorization = `Bearer ${token}`
  } else {
    delete axios.defaults.headers.common.Authorization
  }
}

function setToken(token) {
  state.token = token
  if (token) {
    localStorage.setItem("auth:token", token)
  } else {
    localStorage.removeItem("auth:token")
  }
  setAuthHeader(token)
}

setAuthHeader(state.token)

function setUser(user) {
  state.user = user
  state.isAdmin = user ? user.is_admin : false
  if (user) {
    localStorage.setItem("auth:user", JSON.stringify(user))
    localStorage.setItem("auth:isAdmin", JSON.stringify(user.is_admin))
  } else {
    localStorage.removeItem("auth:user")
    localStorage.removeItem("auth:isAdmin")
  }
}

async function login({ email, password }) {
  const response = await axios.post("/login", {
    email,
    password,
    device_name: "web"
  })
  setUser(response.data.user)
  setToken(response.data.token)
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
  setToken(response.data.token)
  return response.data
}

async function logout() {
  try {
    await axios.post("/logout")
  } finally {
    setUser(null)
    setToken("")
  }
}

export function useAuth() {
  return {
    user: computed(() => state.user),
    token: computed(() => state.token),
    isAuthenticated: computed(() => !!state.token),
    isAdmin: computed(() => state.isAdmin),
    login,
    register,
    logout
  }
}

