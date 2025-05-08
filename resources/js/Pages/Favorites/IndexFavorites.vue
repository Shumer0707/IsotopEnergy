<script setup>
  import { router } from '@inertiajs/vue3'
  import { useFavoritesStore } from '@/Stores/favorites'
  import { useCartStore } from '@/Stores/cart'
  import { computed, onMounted } from 'vue'

  const favorites = useFavoritesStore()
  const cart = useCartStore()

  onMounted(() => {
    favorites.load()
  })

  const products = computed(() => favorites.items)

  const openProduct = (id) => {
    router.visit(`/product/${id}`)
  }
</script>

<template>
  <div class="p-6">
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-2xl font-bold">Избранное</h1>
      <button @click="favorites.clear()" class="text-sm text-red-600 hover:underline">Очистить избранное</button>
    </div>

    <div v-if="products.length === 0" class="text-gray-500">Нет избранных товаров.</div>

    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4" v-else>
      <div v-for="product in products" :key="product.id" class="border rounded p-4 flex flex-col">
        <img
          :src="product.main_image ? `/storage/${product.main_image}` : '/images/placeholder.jpg'"
          alt=""
          class="w-full h-32 object-cover mb-2 cursor-pointer"
          @click="openProduct(product.id)"
        />
        <h3 class="text-lg font-semibold">{{ product.description?.title ?? 'Без названия' }}</h3>
        <p class="text-sm">{{ product.price }} {{ product.currency }}</p>

        <div class="flex justify-between mt-3">
          <button
            @click="favorites.localToggle(product)"
            class="text-red-500 hover:text-red-700 text-lg"
            title="Удалить из избранного"
          >
            ✖
          </button>

          <button
            @click="cart.add(product.id)"
            class="p-1 text-white bg-gray-600 hover:bg-gray-700 rounded"
            title="Добавить в корзину"
          >
            🛒
          </button>
        </div>
      </div>
    </div>
  </div>
</template>
