import axios from 'axios'

const api = axios.create({
  baseURL: import.meta.env.VITE_API_URL || 'http://localhost:8000/api',
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json'
  }
})

// Request interceptor to add auth token
api.interceptors.request.use(
  config => {
    const token = localStorage.getItem('token')
    if (token) {
      config.headers.Authorization = `Bearer ${token}`
    }
    return config
  },
  error => Promise.reject(error)
)

// Response interceptor for error handling
api.interceptors.response.use(
  response => response,
  error => {
    if (error.response?.status === 401) {
      localStorage.removeItem('token')
      window.location.href = '/login'
    }
    return Promise.reject(error)
  }
)

export default {
  // Auth
  register: (data) => api.post('/register', data),
  login: (data) => api.post('/login', data),
  logout: () => api.post('/logout'),
  me: () => api.get('/me'),

  // Categories
  getCategories: () => api.get('/categories'),
  getCategory: (slug) => api.get(`/categories/${slug}`),

  // Brands
  getBrands: (params) => api.get('/brands', { params }),
  getBrand: (slug) => api.get(`/brands/${slug}`),
  createBrand: (data) => api.post('/brands', data),
  updateBrand: (id, data) => api.put(`/brands/${id}`, data),
  deleteBrand: (id) => api.delete(`/brands/${id}`),
  getMyBrands: () => api.get('/my-brands'),

  // Inventory
  getBrandInventory: (brandId) => api.get(`/brands/${brandId}/inventory`),
  createInventoryItem: (brandId, data) => api.post(`/brands/${brandId}/inventory`, data),
  updateInventoryItem: (brandId, itemId, data) => api.put(`/brands/${brandId}/inventory/${itemId}`, data),
  deleteInventoryItem: (brandId, itemId) => api.delete(`/brands/${brandId}/inventory/${itemId}`),

  // Reviews
  getBrandReviews: (brandId, params) => api.get(`/brands/${brandId}/reviews`, { params }),
  createReview: (brandId, data) => api.post(`/brands/${brandId}/reviews`, data),
  updateReview: (reviewId, data) => api.put(`/reviews/${reviewId}`, data),
  deleteReview: (reviewId) => api.delete(`/reviews/${reviewId}`),

  // Favorites
  getFavorites: () => api.get('/favorites'),
  toggleFavorite: (brandId) => api.post(`/brands/${brandId}/favorite`),
}
