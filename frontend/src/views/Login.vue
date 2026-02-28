<template>
  <div class="container py-5" style="max-width: 420px">
    <h3 class="fw-semibold mb-4 text-center">Login</h3>
    <div class="card p-4 shadow-sm">
      <form @submit.prevent="onSubmit">
        <div v-if="error" class="alert alert-danger py-2">{{ error }}</div>
        <div class="mb-3">
          <label class="form-label">Email</label>
          <input v-model="email" type="email" required class="form-control" placeholder="you@example.com" />
        </div>
        <div class="mb-3">
          <label class="form-label">Password</label>
          <input v-model="password" type="password" required class="form-control" placeholder="••••••••" />
        </div>
        <button class="btn btn-dark w-100" type="submit">Login</button>
      </form>
      <div class="text-center mt-3">
        <span class="text-muted">Don't have an account?</span>
        <router-link to="/register" class="ms-1">Register</router-link>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from "vue"
import { useRouter, useRoute } from "vue-router"
import { useAuth } from "@/composables/useAuth.js"

const email = ref("")
const password = ref("")
const error = ref("")

const router = useRouter()
const route = useRoute()
const { login } = useAuth()

const onSubmit = async () => {
  error.value = ""
  try {
    await login({ email: email.value, password: password.value })
    const redirectTo = route.query.redirect || "/"
    router.push(redirectTo)
  } catch (err) {
    error.value = err?.response?.data?.message || "Login failed"
  }
}
</script>

<style scoped>
.card {
  border: 1px solid #e9ecef;
  border-radius: 16px;
  background: #fff;
}
</style>
