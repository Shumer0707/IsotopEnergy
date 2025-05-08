<script setup>
  import { router } from '@inertiajs/vue3'
  import { ref, onMounted } from 'vue'
  import FilterPanel from '@/Components/shared/FilterPanel.vue'
  import QuantityControl from '@/Components/common/QuantityControl.vue'
  import { useCartStore } from '@/Stores/cart'
  import { useFavoritesStore } from '@/Stores/favorites'

  const props = defineProps({
    category: Object,
    products: Array,
    brands: Array,
  })

  const cart = useCartStore()
  const favorites = useFavoritesStore()

  const openProduct = (id) => {
    router.visit(`/product/${id}`)
  }

  onMounted(() => {
    favorites.load()
  })
</script>

<template>
  <div class="flex flex-col lg:flex-row gap-6">
    <FilterPanel :brands="brands" :category-id="category.id" />

    <div class="flex-1">
      <h1 class="text-2xl font-bold mb-4">
        {{ category.translation.name }}
      </h1>

      <div v-if="products.length === 0">
        <p>Нет товаров в этой категории.</p>
      </div>

      <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
        <div v-for="product in products" :key="product.id" class="border rounded p-4 flex flex-col">
          <img
            :src="product.main_image ? `/storage/${product.main_image}` : '/images/placeholder.jpg'"
            alt=""
            class="w-full h-32 object-cover mb-2 cursor-pointer"
            @click="openProduct(product.id)"
          />
          <h3 class="text-lg font-semibold">
            {{ product.description?.title ?? 'Без названия' }}
          </h3>
          <p class="text-sm">{{ product.price }} {{ product.currency }}</p>
          <p class="text-sm mb-2">{{ product.brand.name }}</p>

          <div class="flex justify-between items-center mt-3">
            <div>
              <div v-if="product.promotion?.discount_group" class="text-xs text-gray-400 line-through">
                {{ product.price }} mdl
              </div>
              <div class="text-pink-600 font-bold text-xl">{{ product.price }} mdl</div>
            </div>

            <div class="flex gap-2">
              <button @click="cart.add(product.id)" class="p-1 text-xl text-white rounded-md bg-gray-600 hover:bg-gray-700">
                🛒
              </button>

              <button
                @click="favorites.localToggle(product)"
                class="p-1 text-2xl hover:text-gray-300"
                title="Добавить в избранное"
              >
                <font-awesome-icon
                  :icon="favorites.isFavorite(product.id) ? ['fas', 'heart'] : ['far', 'heart']"
                  class="text-gray-500 hover:text-pink-600"
                />
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
