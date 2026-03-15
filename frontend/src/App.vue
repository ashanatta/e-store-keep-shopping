<template>
  <div class="d-flex flex-column min-vh-100">
    <Header />
       <main class="flex-fill">
      <RouterView />
    </main>
      <Footer />
      <ChatWidget v-if="!isAdmin" />
  </div>
</template>

<script setup>
import { useAuth } from '@/composables/useAuth'
import Header from './components/Header.vue'
import Footer from './components/Footer.vue'
import ChatWidget from './components/common/ChatWidget.vue'

const { isAdmin } = useAuth()
</script>
