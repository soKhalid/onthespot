<template>
  <div class="search-page max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <h1 class="text-3xl font-bold mb-8">Discover Brands</h1>

    <!-- Filters -->
    <div class="card mb-8">
      <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <input
          v-model="filters.search"
          type="text"
          placeholder="Search..."
          class="input"
          @input="searchBrands"
        />

        <select v-model="filters.category" class="input" @change="searchBrands">
          <option value="">All Categories</option>
          <option v-for="cat in categories" :key="cat.id" :value="cat.slug">
            {{ cat.name }}
          </option>
        </select>

        <select v-model="filters.city" class="input" @change="searchBrands">
          <option value="">All Cities</option>
          <option value="Kuwait City">Kuwait City</option>
          <option value="Salmiya">Salmiya</option>
          <option value="Hawally">Hawally</option>
        </select>

        <select v-model="filters.sort_by" class="input" @change="searchBrands">
          <option value="rating">Top Rated</option>
          <option value="created_at">Newest</option>
          <option value="name">Name</option>
        </select>
      </div>
    </div>

    <!-- Results -->
    <div v-if="loading" class="text-center py-12">
      <p class="text-xl text-gray-600">Loading...</p>
    </div>

    <div v-else-if="brands.length === 0" class="text-center py-12">
      <p class="text-xl text-gray-600">No brands found</p>
    </div>

    <div v-else class="grid grid-cols-1 md:grid-cols-3 gap-6">
      <div
        v-for="brand in brands"
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
</template>

<script setup>
import { ref, onMounted, watch } from 'vue'
import { useRoute } from 'vue-router'
import api from '../services/api'

const route = useRoute()
const brands = ref([])
const categories = ref([])
const loading = ref(false)

const filters = ref({
  search: route.query.q || '',
  category: '',
  city: '',
  sort_by: 'rating'
})

onMounted(async () => {
  const response = await api.getCategories()
  categories.value = response.data
  searchBrands()
})

watch(() => route.query.q, (newQuery) => {
  filters.value.search = newQuery || ''
  searchBrands()
})

const searchBrands = async () => {
  loading.value = true
  try {
    const params = {}
    if (filters.value.search) params.search = filters.value.search
    if (filters.value.category) params.category = filters.value.category
    if (filters.value.city) params.city = filters.value.city
    params.sort_by = filters.value.sort_by
    params.sort_order = 'desc'

    const response = await api.getBrands(params)
    brands.value = response.data.data || []
  } catch (error) {
    console.error('Error searching brands:', error)
  } finally {
    loading.value = false
  }
}
</script>
