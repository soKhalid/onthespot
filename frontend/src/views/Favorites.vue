<template>
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <h1 class="text-3xl font-bold mb-8">My Favorites</h1>

    <div v-if="loading" class="text-center py-12">
      <p class="text-xl text-gray-600">Loading...</p>
    </div>

    <div v-else-if="favorites.length === 0" class="text-center py-12">
      <p class="text-xl text-gray-600">No favorites yet</p>
      <router-link to="/search" class="btn btn-primary mt-4">
        Discover Brands
      </router-link>
    </div>

    <div v-else class="grid grid-cols-1 md:grid-cols-3 gap-6">
      <div
        v-for="brand in favorites"
        :key="brand.id"
        class="card hover:shadow-xl transition cursor-pointer"
        @click="$router.push(`/brands/${brand.slug}`)"
      >
        <img
          v-if="brand.logo"
          :src="brand.logo"
          :alt="brand.name"
          class="w-full h-48 object-cover rounded-lg mb-4"
        />

        <h3 class="text-xl font-semibold mb-2">{{ brand.name }}</h3>
        <p class="text-sm text-gray-600 mb-2">{{ brand.category?.name }}</p>

        <div class="flex items-center justify-between">
          <div class="flex items-center">
            <span class="text-yellow-500">★</span>
            <span class="ml-1">{{ brand.rating || 'New' }}</span>
          </div>
          <span class="text-sm text-gray-500">{{ brand.city }}</span>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import api from '../services/api'

const favorites = ref([])
const loading = ref(false)

onMounted(async () => {
  loading.value = true
  try {
    const response = await api.getFavorites()
    favorites.value = response.data
  } catch (error) {
    console.error('Error loading favorites:', error)
  } finally {
    loading.value = false
  }
})
</script>
