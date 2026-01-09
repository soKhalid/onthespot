<template>
  <div class="home">
    <!-- Hero Section -->
    <section class="bg-gradient-to-r from-primary-600 to-primary-800 text-white py-20">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center">
          <h1 class="text-4xl md:text-6xl font-bold mb-6">
            Discover Local Brands & Shops
          </h1>
          <p class="text-xl md:text-2xl mb-8 text-primary-100">
            Your trusted portal for fashion, dining, and local services
          </p>

          <div class="max-w-2xl mx-auto">
            <div class="flex gap-2">
              <input
                v-model="searchQuery"
                type="text"
                placeholder="Search for brands, shops, or products..."
                class="flex-1 px-6 py-4 rounded-lg text-gray-900 text-lg"
                @keyup.enter="handleSearch"
              />
              <button @click="handleSearch" class="btn btn-secondary px-8 text-lg">
                Search
              </button>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Categories Section -->
    <section class="py-16">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="text-3xl font-bold text-center mb-12">Browse by Category</h2>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
          <div
            v-for="category in categories"
            :key="category.id"
            class="card hover:shadow-xl transition-shadow cursor-pointer"
            @click="router.push(`/categories/${category.slug}`)"
          >
            <div class="text-center">
              <div class="text-4xl mb-4">{{ category.icon || '🏪' }}</div>
              <h3 class="text-xl font-semibold mb-2">{{ category.name }}</h3>
              <p class="text-gray-600">{{ category.description }}</p>
              <p class="text-primary-600 mt-4">{{ category.brands_count }} brands</p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Featured Brands Section -->
    <section class="bg-gray-100 py-16">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="text-3xl font-bold text-center mb-12">Featured Brands</h2>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
          <div
            v-for="brand in featuredBrands"
            :key="brand.id"
            class="card hover:shadow-xl transition-shadow cursor-pointer"
            @click="router.push(`/brands/${brand.slug}`)"
          >
            <div v-if="brand.logo" class="mb-4">
              <img :src="brand.logo" :alt="brand.name" class="w-full h-32 object-cover rounded" />
            </div>
            <h3 class="font-semibold text-lg">{{ brand.name }}</h3>
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
    </section>

    <!-- Call to Action -->
    <section class="py-16">
      <div class="max-w-4xl mx-auto px-4 text-center">
        <h2 class="text-3xl font-bold mb-6">Are you a business owner?</h2>
        <p class="text-xl text-gray-600 mb-8">
          Join OnTheSpot and get a professional online presence with easy inventory management
        </p>
        <router-link to="/register" class="btn btn-primary px-8 py-3 text-lg">
          List Your Business
        </router-link>
      </div>
    </section>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import api from '../services/api'

const router = useRouter()
const searchQuery = ref('')
const categories = ref([])
const featuredBrands = ref([])

onMounted(async () => {
  try {
    const [categoriesRes, brandsRes] = await Promise.all([
      api.getCategories(),
      api.getBrands({ sort_by: 'rating', sort_order: 'desc' })
    ])

    categories.value = categoriesRes.data
    featuredBrands.value = brandsRes.data.data?.slice(0, 8) || []
  } catch (error) {
    console.error('Error loading data:', error)
  }
})

const handleSearch = () => {
  if (searchQuery.value.trim()) {
    router.push({ name: 'Search', query: { q: searchQuery.value } })
  }
}
</script>
