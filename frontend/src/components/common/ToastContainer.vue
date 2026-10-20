<template>
  <div class="toast-stack" aria-live="polite">
    <TransitionGroup name="toast">
      <div
        v-for="toast in toasts"
        :key="toast.id"
        class="toast-item"
        :class="`toast-${toast.type}`"
        role="status"
      >
        <span class="toast-icon"><i :class="icons[toast.type] || icons.info"></i></span>
        <span class="toast-msg">{{ toast.message }}</span>
        <button type="button" class="toast-close" aria-label="Dismiss" @click="removeToast(toast.id)">
          <i class="bi bi-x-lg"></i>
        </button>
      </div>
    </TransitionGroup>
  </div>
</template>

<script setup>
import { useToast } from "@/composables/useToast.js"

const { toasts, removeToast } = useToast()

const icons = {
  success: "bi bi-check-lg",
  error: "bi bi-exclamation-lg",
  warning: "bi bi-exclamation-triangle",
  info: "bi bi-info-lg",
}
</script>

<style scoped>
.toast-stack {
  position: fixed;
  top: 20px;
  left: 50%;
  transform: translateX(-50%);
  z-index: 2000;
  display: flex;
  flex-direction: column;
  gap: 10px;
  width: min(420px, calc(100vw - 32px));
  pointer-events: none;
}

.toast-item {
  pointer-events: auto;
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 12px 12px 12px 14px;
  border-radius: 16px;
  background: var(--ink);
  color: #fff;
  box-shadow: var(--shadow-lg);
  font-size: 14px;
  font-weight: 500;
}

.toast-icon {
  width: 28px;
  height: 28px;
  flex-shrink: 0;
  border-radius: 50%;
  display: grid;
  place-items: center;
  font-size: 14px;
  background: rgba(255, 255, 255, 0.12);
}

.toast-success .toast-icon { background: #1fae7a; }
.toast-error .toast-icon { background: var(--danger); }
.toast-warning .toast-icon { background: #f59e0b; color: var(--ink); }
.toast-info .toast-icon { background: #3b82f6; }

.toast-msg {
  flex: 1;
  line-height: 1.4;
}

.toast-close {
  border: 0;
  background: transparent;
  color: rgba(255, 255, 255, 0.6);
  width: 28px;
  height: 28px;
  border-radius: 50%;
  display: grid;
  place-items: center;
}

.toast-close:hover {
  background: rgba(255, 255, 255, 0.1);
  color: #fff;
}

.toast-enter-active,
.toast-leave-active {
  transition: all 0.4s var(--ease);
}

.toast-enter-from {
  opacity: 0;
  transform: translateY(-16px) scale(0.96);
}

.toast-leave-to {
  opacity: 0;
  transform: scale(0.96);
}
</style>
