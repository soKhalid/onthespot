<template>
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex justify-between items-center mb-8">
      <h1 class="text-3xl font-bold">My Brands</h1>
      <button @click="showCreateForm = true" class="btn btn-primary">
        + Add New Brand
      </button>
    </div>

    <!-- Create Brand Modal -->
    <div v-if="showCreateForm" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div class="card max-w-2xl w-full max-h-[90vh] overflow-y-auto">
        <h2 class="text-2xl font-bold mb-6">Create New Brand</h2>

        <form @submit.prevent="createBrand" class="space-y-4">
          <div>
            <label class="block text-sm font-medium mb-2">Category</label>
            <select v-model="newBrand.category_id" class="input" required>
              <option value="">Select Category</option>
              <option v-for="cat in categories" :key="cat.id" :value="cat.id">
                {{ cat.name }}
              </option>
            </select>
          </div>

          <div>
            <label class="block text-sm font-medium mb-2">Brand Name</label>
            <input v-model="newBrand.name" type="text" required class="input" />
          </div>

          <div>
            <label class="block text-sm font-medium mb-2">Description</label>
            <textarea v-model="newBrand.description" rows="3" class="input"></textarea>
          </div>

          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium mb-2">Address</label>
              <input v-model="newBrand.address" type="text" required class="input" />
            </div>

            <div>
              <label class="block text-sm font-medium mb-2">City</label>
              <input v-model="newBrand.city" type="text" required class="input" />
            </div>
          </div>

          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium mb-2">Phone</label>
              <input v-model="newBrand.phone" type="tel" class="input" />
            </div>

            <div>
              <label class="block text-sm font-medium mb-2">Email</label>
              <input v-model="newBrand.email" type="email" class="input" />
            </div>
          </div>

          <div v-if="error" class="p-3 bg-red-100 text-red-700 rounded text-sm">
            {{ error }}
          </div>

          <div class="flex gap-4">
            <button type="submit" class="btn btn-primary" :disabled="loading">
              {{ loading ? 'Creating...' : 'Create Brand' }}
            </button>
            <button type="button" @click="showCreateForm = false" class="btn btn-secondary">
              Cancel
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Brands List -->
    <div v-if="brands.length === 0" class="text-center py-12">
      <p class="text-xl text-gray-600 mb-4">You haven't created any brands yet</p>
      <button @click="showCreateForm = true" class="btn btn-primary">
        Create Your First Brand
      </button>
    </div>

    <div v-else class="grid grid-cols-1 md:grid-cols-2 gap-6">
      <div v-for="brand in brands" :key="brand.id" class="card hover:shadow-xl transition">
        <div class="flex justify-between items-start mb-4">
          <div>
            <h3 class="text-xl font-bold">{{ brand.name }}</h3>
            <p class="text-sm text-gray-600">{{ brand.category?.name }}</p>
          </div>

          <span
            class="px-2 py-1 text-xs rounded"
            :class="brand.is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'"
          >
            {{ brand.is_active ? 'Active' : 'Inactive' }}
          </span>
        </div>

        <p class="text-gray-700 mb-4 line-clamp-2">{{ brand.description }}</p>

        <div class="flex items-center justify-between text-sm text-gray-600 mb-4">
          <div>
            <span class="text-yellow-500">★</span>
            {{ brand.rating || 'N/A' }} ({{ brand.review_count }} reviews)
          </div>
          <div>📍 {{ brand.city }}</div>
        </div>

        <div class="flex gap-2">
          <router-link :to="`/business/brands/${brand.id}`" class="btn btn-primary flex-1">
            Manage
          </router-link>
          <router-link :to="`/brands/${brand.slug}`" class="btn btn-secondary">
            View Public Page
          </router-link>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import api from '../../services/api'

const router = useRouter()
const brands = ref([])
const categories = ref([])
const showCreateForm = ref(false)
const loading = ref(false)
const error = ref('')

const newBrand = ref({
  category_id: '',
  name: '',
  description: '',
  address: '',
  city: 'Kuwait City',
  phone: '',
  email: ''
})

onMounted(async () => {
  try {
    const [brandsRes, categoriesRes] = await Promise.all([
      api.getMyBrands(),
      api.getCategories()
    ])

    brands.value = brandsRes.data
    categories.value = categoriesRes.data
  } catch (error) {
    console.error('Error loading data:', error)
  }
})

const createBrand = async () => {
  loading.value = true
  error.value = ''

  try {
    await api.createBrand(newBrand.value)
    showCreateForm.value = false

    // Reload brands
    const response = await api.getMyBrands()
    brands.value = response.data

    // Reset form
    newBrand.value = {
      category_id: '',
      name: '',
      description: '',
      address: '',
      city: 'Kuwait City',
      phone: '',
      email: ''
    }
  } catch (err) {
    error.value = err.response?.data?.message || 'Failed to create brand'
  } finally {
    loading.value = false
  }
}
</script>
