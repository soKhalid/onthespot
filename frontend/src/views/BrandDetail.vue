<template>
  <div v-if="brand" class="brand-detail">
    <!-- Brand Header -->
    <div class="bg-white shadow-md">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="flex items-start gap-6">
          <img
            v-if="brand.logo"
            :src="brand.logo"
            :alt="brand.name"
            class="w-32 h-32 object-cover rounded-lg shadow"
          />

          <div class="flex-1">
            <div class="flex items-center justify-between">
              <div>
                <h1 class="text-4xl font-bold mb-2">{{ brand.name }}</h1>
                <p class="text-gray-600 mb-4">{{ brand.category?.name }}</p>
              </div>

              <button
                v-if="authStore.isAuthenticated"
                @click="toggleFavorite"
                class="btn"
                :class="isFavorite ? 'btn-primary' : 'btn-secondary'"
              >
                {{ isFavorite ? '★ Favorited' : '☆ Add to Favorites' }}
              </button>
            </div>

            <div class="flex items-center gap-6 mb-4">
              <div class="flex items-center">
                <span class="text-yellow-500 text-2xl">★</span>
                <span class="ml-2 text-xl font-semibold">{{ brand.rating || 'N/A' }}</span>
                <span class="ml-1 text-gray-500">({{ brand.review_count }} reviews)</span>
              </div>

              <div class="text-gray-600">
                📍 {{ brand.city }}, {{ brand.country }}
              </div>
            </div>

            <p class="text-gray-700">{{ brand.description }}</p>

            <!-- Delivery Services (for restaurants) -->
            <div v-if="brand.category?.slug === 'restaurant' && (brand.talabat_url || brand.deliveroo_url)" class="mt-4 flex gap-4">
              <a
                v-if="brand.talabat_url"
                :href="brand.talabat_url"
                target="_blank"
                class="btn btn-secondary"
              >
                <img src="/talabat-icon.png" alt="Talabat" class="inline h-5 w-5 mr-2" />
                Order on Talabat
              </a>
              <a
                v-if="brand.deliveroo_url"
                :href="brand.deliveroo_url"
                target="_blank"
                class="btn btn-secondary"
              >
                <img src="/deliveroo-icon.png" alt="Deliveroo" class="inline h-5 w-5 mr-2" />
                Order on Deliveroo
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Content Sections -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Main Content -->
        <div class="lg:col-span-2">
          <!-- Inventory/Menu Items -->
          <section class="card mb-8">
            <h2 class="text-2xl font-bold mb-6">
              {{ brand.category?.slug === 'restaurant' ? 'Menu' : 'Products' }}
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div
                v-for="item in inventoryItems"
                :key="item.id"
                class="border rounded-lg p-4 hover:shadow-md transition"
              >
                <div v-if="item.images?.length" class="mb-3">
                  <img :src="item.images[0]" :alt="item.name" class="w-full h-40 object-cover rounded" />
                </div>

                <h3 class="font-semibold text-lg mb-1">{{ item.name }}</h3>
                <p class="text-sm text-gray-600 mb-2">{{ item.description }}</p>

                <div class="flex items-center justify-between">
                  <div>
                    <span class="text-lg font-bold text-primary-600">
                      {{ item.sale_price || item.price }} KD
                    </span>
                    <span v-if="item.sale_price" class="text-sm text-gray-500 line-through ml-2">
                      {{ item.price }} KD
                    </span>
                  </div>

                  <span
                    v-if="item.is_available"
                    class="text-xs px-2 py-1 bg-green-100 text-green-800 rounded"
                  >
                    Available
                  </span>
                  <span
                    v-else
                    class="text-xs px-2 py-1 bg-red-100 text-red-800 rounded"
                  >
                    Unavailable
                  </span>
                </div>
              </div>
            </div>
          </section>

          <!-- Reviews Section -->
          <section class="card">
            <h2 class="text-2xl font-bold mb-6">Customer Reviews</h2>

            <div v-if="authStore.isAuthenticated && !userHasReviewed" class="mb-6 p-4 bg-gray-50 rounded-lg">
              <h3 class="font-semibold mb-3">Write a Review</h3>
              <div class="mb-3">
                <div class="flex gap-2">
                  <button
                    v-for="star in 5"
                    :key="star"
                    @click="newReview.rating = star"
                    class="text-2xl"
                    :class="star <= newReview.rating ? 'text-yellow-500' : 'text-gray-300'"
                  >
                    ★
                  </button>
                </div>
              </div>
              <textarea
                v-model="newReview.comment"
                placeholder="Share your experience..."
                class="input mb-3"
                rows="3"
              ></textarea>
              <button @click="submitReview" class="btn btn-primary">
                Submit Review
              </button>
            </div>

            <div class="space-y-4">
              <div v-for="review in reviews" :key="review.id" class="border-b pb-4">
                <div class="flex items-center justify-between mb-2">
                  <div>
                    <span class="font-semibold">{{ review.user?.name }}</span>
                    <span class="text-yellow-500 ml-2">
                      {{ '★'.repeat(review.rating) }}{{ '☆'.repeat(5 - review.rating) }}
                    </span>
                  </div>
                  <span class="text-sm text-gray-500">
                    {{ new Date(review.created_at).toLocaleDateString() }}
                  </span>
                </div>
                <p class="text-gray-700">{{ review.comment }}</p>
              </div>
            </div>
          </section>
        </div>

        <!-- Sidebar -->
        <div class="lg:col-span-1">
          <!-- Location Map -->
          <div class="card mb-6">
            <h3 class="font-semibold text-lg mb-4">Location</h3>
            <div class="bg-gray-200 h-48 rounded mb-3" id="map">
              <!-- Google Maps will be loaded here -->
              <p class="text-center pt-20 text-gray-500">Map Loading...</p>
            </div>
            <p class="text-sm text-gray-700">
              📍 {{ brand.address }}<br>
              {{ brand.city }}, {{ brand.country }}
            </p>
            <p v-if="brand.phone" class="text-sm text-gray-700 mt-2">
              📞 {{ brand.phone }}
            </p>
          </div>

          <!-- Business Hours -->
          <div v-if="brand.business_hours" class="card">
            <h3 class="font-semibold text-lg mb-4">Business Hours</h3>
            <div class="space-y-2 text-sm">
              <div v-for="(hours, day) in brand.business_hours" :key="day" class="flex justify-between">
                <span class="capitalize">{{ day }}</span>
                <span>{{ hours }}</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div v-else class="max-w-7xl mx-auto px-4 py-16 text-center">
    <p class="text-xl text-gray-600">Loading...</p>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { useRoute } from 'vue-router'
