import { reactive, readonly } from 'vue'

const state = reactive({
  toasts: []
})

export function useToast() {
  const addToast = (message, type = 'success', duration = 3000) => {
    const id = Date.now() + Math.random()
    const toast = { id, message, type }
    
    state.toasts.push(toast)

    if (duration > 0) {
      setTimeout(() => {
        removeToast(id)
      }, duration)
    }
  }

  const removeToast = (id) => {
    const index = state.toasts.findIndex(t => t.id === id)
    if (index !== -1) {
      state.toasts.splice(index, 1)
    }
  }

  return {
    toasts: readonly(state.toasts),
    success: (msg, dur) => addToast(msg, 'success', dur),
    error: (msg, dur) => addToast(msg, 'error', dur),
    info: (msg, dur) => addToast(msg, 'info', dur),
    warning: (msg, dur) => addToast(msg, 'warning', dur),
    removeToast
  }
}
