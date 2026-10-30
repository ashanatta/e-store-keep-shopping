<template>
  <div class="auth">
    <aside class="auth-visual">
      <img :src="image" alt="" />
      <div class="auth-visual-copy">
        <span class="eyebrow eyebrow-light">Doller Shope</span>
        <p class="quote">
          “Style is a way to say who you are <em>without having to speak.</em>”
        </p>
        <ul>
          <li><i class="bi bi-stars"></i> AI virtual try-on</li>
          <li><i class="bi bi-truck"></i> Free shipping over $100</li>
          <li><i class="bi bi-arrow-repeat"></i> 30-day free returns</li>
        </ul>
      </div>
    </aside>

    <section class="auth-panel">
      <div class="auth-box">
        <h1 class="auth-title">{{ title }}</h1>
        <p class="auth-sub">{{ subtitle }}</p>
        <slot />
      </div>
    </section>
  </div>
</template>

<script setup>
defineProps({
  title: { type: String, required: true },
  subtitle: { type: String, default: "" },
  image: {
    type: String,
    default: "https://images.unsplash.com/photo-1483985988355-763728e1935b?auto=format&fit=crop&w=1200&q=80",
  },
})
</script>

<style scoped>
.auth {
  display: grid;
  min-height: calc(100vh - var(--header-h) - 36px);
}

@media (min-width: 992px) {
  .auth {
    grid-template-columns: 1fr 1fr;
  }
}

.auth-visual {
  position: relative;
  display: none;
  overflow: hidden;
  background: var(--ink);
}

@media (min-width: 992px) {
  .auth-visual {
    display: block;
    margin: 16px 0 16px 16px;
    border-radius: var(--radius-xl);
  }
}

.auth-visual img {
  position: absolute;
  inset: 0;
  width: 100%;
  height: 100%;
  object-fit: cover;
  opacity: 0.85;
}

.auth-visual::after {
  content: "";
  position: absolute;
  inset: 0;
  background: linear-gradient(to top, rgba(14, 14, 16, 0.85), rgba(14, 14, 16, 0.1) 60%);
}

.auth-visual-copy {
  position: absolute;
  left: 48px;
  right: 48px;
  bottom: 48px;
  z-index: 1;
  color: #fff;
}

.eyebrow-light {
  color: rgba(255, 255, 255, 0.7);
}

.quote {
  font-size: clamp(26px, 2.6vw, 38px);
  line-height: 1.15;
  font-weight: 700;
  letter-spacing: -0.02em;
  margin: 16px 0 28px;
}

.quote em {
  font-family: var(--font-display);
  font-weight: 400;
}

.auth-visual-copy ul {
  list-style: none;
  padding: 0;
  margin: 0;
  display: flex;
  flex-wrap: wrap;
  gap: 8px;
}

.auth-visual-copy li {
  padding: 8px 14px;
  border-radius: 999px;
  background: rgba(255, 255, 255, 0.14);
  backdrop-filter: blur(10px);
  font-size: 13px;
  font-weight: 600;
}

.auth-visual-copy li i {
  color: var(--accent);
  margin-right: 6px;
}

.auth-panel {
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 48px 20px;
}

.auth-box {
  width: 100%;
  max-width: 420px;
  animation: authIn 0.8s var(--ease) both;
}

@keyframes authIn {
  from {
    opacity: 0;
    transform: translateY(16px);
  }
}

.auth-title {
  font-size: clamp(32px, 3.4vw, 44px);
  font-weight: 800;
  letter-spacing: -0.04em;
  margin-bottom: 8px;
}

.auth-sub {
  color: var(--ink-soft);
  margin-bottom: 32px;
}
</style>
