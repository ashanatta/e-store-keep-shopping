// src/router/index.js
import { createRouter, createWebHistory } from 'vue-router'

// Import your components
import Home from '../views/Home.vue'
import Men from '@/views/Men.vue'
import Women from '@/views/Women.vue'
import Kids from '@/views/Kids.vue'
import Shop from '@/views/Shop.vue'
import ProductDetail from '@/views/ProductDetail.vue'
import Cart from '@/views/Cart.vue'
import Checkout from '@/views/Checkout.vue'
import Sales from '@/views/Sales.vue'
import About from '@/views/About.vue'
import Login from '@/views/Login.vue'
import Register from '@/views/Register.vue'

// Define routes
const routes = [
  { path: '/', name: 'Home', component: Home },
  { path: '/men', name: 'Men', component: Men },
  { path: '/women', name: 'Women', component: Women },
  { path: '/kids', name: 'Kids', component: Kids },
  { path: '/shop', name: 'Shop', component: Shop },
  { path: '/product/:id', name: 'ProductDetail', component: ProductDetail },
  { path: '/cart', name: 'Cart', component: Cart },
  { path: '/checkout', name: 'Checkout', component: Checkout },
  { path: '/sales', name: 'Sales', component: Sales },
  { path: '/about', name: 'About', component: About },
  { path: '/login', name: 'Login', component: Login },
  { path: '/register', name: 'Register', component: Register },
]

// Create router
const router = createRouter({
  history: createWebHistory(),
  routes,
})

export default router
