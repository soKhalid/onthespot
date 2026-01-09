import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '../stores/auth'

const routes = [
  {
    path: '/',
    name: 'Home',
    component: () => import('../views/Home.vue')
  },
  {
    path: '/search',
    name: 'Search',
    component: () => import('../views/Search.vue')
  },
  {
    path: '/categories/:slug',
    name: 'Category',
    component: () => import('../views/CategoryPage.vue')
  },
  {
    path: '/brands/:slug',
    name: 'BrandDetail',
    component: () => import('../views/BrandDetail.vue')
  },
  {
    path: '/login',
    name: 'Login',
    component: () => import('../views/Login.vue')
  },
  {
    path: '/register',
    name: 'Register',
    component: () => import('../views/Register.vue')
  },
  {
    path: '/dashboard',
    name: 'Dashboard',
    component: () => import('../views/Dashboard.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/business/brands',
    name: 'MyBrands',
    component: () => import('../views/business/MyBrands.vue'),
    meta: { requiresAuth: true, requiresBusiness: true }
  },
  {
    path: '/business/brands/:id',
    name: 'ManageBrand',
    component: () => import('../views/business/ManageBrand.vue'),
    meta: { requiresAuth: true, requiresBusiness: true }
  },
  {
    path: '/favorites',
    name: 'Favorites',
    component: () => import('../views/Favorites.vue'),
    meta: { requiresAuth: true }
  }
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

router.beforeEach((to, from, next) => {
  const authStore = useAuthStore()

  if (to.meta.requiresAuth && !authStore.isAuthenticated) {
    next({ name: 'Login', query: { redirect: to.fullPath } })
  } else if (to.meta.requiresBusiness && authStore.user?.user_type !== 'business') {
    next({ name: 'Dashboard' })
  } else {
    next()
  }
})

export default router
