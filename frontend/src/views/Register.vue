<template>
  <AuthShell
    title="Create account"
    subtitle="Join Doller Shope for faster checkout, order tracking and AI try-on."
    image="https://images.unsplash.com/photo-1469334031218-e382a71b716b?auto=format&fit=crop&w=1200&q=80"
  >
    <form @submit.prevent="onSubmit">
      <div v-if="error" class="alert alert-danger py-2">{{ error }}</div>
      <div class="mb-3">
        <label class="form-label" for="reg-name">Name</label>
        <input id="reg-name" v-model="name" type="text" required autocomplete="name" class="form-control" placeholder="Your name" />
      </div>
      <div class="mb-3">
        <label class="form-label" for="reg-email">Email</label>
        <input id="reg-email" v-model="email" type="email" required autocomplete="email" class="form-control" placeholder="you@example.com" />
      </div>
      <div class="row g-3 mb-4">
        <div class="col-sm-6">
          <label class="form-label" for="reg-password">Password</label>
          <input id="reg-password" v-model="password" type="password" required autocomplete="new-password" class="form-control" placeholder="Create a password" />
        </div>
        <div class="col-sm-6">
          <label class="form-label" for="reg-password2">Confirm password</label>
          <input id="reg-password2" v-model="passwordConfirmation" type="password" required autocomplete="new-password" class="form-control" placeholder="Repeat password" />
        </div>
      </div>
      <button class="btn btn-dark btn-lg w-100" type="submit" :disabled="submitting">
        <span v-if="submitting" class="spinner-border spinner-border-sm me-2"></span>
        Create Account
      </button>
    </form>
    <p class="switch">
      Already have an account?
      <router-link to="/login">Login</router-link>
    </p>
  </AuthShell>
</template>

<script setup>
import { ref } from "vue"
import { useRouter } from "vue-router"
import { useAuth } from "@/composables/useAuth.js"
import AuthShell from "@/components/common/AuthShell.vue"

const name = ref("")
const email = ref("")
const password = ref("")
const passwordConfirmation = ref("")
const error = ref("")
const submitting = ref(false)

const router = useRouter()
const { register } = useAuth()

const onSubmit = async () => {
  error.value = ""
  submitting.value = true
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
  } finally {
    submitting.value = false
  }
}
</script>

<style scoped>
.switch {
  text-align: center;
  margin: 28px 0 0;
  color: var(--ink-soft);
}

.switch a {
  font-weight: 700;
  color: var(--ink);
  margin-left: 4px;
}
</style>
