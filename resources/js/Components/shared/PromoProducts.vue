<script setup>
  import { ref, onMounted } from 'vue'
  import axios from 'axios'

  const promoProducts = ref([])
  const currentPage = ref(1)
  const lastPage = ref(1)

  const fetchProducts = async (page = 1) => {
    const res = await axios.get('/promo-products?page=' + page)
    promoProducts.value = res.data.data
    currentPage.value = res.data.current_page
    lastPage.value = res.data.last_page
  }

  onMounted(() => fetchProducts())

  function discountedPrice(product) {
    const discount = product.promotion?.discount_group?.discount_percent || 0
    return (product.price * (1 - discount / 100)).toFixed(2)
  }
</script>

<template>
  <div>
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-6">
      <div v-for="product in promoProducts" :key="product.id" class="bg-white rounded-2xl shadow p-4 relative">
        <!-- 🔹 Скидка -->
        <div
          v-if="product.promotion?.discount_group"
          class="absolute top-2 left-2 bg-gray-300 text-xs font-bold px-2 py-1 rounded"
        >
          СКИДКА -{{ product.promotion.discount_group.discount_percent }}%
        </div>

        <!-- 🔹 Изображение -->
        <div class="h-40 bg-gray-100 rounded flex items-center justify-center mb-4 overflow-hidden">
          <img
            :src="product.main_image ? `/storage/${product.main_image}` : '/images/placeholder.jpg'"
            alt="product image"
            class="max-h-full max-w-full object-contain"
          />
        </div>

        <!-- 🔹 Название -->
        <h4 class="font-semibold text-sm mb-1 leading-tight">
          {{ product.description?.title ?? 'Без названия' }}
        </h4>
        <p class="text-xs text-gray-600 mb-1">Артикул: {{ product.id }}</p>


        <div class="flex justify-between items-center mt-3">
          <div>
            <div v-if="product.promotion?.discount_group" class="text-xs text-gray-400 line-through">{{ product.price }} mdl</div>
            <div class="text-pink-600 font-bold text-xl">{{ discountedPrice(product) }} mdl</div>
          </div>

          <div class="flex gap-2">
            <button class="bg-gray-600 text-white rounded-md p-2 hover:bg-gray-700" title="Добавить в корзину">🛒</button>
            <button class="bg-gray-200 text-white rounded-md p-2 hover:text-pink-500" title="Добавить в избранное">
              ❤️
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- 🔹 Пагинация -->
    <div class="mt-6 flex justify-center gap-4">
      <button
        v-if="currentPage > 1"
        @click="fetchProducts(currentPage - 1)"
        class="px-4 py-2 rounded bg-gray-200 hover:bg-gray-300"
      >
        Назад
      </button>
      <button
        v-if="currentPage < lastPage"
        @click="fetchProducts(currentPage + 1)"
        class="px-4 py-2 rounded bg-pink-600 text-white hover:bg-pink-700"
      >
        Показать ещё
      </button>
    </div>
  </div>
</template>
