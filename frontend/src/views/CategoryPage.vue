<template>
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div v-if="category">
      <h1 class="text-4xl font-bold mb-4">{{ category.name }}</h1>
      <p class="text-xl text-gray-600 mb-8">{{ category.description }}</p>

      <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div
          v-for="brand in category.brands"
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
          <p class="text-sm text-gray-700 mb-4 line-clamp-2">{{ brand.description }}</p>

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
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import api from '../services/api'

const route = useRoute()
const category = ref(null)

onMounted(async () => {
  try {
    const response = await api.getCategory(route.params.slug)
    category.value = response.data
  } catch (error) {
    console.error('Error loading category:', error)
  }
})
</script>
