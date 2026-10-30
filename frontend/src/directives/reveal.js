// v-reveal: fades/slides an element in when it scrolls into view.
// Usage: <div v-reveal> or <div v-reveal="150"> (delay in ms)
let observer = null

const getObserver = () => {
  if (observer || typeof IntersectionObserver === "undefined") return observer
  observer = new IntersectionObserver(
    (entries) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          entry.target.classList.add("is-visible")
          observer.unobserve(entry.target)
        }
      })
    },
    { rootMargin: "0px 0px -8% 0px", threshold: 0.08 }
  )
  return observer
}

export default {
  mounted(el, binding) {
    const io = getObserver()
    if (!io) return
    el.setAttribute("data-reveal", "")
    if (binding.value) el.style.setProperty("--reveal-delay", `${binding.value}ms`)
    io.observe(el)
  },
  unmounted(el) {
    observer?.unobserve(el)
  },
}
