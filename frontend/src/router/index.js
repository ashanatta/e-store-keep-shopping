// src/router/index.js
import { createRouter, createWebHistory } from 'vue-router'

// Import your components
import Home from '../views/Home.vue'

// Define routes
const routes = [
  { path: '/', name: 'Home', component: Home },
]

// Create router
const router = createRouter({
  history: createWebHistory(),
  routes,
})

export default router
