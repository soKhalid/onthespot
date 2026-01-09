<template>
  <nav class="bg-white shadow-md sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex justify-between h-16">
        <div class="flex items-center">
          <router-link to="/" class="flex items-center">
            <span class="text-2xl font-bold text-primary-600">OnTheSpot</span>
          </router-link>

          <div class="hidden md:ml-10 md:flex md:space-x-8">
            <router-link to="/search" class="text-gray-700 hover:text-primary-600 px-3 py-2">
              Discover
            </router-link>
            <router-link
              v-for="category in categories"
              :key="category.id"
              :to="`/categories/${category.slug}`"
              class="text-gray-700 hover:text-primary-600 px-3 py-2"
            >
              {{ category.name }}
            </router-link>
          </div>
        </div>

        <div class="flex items-center space-x-4">
          <template v-if="authStore.isAuthenticated">
            <router-link
              v-if="authStore.isBusinessAccount"
              to="/business/brands"
              class="text-gray-700 hover:text-primary-600"
            >
              My Brands
            </router-link>
            <router-link to="/favorites" class="text-gray-700 hover:text-primary-600">
              Favorites
            </router-link>
            <router-link to="/dashboard" class="text-gray-700 hover:text-primary-600">
              Dashboard
            </router-link>
            <button @click="handleLogout" class="btn btn-secondary">
              Logout
            </button>
          </template>
          <template v-else>
            <router-link to="/login" class="text-gray-700 hover:text-primary-600">
              Login
            </router-link>
            <router-link to="/register" class="btn btn-primary">
              Sign Up
            </router-link>
          </template>
        </div>
      </div>
    </div>
  </nav>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth'
import api from '../services/api'

const router = useRouter()
const authStore = useAuthStore()
const categories = ref([])

onMounted(async () => {
  if (authStore.isAuthenticated && !authStore.user) {
    await authStore.fetchUser()
  }

  try {
    const response = await api.getCategories()
    categories.value = response.data
  } catch (error) {
    console.error('Error fetching categories:', error)
  }
})

const handleLogout = async () => {
  await authStore.logout()
  router.push('/')
}
</script>
