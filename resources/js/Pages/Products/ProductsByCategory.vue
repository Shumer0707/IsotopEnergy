<script setup>
  import { router, usePage } from '@inertiajs/vue3'
  import { ref, onMounted } from 'vue'
  import FilterPanel from '@/Components/shared/FilterPanel.vue'
  import Pagination from '@/Components/common/Pagination.vue'
  import { useCartStore } from '@/Stores/cart'
  import { useProductFilterStore } from '@/Stores/productFilter'
  import ProductCard from '@/Components/shared/ProductCard.vue'
  import ProductSortAndCount from '@/Components/shared/ProductSortAndCount.vue'
  import CategoryHeadSeo from '@/Components/seo/pages/CategoryHeadSeo.vue'

  const props = defineProps({
    category: Object,
    products: Object,
    brands: Array,
    sort: String,
    filters: Object,
    max_price: Number,
    available_filters: Array,
  })

  const cart = useCartStore()
  const filterStore = useProductFilterStore()
  const page = usePage()

  filterStore.init({
    locale: page.props.locale,
    categoryId: props.category.id,
    sort: props.sort,
    filters: props.filters,
    max_price: props.max_price,
    available_filters: props.available_filters,
  })

  // const openProduct = (id) => router.visit(`/product/${id}`)
  const openProduct = (id) => {
    router.visit(route('product.show', { locale: page.props.locale, product: id }))
  }
</script>

<template>
  <CategoryHeadSeo :category="category" :products="products" />
  <div class="max-w-7xl mx-auto px-4">
    <div class="flex flex-col lg:flex-row gap-6">
      <FilterPanel :brands="brands" />

      <div class="flex-1">
        <h1 class="text-2xl font-bold mb-4">
          {{ category.translation.name }}
        </h1>

        <ProductSortAndCount
          :sort="filterStore.filters.sort"
          :from="products.from"
          :to="products.to"
          :total="products.total"
          :onSortChange="filterStore.setSort"
        />

        <div v-if="products.data.length === 0">
          <p>Нет товаров в этой категории.</p>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
          <ProductCard
            v-for="product in products.data"
            :key="product.id"
            :product="product"
            :onClick="openProduct"
            :onAddToCart="cart.add"
          />
        </div>

        <Pagination :links="products.links" />
      </div>
    </div>
  </div>
</template>
