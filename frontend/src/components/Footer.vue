<template>
  <footer class="bg-gray-800 text-white mt-auto">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <div>
          <h3 class="text-xl font-bold mb-4">OnTheSpot</h3>
          <p class="text-gray-300">
            Discover and connect with trusted local brands and shops in fashion, dining, and more.
          </p>
        </div>

        <div>
          <h4 class="font-semibold mb-4">Quick Links</h4>
          <ul class="space-y-2">
            <li><router-link to="/search" class="text-gray-300 hover:text-white">Discover Brands</router-link></li>
            <li><router-link to="/categories/fashion" class="text-gray-300 hover:text-white">Fashion</router-link></li>
            <li><router-link to="/categories/restaurant" class="text-gray-300 hover:text-white">Restaurants</router-link></li>
            <li><router-link to="/register" class="text-gray-300 hover:text-white">List Your Business</router-link></li>
          </ul>
        </div>

        <div>
          <h4 class="font-semibold mb-4">Contact</h4>
          <p class="text-gray-300">
            Location: Place<br>
            Email: hello@onthespot.com
          </p>
        </div>
      </div>

      <div class="mt-8 pt-8 border-t border-gray-700 text-center text-gray-400">
        <p>&copy; 2024 OnTheSpot. All rights reserved.</p>
      </div>
    </div>
  </footer>
</template>
