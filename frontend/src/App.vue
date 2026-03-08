<template>
  <div class="d-flex flex-column min-vh-100">
    <Header />
    <main class="flex-fill">
      <RouterView />
    </main>
    <Footer />
    <Toast />
  </div>
</template>

<script setup>
import Header from './components/Header.vue'
import Footer from './components/Footer.vue'
import Toast from './components/common/Toast.vue'
</script>
