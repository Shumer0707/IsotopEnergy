<script setup>
  import { useProductFilterStore } from '@/Stores/productFilter'
  import { ref, computed, watch } from 'vue'
  import Slider from '@vueform/slider'
  import '@vueform/slider/themes/default.css'

  const filterStore = useProductFilterStore()

  // локальные значения для инпутов
  const inputMin = ref(filterStore.filters.price_from)
  const inputMax = ref(filterStore.filters.price_to)

  // связка со стором
  const priceRange = computed({
    get: () => [filterStore.filters.price_from, filterStore.filters.price_to],
    set: ([min, max]) => {
      filterStore.filters.price_from = min
      filterStore.filters.price_to = max
      filterStore.apply()
    },
  })

  // следим за внешними изменениями (reset, сорт и т.п.)
  watch(
    () => [filterStore.filters.price_from, filterStore.filters.price_to],
    ([min, max]) => {
      inputMin.value = min
      inputMax.value = max
    },
    { immediate: true }
  )

  // ввод руками — применяем на blur/Enter с клампом и шагом
  const STEP = 100
  const clamp = (v, lo, hi) => Math.min(Math.max(v, lo), hi)
  const roundToStep = (v) => Math.round(v / STEP) * STEP

  const commitInputs = () => {
    const mp = filterStore.filters.max_price

    let min = Number.isFinite(+inputMin.value) ? +inputMin.value : 0
    let max = Number.isFinite(+inputMax.value) ? +inputMax.value : mp

    min = clamp(roundToStep(min), 0, mp)
    max = clamp(roundToStep(max), 0, mp)
    if (min > max) [min, max] = [max, min]

    const changed = min !== filterStore.filters.price_from || max !== filterStore.filters.price_to
    if (!changed) {
      inputMin.value = filterStore.filters.price_from
      inputMax.value = filterStore.filters.price_to
      return
    }

    filterStore.filters.price_from = min
    filterStore.filters.price_to = max
    filterStore.apply()
  }
</script>

<template>
  <div>
    <Slider
      v-model="priceRange"
      :min="0"
      :max="filterStore.filters.max_price"
      :step="100"
      :lazy="true"
      :tooltips="true"
      class="mb-4"
      :key="`${filterStore.filters.price_from}-${filterStore.filters.price_to}-${filterStore.filters.max_price}`"
    />

    <div class="flex items-center justify-between gap-2 mb-4">
      <input
        type="number"
        v-model.number="inputMin"
        :min="0"
        :max="inputMax"
        class="w-1/2 border rounded px-2 py-1 text-sm"
        @blur="commitInputs"
        @keyup.enter="commitInputs"
      />
      <input
        type="number"
        v-model.number="inputMax"
        :min="inputMin"
        :max="filterStore.filters.max_price"
        class="w-1/2 border rounded px-2 py-1 text-sm"
        @blur="commitInputs"
        @keyup.enter="commitInputs"
      />
    </div>

    <div class="text-sm text-gray-500 flex justify-between">
      <span>{{ priceRange[0] }} mdl</span>
      <span>{{ priceRange[1] }} mdl</span>
    </div>
  </div>
</template>
