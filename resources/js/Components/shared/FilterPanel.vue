<script setup>
  import { useProductFilterStore } from '@/Stores/productFilter'

  const props = defineProps({
    brands: Array,
  })

  const { brands } = props
  const filterStore = useProductFilterStore()
</script>

<template>
  <aside class="mb-6 w-full sm:w-64 bg-white border rounded-lg p-4 shadow">
    <h2 class="font-semibold mb-3">Бренды</h2>

    <div v-for="brand in brands" :key="brand.id" class="flex items-center space-x-2 mb-1">
      <input
        type="checkbox"
        :value="brand.id"
        :checked="filterStore.filters.brands.includes(brand.id)"
        @change="filterStore.toggleBrand(brand.id)"
        class="accent-pink-500"
      />
      <label class="text-sm text-gray-700">{{ brand.name }}</label>
    </div>

    <div v-if="filterStore.filters.brands.length || filterStore.filters.sort" class="mt-4">
      <button
        @click="filterStore.reset"
        class="w-full flex items-center justify-center gap-2 text-sm px-3 py-2 border rounded-lg text-gray-600 hover:bg-gray-100 transition"
      >
        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
        </svg>
        Сбросить фильтры
      </button>
    </div>
  </aside>
</template>
