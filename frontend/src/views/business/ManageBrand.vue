<template>
  <div v-if="brand" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-8">
      <router-link to="/business/brands" class="text-primary-600 hover:underline">
        ← Back to My Brands
      </router-link>
    </div>

    <div class="flex justify-between items-center mb-8">
      <h1 class="text-3xl font-bold">{{ brand.name }}</h1>
      <router-link :to="`/brands/${brand.slug}`" class="btn btn-secondary">
        View Public Page
      </router-link>
    </div>

    <!-- Tabs -->
    <div class="border-b mb-6">
      <div class="flex gap-8">
        <button
          @click="activeTab = 'info'"
          class="pb-3 border-b-2 transition"
          :class="activeTab === 'info' ? 'border-primary-600 text-primary-600 font-semibold' : 'border-transparent'"
        >
          Brand Information
        </button>
        <button
          @click="activeTab = 'inventory'"
          class="pb-3 border-b-2 transition"
          :class="activeTab === 'inventory' ? 'border-primary-600 text-primary-600 font-semibold' : 'border-transparent'"
        >
          Inventory / Menu ({{ inventoryItems.length }})
        </button>
      </div>
    </div>

    <!-- Brand Information Tab -->
    <div v-if="activeTab === 'info'" class="card">
      <h2 class="text-2xl font-bold mb-6">Brand Information</h2>

      <form @submit.prevent="updateBrand" class="space-y-4">
        <div>
          <label class="block text-sm font-medium mb-2">Brand Name</label>
          <input v-model="brand.name" type="text" required class="input" />
        </div>

        <div>
          <label class="block text-sm font-medium mb-2">Description</label>
          <textarea v-model="brand.description" rows="4" class="input"></textarea>
        </div>

        <div class="grid grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium mb-2">Address</label>
            <input v-model="brand.address" type="text" required class="input" />
          </div>

          <div>
            <label class="block text-sm font-medium mb-2">City</label>
            <input v-model="brand.city" type="text" required class="input" />
          </div>
        </div>

        <div class="grid grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium mb-2">Phone</label>
            <input v-model="brand.phone" type="tel" class="input" />
          </div>

          <div>
            <label class="block text-sm font-medium mb-2">Email</label>
            <input v-model="brand.email" type="email" class="input" />
          </div>
        </div>

        <div v-if="brand.category?.slug === 'restaurant'">
          <h3 class="font-semibold mb-3">Delivery Services</h3>

          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium mb-2">Talabat URL</label>
              <input v-model="brand.talabat_url" type="url" class="input" placeholder="https://..." />
            </div>

            <div>
              <label class="block text-sm font-medium mb-2">Deliveroo URL</label>
              <input v-model="brand.deliveroo_url" type="url" class="input" placeholder="https://..." />
            </div>
          </div>
        </div>

        <div v-if="updateError" class="p-3 bg-red-100 text-red-700 rounded text-sm">
          {{ updateError }}
        </div>

        <div v-if="updateSuccess" class="p-3 bg-green-100 text-green-700 rounded text-sm">
          Brand updated successfully!
        </div>

        <button type="submit" class="btn btn-primary" :disabled="updating">
          {{ updating ? 'Saving...' : 'Save Changes' }}
        </button>
      </form>
    </div>

    <!-- Inventory Tab -->
    <div v-if="activeTab === 'inventory'">
      <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold">
          {{ brand.category?.slug === 'restaurant' ? 'Menu Items' : 'Inventory' }}
        </h2>
        <button @click="showItemForm = true" class="btn btn-primary">
          + Add Item
        </button>
      </div>

      <!-- Add/Edit Item Form -->
      <div v-if="showItemForm" class="card mb-6">
        <h3 class="text-xl font-semibold mb-4">
          {{ editingItem ? 'Edit Item' : 'Add New Item' }}
        </h3>

        <form @submit.prevent="saveItem" class="space-y-4">
          <div>
            <label class="block text-sm font-medium mb-2">Item Name</label>
            <input v-model="itemForm.name" type="text" required class="input" />
          </div>

          <div>
            <label class="block text-sm font-medium mb-2">Description</label>
            <textarea v-model="itemForm.description" rows="2" class="input"></textarea>
          </div>

          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium mb-2">Price (KD)</label>
              <input v-model.number="itemForm.price" type="number" step="0.01" required class="input" />
            </div>

            <div>
              <label class="block text-sm font-medium mb-2">Sale Price (KD)</label>
              <input v-model.number="itemForm.sale_price" type="number" step="0.01" class="input" />
            </div>
          </div>

          <div v-if="brand.category?.slug === 'fashion'" class="grid grid-cols-3 gap-4">
            <div>
              <label class="block text-sm font-medium mb-2">Size</label>
              <input v-model="itemForm.size" type="text" class="input" />
            </div>

            <div>
              <label class="block text-sm font-medium mb-2">Color</label>
              <input v-model="itemForm.color" type="text" class="input" />
            </div>

            <div>
              <label class="block text-sm font-medium mb-2">Stock Quantity</label>
              <input v-model.number="itemForm.stock_quantity" type="number" class="input" />
            </div>
          </div>

          <div class="flex items-center">
            <input v-model="itemForm.is_available" type="checkbox" class="mr-2" id="available" />
            <label for="available" class="text-sm font-medium">Available</label>
          </div>

          <div class="flex gap-4">
            <button type="submit" class="btn btn-primary">
              {{ editingItem ? 'Update Item' : 'Add Item' }}
            </button>
            <button type="button" @click="cancelItemForm" class="btn btn-secondary">
              Cancel
            </button>
          </div>
        </form>
      </div>

      <!-- Items List -->
      <div v-if="inventoryItems.length === 0" class="text-center py-12 card">
        <p class="text-xl text-gray-600 mb-4">No items added yet</p>
        <button @click="showItemForm = true" class="btn btn-primary">
          Add Your First Item
        </button>
      </div>

      <div v-else class="space-y-4">
        <div v-for="item in inventoryItems" :key="item.id" class="card flex justify-between items-start">
          <div class="flex-1">
            <h3 class="font-semibold text-lg">{{ item.name }}</h3>
            <p class="text-sm text-gray-600 mb-2">{{ item.description }}</p>

            <div class="flex items-center gap-4 text-sm">
              <span class="font-semibold text-primary-600">
                {{ item.sale_price || item.price }} KD
                <span v-if="item.sale_price" class="text-gray-500 line-through ml-1">
                  {{ item.price }} KD
                </span>
              </span>

              <span
                class="px-2 py-1 text-xs rounded"
                :class="item.is_available ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'"
              >
                {{ item.is_available ? 'Available' : 'Unavailable' }}
              </span>

              <span v-if="item.stock_quantity !== null" class="text-gray-600">
                Stock: {{ item.stock_quantity }}
              </span>
            </div>
          </div>

          <div class="flex gap-2">
            <button @click="editItem(item)" class="btn btn-secondary">
              Edit
            </button>
            <button @click="deleteItem(item.id)" class="btn bg-red-500 text-white hover:bg-red-600">
              Delete
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import api from '../../services/api'

