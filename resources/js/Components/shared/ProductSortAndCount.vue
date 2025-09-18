<script setup>
  import { useTranslations } from '@/composables/useTranslations'

  const t = useTranslations()

  defineProps({
    sort: String,
    total: Number,
    from: Number,
    to: Number,
    onSortChange: Function,
  })
</script>

<template>
  <div class="flex justify-between items-center mb-4">
    <div class="text-sm text-gray-600 flex items-center gap-2">
      {{ t['product_sorting'] }}:
      <select
        class="border rounded px-2 py-1 pr-8 text-sm appearance-none bg-white"
        :value="sort"
        @change="onSortChange($event.target.value)"
      >
        <option value="">{{ t['product_default'] }}</option>
        <option value="cheap">{{ t['product_low'] }}</option>
        <option value="expensive">{{ t['product_high'] }}</option>
      </select>
    </div>

    <div class="text-sm text-gray-600">{{ t['product_showing'] }} {{ from }}–{{ to }} {{ t['product_of'] }} {{ total }} {{ t['product_products'] }}</div>
  </div>
</template>
