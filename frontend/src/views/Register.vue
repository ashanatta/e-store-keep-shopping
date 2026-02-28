<template>
  <div class="container py-5" style="max-width: 420px">
    <h3 class="fw-semibold mb-4 text-center">Register</h3>
    <div class="card p-4 shadow-sm">
      <form @submit.prevent="onSubmit">
        <div v-if="error" class="alert alert-danger py-2">{{ error }}</div>
        <div class="mb-3">
          <label class="form-label">Name</label>
          <input v-model="name" type="text" required class="form-control" placeholder="Your name" />
        </div>
        <div class="mb-3">
          <label class="form-label">Email</label>
          <input v-model="email" type="email" required class="form-control" placeholder="you@example.com" />
        </div>
        <div class="mb-3">
          <label class="form-label">Password</label>
          <input v-model="password" type="password" required class="form-control" placeholder="Create a password" />
        </div>
        <div class="mb-3">
          <label class="form-label">Confirm Password</label>
          <input v-model="passwordConfirmation" type="password" required class="form-control" placeholder="Confirm password" />
        </div>
        <button class="btn btn-dark w-100" type="submit">Create Account</button>
      </form>
      <div class="text-center mt-3">
        <span class="text-muted">Already have an account?</span>
        <router-link to="/login" class="ms-1">Login</router-link>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from "vue"
import { useRouter } from "vue-router"
import { useAuth } from "@/composables/useAuth.js"

const name = ref("")
const email = ref("")
const password = ref("")
const passwordConfirmation = ref("")
const error = ref("")

const router = useRouter()
const { register } = useAuth()

const onSubmit = async () => {
  error.value = ""
  try {
    await register({
      name: name.value,
      email: email.value,
      password: password.value,
      password_confirmation: passwordConfirmation.value
    })
    router.push("/")
  } catch (err) {
    error.value = err?.response?.data?.message || "Registration failed"
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