import { useAuthStore } from '../stores/auth'
import api from '../services/api'

const route = useRoute()
const authStore = useAuthStore()

const brand = ref(null)
const inventoryItems = ref([])
const reviews = ref([])
const isFavorite = ref(false)
const newReview = ref({ rating: 5, comment: '' })

const userHasReviewed = computed(() => {
  if (!authStore.user) return false
  return reviews.value.some(r => r.user_id === authStore.user.id)
})

onMounted(async () => {
  try {
    const response = await api.getBrand(route.params.slug)
    brand.value = response.data
    inventoryItems.value = response.data.inventory_items || []
    reviews.value = response.data.reviews || []

    // Load Google Maps
    if (brand.value.latitude && brand.value.longitude) {
      loadGoogleMaps()
    }
  } catch (error) {
    console.error('Error loading brand:', error)
  }
})

const toggleFavorite = async () => {
  try {
    await api.toggleFavorite(brand.value.id)
    isFavorite.value = !isFavorite.value
  } catch (error) {
    console.error('Error toggling favorite:', error)
  }
}

const submitReview = async () => {
  try {
    await api.createReview(brand.value.id, newReview.value)
    // Reload reviews
    const response = await api.getBrandReviews(brand.value.id)
    reviews.value = response.data.data
    newReview.value = { rating: 5, comment: '' }
  } catch (error) {
    console.error('Error submitting review:', error)
  }
}

const loadGoogleMaps = () => {
  // Google Maps integration would go here
  // This is a placeholder for the actual implementation
}
</script>
