<template>
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <h1 class="text-3xl font-bold mb-8">Dashboard</h1>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
      <div class="card text-center">
        <div class="text-4xl mb-2">❤️</div>
        <div class="text-3xl font-bold text-primary-600">{{ stats.favorites }}</div>
        <div class="text-gray-600">Favorites</div>
      </div>

      <div class="card text-center">
        <div class="text-4xl mb-2">⭐</div>
        <div class="text-3xl font-bold text-primary-600">{{ stats.reviews }}</div>
        <div class="text-gray-600">Reviews</div>
      </div>

      <div v-if="authStore.isBusinessAccount" class="card text-center">
        <div class="text-4xl mb-2">🏪</div>
        <div class="text-3xl font-bold text-primary-600">{{ stats.brands }}</div>
        <div class="text-gray-600">My Brands</div>
      </div>
    </div>

    <div class="card">
      <h2 class="text-2xl font-bold mb-4">Account Information</h2>

      <div class="space-y-3">
        <div>
          <span class="font-medium">Name:</span>
          <span class="ml-2">{{ authStore.user?.name }}</span>
        </div>
        <div>
          <span class="font-medium">Email:</span>
          <span class="ml-2">{{ authStore.user?.email }}</span>
        </div>
        <div>
          <span class="font-medium">Account Type:</span>
          <span class="ml-2 capitalize">{{ authStore.user?.user_type }}</span>
        </div>
      </div>

      <div v-if="authStore.isBusinessAccount" class="mt-6">
        <router-link to="/business/brands" class="btn btn-primary">
          Manage My Brands
        </router-link>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useAuthStore } from '../stores/auth'
import api from '../services/api'

const authStore = useAuthStore()

const stats = ref({
  favorites: 0,
  reviews: 0,
  brands: 0
})

onMounted(async () => {
  try {
    const favorites = await api.getFavorites()
    stats.value.favorites = favorites.data.length

    if (authStore.isBusinessAccount) {
      const brands = await api.getMyBrands()
      stats.value.brands = brands.data.length
    }
  } catch (error) {
    console.error('Error loading dashboard data:', error)
  }
})
</script>