const route = useRoute()
const brand = ref(null)
const inventoryItems = ref([])
const activeTab = ref('info')
const showItemForm = ref(false)
const editingItem = ref(null)
const updating = ref(false)
const updateError = ref('')
const updateSuccess = ref('')

const itemForm = ref({
  name: '',
  description: '',
  price: 0,
  sale_price: null,
  size: '',
  color: '',
  stock_quantity: 0,
  is_available: true
})

onMounted(async () => {
  try {
    const brandsResponse = await api.getMyBrands()
    brand.value = brandsResponse.data.find(b => b.id === parseInt(route.params.id))

    if (brand.value) {
      const inventoryResponse = await api.getBrandInventory(brand.value.id)
      inventoryItems.value = inventoryResponse.data
    }
  } catch (error) {
    console.error('Error loading brand:', error)
  }
})

const updateBrand = async () => {
  updating.value = true
  updateError.value = ''
  updateSuccess.value = ''

  try {
    await api.updateBrand(brand.value.id, brand.value)
    updateSuccess.value = 'Brand updated successfully!'

    setTimeout(() => {
      updateSuccess.value = ''
    }, 3000)
  } catch (err) {
    updateError.value = err.response?.data?.message || 'Failed to update brand'
  } finally {
    updating.value = false
  }
}

const saveItem = async () => {
  try {
    if (editingItem.value) {
      await api.updateInventoryItem(brand.value.id, editingItem.value.id, itemForm.value)
    } else {
      await api.createInventoryItem(brand.value.id, itemForm.value)
    }

    // Reload inventory
    const response = await api.getBrandInventory(brand.value.id)
    inventoryItems.value = response.data

    cancelItemForm()
  } catch (error) {
    console.error('Error saving item:', error)
  }
}

const editItem = (item) => {
  editingItem.value = item
  itemForm.value = { ...item }
  showItemForm.value = true
}

const deleteItem = async (itemId) => {
  if (!confirm('Are you sure you want to delete this item?')) return

  try {
    await api.deleteInventoryItem(brand.value.id, itemId)

    // Reload inventory
    const response = await api.getBrandInventory(brand.value.id)
    inventoryItems.value = response.data
  } catch (error) {
    console.error('Error deleting item:', error)
  }
}

const cancelItemForm = () => {
  showItemForm.value = false
  editingItem.value = null
  itemForm.value = {
    name: '',
    description: '',
    price: 0,
    sale_price: null,
    size: '',
    color: '',
    stock_quantity: 0,
    is_available: true
  }
}
</script>
