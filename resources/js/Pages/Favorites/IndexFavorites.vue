<script setup>
  import { router, usePage } from '@inertiajs/vue3'
  import { useFavoritesStore } from '@/Stores/favorites'
  import { useCartStore } from '@/Stores/cart'
  import { computed, onMounted } from 'vue'
  import ProductCard from '@/Components/shared/ProductCard.vue'
  import { useTranslations } from '@/composables/useTranslations'

  const t = useTranslations()
  const favorites = useFavoritesStore()
  const cart = useCartStore()

  const products = computed(() => favorites.items)

  const page = usePage()
  const openProduct = (id) => {
    router.visit(route('product.show', { locale: page.props.locale, product: id }))
  }
</script>

<template>
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <!-- 🔹 Заголовок -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
      <h1 class="text-2xl font-bold">{{ t['favorite_title'] }}</h1>
      <button
        v-if="products.length > 0"
        @click="favorites.clear()"
        class="text-sm text-red-600 hover:underline whitespace-nowrap"
      >
        {{ t['favorite_delete_all'] }}
      </button>
    </div>

    <!-- 🔹 Пусто -->
    <div v-if="products.length === 0" class="text-gray-500 text-center py-8">
      {{ t['favorite_not'] }}
    </div>

    <!-- 🔹 Сетка карточек -->
    <div v-else class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-4">
      <ProductCard v-for="product in products" :key="product.id" :product="product" :onClick="openProduct" :inFavorites="true" />
    </div>
  </div>
</template>
