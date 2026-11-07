// src/router/index.js
import { createRouter, createWebHistory } from 'vue-router'
import { useAuth } from '@/composables/useAuth.js'

// Define routes
const routes = [
  { path: '/', name: 'Home', component: () => import('@/views/Home.vue') },
  { path: '/men', name: 'Men', component: () => import('@/views/Men.vue') },

  { path: '/women', name: 'Women', component: () => import('@/views/Women.vue') },
  { path: '/kids', name: 'Kids', component: () => import('@/views/Kids.vue') },

  { path: '/shop', name: 'Shop', component: () => import('@/views/Shop.vue') },
  { path: '/product/:id', name: 'ProductDetail', component: () => import('@/views/ProductDetail.vue') },
  { path: '/cart', name: 'Cart', component: () => import('@/views/Cart.vue') },
  

  { path: '/checkout', name: 'Checkout', component: () => import('@/views/Checkout.vue') },
  { path: '/orders', name: 'OrderList', component: () => import('@/views/OrderList.vue'), meta: { requiresAuth: true } },
  { path: '/orders/:id', name: 'OrderDetail', component: () => import('@/views/OrderDetail.vue'), meta: { requiresAuth: true } },
  { path: '/sales', name: 'Sales', component: () => import('@/views/Sales.vue') },

  { path: '/about', name: 'About', component: () => import('@/views/About.vue') },
  { path: '/login', name: 'Login', component: () => import('@/views/Login.vue') },
  
  { path: '/register', name: 'Register', component: () => import('@/views/Register.vue') },
  { path: '/profile', name: 'Profile', component: () => import('@/views/Profile.vue'), meta: { requiresAuth: true } },
  {
    path: '/admin',
    name: 'Admin',
    component: () => import('@/views/admin/AdminLayout.vue'),
    redirect: '/admin/dashboard',
    children: [
      { path: 'dashboard', name: 'AdminDashboard', component: () => import('@/views/admin/AdminDashboard.vue') },
      { path: 'products', name: 'AdminProducts', component: () => import('@/views/admin/ProductList.vue') },
      { path: 'products/create', name: 'AdminProductCreate', component: () => import('@/views/admin/ProductCreate.vue') },
      { path: 'products/:id/edit', name: 'AdminProductEdit', component: () => import('@/views/admin/ProductEdit.vue') },
      { path: 'categories', name: 'AdminCategories', component: () => import('@/views/admin/CategoryList.vue') },
      { path: 'colors', name: 'AdminColors', component: () => import('@/views/admin/ColorList.vue') },
      { path: 'sizes', name: 'AdminSizes', component: () => import('@/views/admin/SizeList.vue') },
      { path: 'reviews', name: 'AdminReviews', component: () => import('@/views/admin/ReviewList.vue') },
      { path: 'wishlists', name: 'AdminWishlists', component: () => import('@/views/admin/WishlistList.vue') },
      { path: 'carts', name: 'AdminCarts', component: () => import('@/views/admin/CartList.vue') },
      { path: 'orders', name: 'AdminOrders', component: () => import('@/views/admin/AdminOrderList.vue') },
      { path: 'banners', name: 'AdminBanners', component: () => import('@/views/admin/BannerList.vue') },
      { path: 'banners/create', name: 'AdminBannerCreate', component: () => import('@/views/admin/BannerCreate.vue') },
      { path: 'banners/:id/edit', name: 'AdminBannerEdit', component: () => import('@/views/admin/BannerEdit.vue') },
      { path: 'chats', name: 'AdminChats', component: () => import('@/views/admin/ChatList.vue') },
    ],
    meta: { requiresAuth: true, requiresAdmin: true }
  }
]

// Create router
const router = createRouter({
  history: createWebHistory(),
  routes,
  scrollBehavior(to, from, savedPosition) {
    if (savedPosition) return savedPosition
    // Keep position when only the query changes (e.g. search / filters)
    if (to.path === from.path) return false
    return { top: 0 }
  },
})

router.beforeEach((to, from, next) => {
  const { isAuthenticated, isAdmin } = useAuth()
  
  if (to.meta.requiresAuth && !isAuthenticated.value) {
    next({ name: 'Login', query: { redirect: to.fullPath } })
  } else if (to.meta.requiresAdmin && !isAdmin.value) {
    next({ name: 'Home' }) 
  } else {
    next()
  }
})

export default router
