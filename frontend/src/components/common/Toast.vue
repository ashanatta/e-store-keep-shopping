<template>
  <div class="toast-container position-fixed bottom-0 end-0 p-3">
    <TransitionGroup name="toast">
      <div
        v-for="toast in toasts"
        :key="toast.id"
        class="toast show align-items-center border-0 mb-2"
        :class="getToastClass(toast.type)"
        role="alert"
        aria-live="assertive"
        aria-atomic="true"
      >
        <div class="d-flex">
          <div class="toast-body d-flex align-items-center gap-2">
            <i :class="getIconClass(toast.type)"></i>
            {{ toast.message }}
          </div>
          <button
            type="button"
            class="btn-close btn-close-white me-2 m-auto"
            @click="removeToast(toast.id)"
            aria-label="Close"
          ></button>
        </div>
      </div>
    </TransitionGroup>
  </div>
</template>

<script setup>
import { useToast } from "@/composables/useToast.js"

const { toasts, removeToast } = useToast()

const getToastClass = (type) => {
  switch (type) {
    case 'success': return 'text-white bg-dark'
    case 'error': return 'text-white bg-danger'
    case 'warning': return 'text-dark bg-warning'
    case 'info': return 'text-white bg-info'
    default: return 'text-white bg-secondary'
  }
}

const getIconClass = (type) => {
  switch (type) {
    case 'success': return 'bi bi-check-circle-fill'
    case 'error': return 'bi bi-exclamation-octagon-fill'
    case 'warning': return 'bi bi-exclamation-triangle-fill'
    case 'info': return 'bi bi-info-circle-fill'
    default: return 'bi bi-info-circle'
  }
}
</script>

<style scoped>
.toast-container {
  z-index: 9999;
}

.toast {
  border-radius: 12px;
  min-width: 300px;
  box-shadow: 0 8px 16px rgba(0, 0, 0, 0.15);
}

.toast-body {
  padding: 12px 16px;
  font-weight: 500;
}

/* Transitions */
.toast-enter-active,
.toast-leave-active {
  transition: all 0.3s ease;
}

.toast-enter-from {
  opacity: 0;
  transform: translateX(100%);
}

.toast-leave-to {
  opacity: 0;
  transform: translateY(-20px);
}
</style>
