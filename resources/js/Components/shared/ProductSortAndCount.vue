<script setup>
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
      Сортировка:
      <select
        class="border rounded px-2 py-1 text-sm"
        :value="sort"
        @change="onSortChange($event.target.value)"
      >
        <option value="">По умолчанию</option>
        <option value="cheap">Сначала дешёвые</option>
        <option value="expensive">Сначала дорогие</option>
      </select>
    </div>

    <div class="text-sm text-gray-600">
      Показано {{ from }}–{{ to }} из {{ total }} товаров
    </div>
  </div>
</template>
