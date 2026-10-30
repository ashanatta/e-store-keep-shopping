<template>
  <AuthShell title="Welcome back" subtitle="Login to track orders, save favourites and check out faster.">
    <form @submit.prevent="onSubmit">
      <div v-if="error" class="alert alert-danger py-2">{{ error }}</div>
      <div class="mb-3">
        <label class="form-label" for="login-email">Email</label>
        <input id="login-email" v-model="email" type="email" required autocomplete="email" class="form-control" placeholder="you@example.com" />
      </div>
      <div class="mb-4">
        <label class="form-label" for="login-password">Password</label>
        <div class="pw-field">
          <input
            id="login-password"
            v-model="password"
            :type="showPassword ? 'text' : 'password'"
            required
            autocomplete="current-password"
            class="form-control"
            placeholder="••••••••"
          />
          <button type="button" class="pw-toggle" :aria-label="showPassword ? 'Hide password' : 'Show password'" @click="showPassword = !showPassword">
            <i :class="showPassword ? 'bi bi-eye-slash' : 'bi bi-eye'"></i>
          </button>
        </div>
      </div>
      <button class="btn btn-dark btn-lg w-100" type="submit" :disabled="submitting">
        <span v-if="submitting" class="spinner-border spinner-border-sm me-2"></span>
        Login
      </button>
    </form>

    <div class="divider">
      <span>or continue with</span>
    </div>

    <div id="google-signin-btn" class="d-flex justify-content-center"></div>

    <p class="switch">
      Don't have an account?
      <router-link to="/register">Create one</router-link>
    </p>
  </AuthShell>
</template>

<script setup>
import { ref, onMounted } from "vue"
import { useRouter, useRoute } from "vue-router"
import { useAuth } from "@/composables/useAuth.js"
import AuthShell from "@/components/common/AuthShell.vue"

const email = ref("")
const password = ref("")
const error = ref("")
const submitting = ref(false)
const showPassword = ref(false)

const router = useRouter()
const route = useRoute()
const { login, loginWithGoogle } = useAuth()

// Only follow same-site paths from ?redirect=
const redirectTarget = () => {
  const target = route.query.redirect
  return typeof target === "string" && target.startsWith("/") && !target.startsWith("//") ? target : "/"
}

onMounted(() => {
  /* global google */
  if (typeof google !== 'undefined') {
    google.accounts.id.initialize({
      client_id: import.meta.env.VITE_GOOGLE_CLIENT_ID,
      callback: handleGoogleResponse
    });
    google.accounts.id.renderButton(
      document.getElementById("google-signin-btn"),
      { theme: "outline", size: "large", shape: "pill", width: 360 }
    );
  }
})

const handleGoogleResponse = async (response) => {
  error.value = ""
  try {
    await loginWithGoogle(response.credential)
    router.push(redirectTarget())
  } catch (err) {
    error.value = err?.response?.data?.message || "Google Login failed"
  }
}

const onSubmit = async () => {
  error.value = ""
  submitting.value = true
  try {
    await login({ email: email.value, password: password.value })
    router.push(redirectTarget())
  } catch (err) {
    error.value = err?.response?.data?.message || "Login failed"
  } finally {
    submitting.value = false
  }
}
</script>

<style scoped>
.pw-field {
  position: relative;
}

.pw-field .form-control {
  padding-right: 48px;
}

.pw-toggle {
  position: absolute;
  top: 50%;
  right: 6px;
  transform: translateY(-50%);
  width: 38px;
  height: 38px;
  border: 0;
  border-radius: 50%;
  background: transparent;
  color: var(--muted);
}

.pw-toggle:hover {
  color: var(--ink);
  background: var(--ivory);
}

.divider {
  display: flex;
  align-items: center;
  gap: 14px;
  margin: 28px 0;
  color: var(--muted);
  font-size: 13px;
}

.divider::before,
.divider::after {
  content: "";
  flex: 1;
  height: 1px;
  background: var(--line);
}

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
