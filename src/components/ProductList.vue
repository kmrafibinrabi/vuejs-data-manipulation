<template>
  <div class="p-6">
    <div class="flex justify-between items-center mb-4">
      <h2 class="text-2xl font-semibold">🛍️ Product List</h2>
      <div class="flex gap-2">
        <button 
          @click="viewMode = 'list'"
          class="p-2 rounded-md"
          :class="{'bg-blue-100 text-blue-600': viewMode === 'list'}"
          title="List View"
        >
          <i class="fas fa-list"></i>
        </button>
        <button 
          @click="viewMode = 'card'"
          class="p-2 rounded-md"
          :class="{'bg-blue-100 text-blue-600': viewMode === 'card'}"
          title="Card View"
        >
          <i class="fas fa-grip-vertical"></i>
        </button>
      </div>
    </div>

    <!-- Vertical Scrollable List View -->
    <div v-if="viewMode === 'list' && products.length > 0" class="relative">
      <div class="overflow-y-auto h-[calc(2*17rem)] hide-scrollbar space-y-4">
        <div 
          v-for="product in products"
          :key="product.id"
          class="ring-2 ring-blue-600 rounded-[20px] overflow-hidden h-64 bg-white"
        >
          <div class="flex h-full">
            <div class="left w-2/5 h-full">
              <img 
                class="h-full w-full object-cover" 
                src="https://www.smartcurrencyexchange.com/media/shutterstock_654737650.png"
                :alt="product.name"
              >
            </div>
            <div class="right w-3/5 flex flex-col gap-2 p-3">
              <div class="top">
                <h1 class="text-xl text-blue-600 font-bold line-clamp-1">
                  {{ product.name }}
                </h1>
                <p class="text-gray-500">
                  <span class="text-xl font-bold text-gray-700">
                    ৳ {{ product.price }}
                  </span>
                  /month
                </p>
              </div>
              <div class="buttons flex gap-2">
                <button class="btn text-white text-sm w-3/4 btn-primary py-1 px-2">Add to cart</button>
                <button class="btn btn-square ring-1 ring-inset ring-gray-500 btn-ghost w-8 h-8">
                  <i class="far fa-heart text-sm"></i>
                </button>
              </div>
              <div class="info">
                <div class="text-xs font-medium">HIGHLIGHTS</div>
                <div class="text-gray-700 text-xs line-clamp-3">
                  {{ product.description }}
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Card View -->
    <div v-if="viewMode === 'card' && products.length > 0" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
      <div
        v-for="product in products"
        :key="product.id"
        class="bg-white shadow-md p-4 rounded"
      >
        <h3 class="text-lg font-bold">{{ product.name }}</h3>
        <p class="text-sm text-gray-600">{{ product.description }}</p>
        <p class="text-blue-600 font-semibold mt-2">৳ {{ product.price }}</p>
      </div>
    </div>

    <div v-if="products.length === 0" class="text-gray-500">
      Loading products...
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'

const products = ref([])
const viewMode = ref('list') // 'list' or 'card'

onMounted(async () => {
  try {
    const res = await fetch('http://localhost:3000/products')
    const data = await res.json()
    products.value = data
  } catch (error) {
    console.error('Error fetching products:', error)
  }
})
</script>

<style>
@import url('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css');

.hide-scrollbar {
  -ms-overflow-style: none;
  scrollbar-width: none;
}
.hide-scrollbar::-webkit-scrollbar {
  display: none;
}

/* Set fixed height to show exactly 2 cards at a time */
.h-\[calc\(2\*17rem\)\] {
  height: calc(2 * 17rem); /* 2 cards * (16rem card + 1rem gap) */
}
</style>