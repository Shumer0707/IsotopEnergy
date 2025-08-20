<script setup>
  import { useProductFilterStore } from '@/Stores/productFilter'
  import { computed, ref, watch } from 'vue'
  import PriceFilter from '@/Components/shared/PriceFilter.vue'

  const props = defineProps({
    brands: Array,
  })

  const filterStore = useProductFilterStore()

  const showReset = computed(() => {
    const f = filterStore.filters
    return (
      f.brands.length > 0 || !!f.sort || f.price_from !== 0 || f.price_to !== f.max_price || Object.keys(f.attrs || {}).length > 0
    )
  })

  const collapsed = ref({})

  // init: выбранные атрибуты — открыты, остальные — свёрнуты
  const initCollapsed = () => {
    const map = {}
    for (const attr of filterStore.availableFilters) {
      const selected = (filterStore.filters.attrs[attr.id] || []).length
      map[attr.id] = selected === 0 // true = свернуть
    }
    collapsed.value = map
  }

  watch(
    () => filterStore.availableFilters,
    () => initCollapsed(),
    { immediate: true, deep: true }
  )

  const toggleAttrOpen = (id) => {
    collapsed.value = { ...collapsed.value, [id]: !collapsed.value[id] }
  }

  const selectedCount = (attrId) => (filterStore.filters.attrs[attrId] || []).length

  const clearAttribute = (attrId) => {
    if (!filterStore.filters.attrs[attrId]) return
    const next = { ...filterStore.filters.attrs }
    delete next[attrId]
    filterStore.filters.attrs = next
    filterStore.apply()
  }
</script>

<template>
  <aside class="mb-6 w-full sm:w-64 bg-white border rounded-lg p-4 shadow">
    <h2 class="font-semibold mb-3">Бренды</h2>
    <div v-for="brand in brands" :key="brand.id" class="flex items-center space-x-2 mb-1">
      <input
        type="checkbox"
        :value="brand.id"
        :checked="filterStore.filters.brands.includes(Number(brand.id))"
        @change="filterStore.toggleBrand(brand.id)"
        class="accent-pink-500"
      />
      <label class="text-sm text-gray-700">{{ brand.name }}</label>
    </div>

    <h2 class="font-semibold mt-4 mb-2">Цена</h2>
    <PriceFilter />

    <!-- ХАРАКТЕРИСТИКИ (атрибуты) -->
    <template v-if="filterStore.availableFilters.length">
      <h2 class="font-semibold mt-4 mb-2">Характеристики</h2>

      <div v-for="attr in filterStore.availableFilters" :key="attr.id" class="mb-3 border rounded-lg">
        <!-- Заголовок атрибута -->
        <button type="button" class="w-full flex items-center justify-between px-3 py-2" @click="toggleAttrOpen(Number(attr.id))">
          <div class="flex items-center gap-2">
            <span class="text-sm font-medium text-gray-800">{{ attr.name }}</span>
            <!-- бейдж с кол-вом выбранных -->
            <span v-if="selectedCount(Number(attr.id))" class="text-xs bg-pink-100 text-pink-700 rounded-full px-2 py-0.5">
              {{ selectedCount(Number(attr.id)) }}
            </span>
          </div>

          <!-- стрелка + кнопка очистить -->
          <div class="flex items-center gap-2">
            <button
              v-if="selectedCount(Number(attr.id))"
              type="button"
              class="text-xs text-gray-500 hover:text-gray-700 underline"
              @click.stop="clearAttribute(Number(attr.id))"
              title="Очистить выбранные"
            >
              Очистить
            </button>
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="w-4 h-4 transition-transform"
              :class="{ 'rotate-180': !collapsed[Number(attr.id)] }"
              fill="none"
              viewBox="0 0 24 24"
              stroke="currentColor"
            >
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>
          </div>
        </button>

        <!-- Тело: чекбоксы значений -->
        <div v-show="!collapsed[Number(attr.id)]" class="px-3 pb-3">
          <div class="space-y-1">
            <label v-for="val in attr.values" :key="val.id" class="flex items-center gap-2 text-sm text-gray-700">
              <input
                type="checkbox"
                :value="val.id"
                :checked="(filterStore.filters.attrs[attr.id] || []).includes(Number(val.id))"
                @change="filterStore.toggleAttr(attr.id, val.id)"
                class="accent-pink-500"
              />
              <span class="flex-1 truncate">{{ val.label }}</span>
              <span class="text-gray-400 text-xs">({{ val.count }})</span>
            </label>
          </div>
        </div>
      </div>
    </template>

    <div v-if="showReset" class="mt-4">
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
