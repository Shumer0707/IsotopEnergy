<script setup>
import { useProductFilterStore } from '@/Stores/productFilter'
import { ref, computed, watch } from 'vue'
import '@vueform/slider/themes/default.css'
import Slider from '@vueform/slider'

const props = defineProps({
  brands: Array,
  maxPrice: Number,
})

const filterStore = useProductFilterStore()

// Значения для инпутов
const inputMin = ref(filterStore.filters.price_from)
const inputMax = ref(filterStore.filters.price_to)

// priceRange связан со store напрямую
const priceRange = computed({
  get: () => [filterStore.filters.price_from, filterStore.filters.price_to],
  set: ([min, max]) => {
    filterStore.filters.price_from = min
    filterStore.filters.price_to = max
    filterStore.apply()
  },
})

// Синхронизация инпутов со стором и ползунком
watch(
  () => [filterStore.filters.price_from, filterStore.filters.price_to],
  ([min, max]) => {
    inputMin.value = min
    inputMax.value = max
  },
  { immediate: true }
)

watch([inputMin, inputMax], ([min, max]) => {
  if (min !== null && max !== null) {
    priceRange.value = [min, max]
  }
})
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

    <h2 class="font-semibold mt-4 mb-2">Цена</h2>

    <Slider
      v-model="priceRange"
      :min="0"
      :max="maxPrice"
      :step="100"
      :lazy="true"
      :tooltips="true"
      class="mb-4"
    />

    <div class="flex items-center justify-between gap-2 mb-4">
      <input
        type="number"
        v-model.number="inputMin"
        :min="0"
        :max="inputMax"
        class="w-1/2 border rounded px-2 py-1 text-sm"
      />
      <input
        type="number"
        v-model.number="inputMax"
        :min="inputMin"
        :max="maxPrice"
        class="w-1/2 border rounded px-2 py-1 text-sm"
      />
    </div>

    <div class="text-sm text-gray-500 flex justify-between mb-4">
      <span>{{ priceRange[0] }} mdl</span>
      <span>{{ priceRange[1] }} mdl</span>
    </div>

    <div v-if="filterStore.filters.brands.length || filterStore.filters.sort" class="mt-4">
      <button
        @click="filterStore.reset"
        class="w-full flex items-center justify-center gap-2 text-sm px-3 py-2 border rounded-lg text-gray-600 hover:bg-gray-100 transition"
      >
        <svg
          xmlns="http://www.w3.org/2000/svg"
          class="w-4 h-4"
          fill="none"
          viewBox="0 0 24 24"
          stroke="currentColor"
        >
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M6 18L18 6M6 6l12 12"
          />
        </svg>
        Сбросить фильтры
      </button>
    </div>
  </aside>
</template>
