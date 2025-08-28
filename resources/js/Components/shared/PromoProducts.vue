<script setup>
  import { ref, onMounted } from 'vue'
  import { Link, router, usePage } from '@inertiajs/vue3'
  import axios from 'axios'
  import { useCartStore } from '@/Stores/cart'
  // import { useFavoritesStore } from '@/Stores/favorites'
  import ProductCard from '@/Components/shared/ProductCard.vue'
  import { useTranslations } from '@/composables/useTranslations'

  const t = useTranslations()
  // const favorites = useFavoritesStore()
  const promoProducts = ref([])
  const currentPage = ref(1)
  const lastPage = ref(1)

  const cart = useCartStore()

  const page = usePage()
  const openProduct = (id) => {
    router.visit(route('product.show', { locale: page.props.locale, product: id }))
  }

  const fetchProducts = async (page = 1) => {
    const res = await axios.get('/promo-products?page=' + page)
    promoProducts.value = res.data.data
    currentPage.value = res.data.current_page
    lastPage.value = res.data.last_page
  }

  onMounted(() => fetchProducts())
</script>

<template>
  <div>
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
      <ProductCard
        v-for="product in promoProducts"
        :key="product.id"
        :product="product"
        :onClick="openProduct"
        :onAddToCart="cart.add"
      />
    </div>

    <!-- 🔹 Пагинация -->
    <div class="mt-6 flex justify-center gap-4">
      <button
        v-if="currentPage > 1"
        @click="fetchProducts(currentPage - 1)"
        class="px-4 py-2 rounded bg-gray-200 hover:bg-gray-300"
      >
        {{ t['back'] }}
      </button>
      <button
        v-if="currentPage < lastPage"
        @click="fetchProducts(currentPage + 1)"
        class="px-4 py-2 rounded bg-my_green text-my_white hover:bg-my_green_op"
      >
        {{ t['show'] }}
      </button>
    </div>
  </div>
</template>
