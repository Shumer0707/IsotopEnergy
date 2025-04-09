<script setup>
import { ref, watch } from 'vue'
import { router } from '@inertiajs/vue3'

const props = defineProps({
  brands: Array,
  categoryId: Number,
})

const filters = ref({
  brands: []
})

// 🔄 следим за фильтрами и отправляем запрос при изменении
watch(filters, () => {
  router.get(`/category/${props.categoryId}`, {
    filters: JSON.stringify(filters.value)
  }, {
    preserveScroll: true,
    preserveState: true
  })
}, { deep: true })
</script>

<template>
  <aside class="mb-6 w-full sm:w-64 bg-white border rounded-lg p-4 shadow">
    <h2 class="font-semibold mb-2">Бренды</h2>
    <div v-for="brand in brands" :key="brand.id" class="flex items-center space-x-2">
      <input
        type="checkbox"
        :value="brand.id"
        v-model="filters.brands"
        class="accent-pink-500"
      />
      <label>{{ brand.name }}</label>
    </div>
  </aside>
</template>
