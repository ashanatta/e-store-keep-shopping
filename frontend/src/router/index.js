// src/router/index.js
import { createRouter, createWebHistory } from 'vue-router'

// Import your components
import Home from '../views/Home.vue'
import Men from '@/views/Men.vue'
import Women from '@/views/Women.vue'
import Shop from '@/views/Shop.vue'

// Define routes
const routes = [
  { path: '/', name: 'Home', component: Home },
  { path: '/men', name: 'Men', component: Men },
  { path: '/women', name: 'women', component: Women },
  { path: '/shop', name: 'shop', component: Shop },
]

// Create router
const router = createRouter({
  history: createWebHistory(),
  routes,
})

export default router
