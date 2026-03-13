// src/router/index.js
import { createRouter, createWebHistory } from 'vue-router'
import { useAuth } from '@/composables/useAuth.js'

// Import your components
import Home from '../views/Home.vue'
import Men from '@/views/Men.vue'
import Women from '@/views/Women.vue'
import Kids from '@/views/Kids.vue'
import Shop from '@/views/Shop.vue'
import ProductDetail from '@/views/ProductDetail.vue'
import Cart from '@/views/Cart.vue'
import Checkout from '@/views/Checkout.vue'
import OrderList from '@/views/OrderList.vue'
import OrderDetail from '@/views/OrderDetail.vue'
import Sales from '@/views/Sales.vue'
import About from '@/views/About.vue'
import Login from '@/views/Login.vue'
import Register from '@/views/Register.vue'
import Profile from '@/views/Profile.vue'
import AdminLayout from '@/views/admin/AdminLayout.vue'
import AdminDashboard from '@/views/admin/AdminDashboard.vue'
import ProductList from '@/views/admin/ProductList.vue'
import ProductCreate from '@/views/admin/ProductCreate.vue'
import ProductEdit from '@/views/admin/ProductEdit.vue'
import CategoryList from '@/views/admin/CategoryList.vue'
import ColorList from '@/views/admin/ColorList.vue'
import SizeList from '@/views/admin/SizeList.vue'
import ReviewList from '@/views/admin/ReviewList.vue'
import WishlistList from '@/views/admin/WishlistList.vue'
import CartList from '@/views/admin/CartList.vue'
import AdminOrderList from '@/views/admin/AdminOrderList.vue'
import BannerList from '@/views/admin/BannerList.vue'
import BannerCreate from '@/views/admin/BannerCreate.vue'
import BannerEdit from '@/views/admin/BannerEdit.vue'

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
  { path: '/orders', name: 'OrderList', component: OrderList, meta: { requiresAuth: true } },
  { path: '/orders/:id', name: 'OrderDetail', component: OrderDetail, meta: { requiresAuth: true } },
  { path: '/sales', name: 'Sales', component: Sales },
  { path: '/about', name: 'About', component: About },
  { path: '/login', name: 'Login', component: Login },
  { path: '/register', name: 'Register', component: Register },
  { path: '/profile', name: 'Profile', component: Profile, meta: { requiresAuth: true } },
  {
    path: '/admin',
    name: 'Admin',
    component: AdminLayout,
    redirect: '/admin/dashboard',
    children: [
      { path: 'dashboard', name: 'AdminDashboard', component: AdminDashboard },
      { path: 'products', name: 'AdminProducts', component: ProductList },
      { path: 'products/create', name: 'AdminProductCreate', component: ProductCreate },
      { path: 'products/:id/edit', name: 'AdminProductEdit', component: ProductEdit },
      { path: 'categories', name: 'AdminCategories', component: CategoryList },
      { path: 'colors', name: 'AdminColors', component: ColorList },
      { path: 'sizes', name: 'AdminSizes', component: SizeList },
      { path: 'reviews', name: 'AdminReviews', component: ReviewList },
      { path: 'wishlists', name: 'AdminWishlists', component: WishlistList },
      { path: 'carts', name: 'AdminCarts', component: CartList },
      { path: 'orders', name: 'AdminOrders', component: AdminOrderList },
      { path: 'banners', name: 'AdminBanners', component: BannerList },
      { path: 'banners/create', name: 'AdminBannerCreate', component: BannerCreate },
      { path: 'banners/:id/edit', name: 'AdminBannerEdit', component: BannerEdit },
    ],
    meta: { requiresAuth: true, requiresAdmin: true }
  }
]

// Create router
const router = createRouter({
  history: createWebHistory(),
  routes,
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
