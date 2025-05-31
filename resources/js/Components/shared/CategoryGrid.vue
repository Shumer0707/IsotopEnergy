<script setup>
  import { onMounted } from 'vue'
  import { useCategoryStore } from '@/Stores/category'

  const categoryStore = useCategoryStore()

  onMounted(() => {
    if (!categoryStore.isLoaded) {
      categoryStore.loadCategories()
    }
  })

  const openModal = (category) => {
    categoryStore.openCategory(category.id)
  }
</script>

<template>
  <div>
    <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-6 xl:grid-cols-10 gap-8 sm:gap-4 text-sm sm:text-base">
      <div
        v-for="category in categoryStore.navCategories"
        :key="category.id"
        class="flex flex-col items-center cursor-pointer"
        @click="openModal(category)"
      >
        <img
          :src="category.logo ? `/storage/${category.logo}` : `/images/placeholder.webp`"
          alt="logo"
          class="w-20 h-20 object-contain rounded-md bg-white"
        />
        <span class="text-md mt-2 text-center">
          {{ category.translation?.name ?? 'Без названия' }}
        </span>
      </div>
    </div>
  </div>
</template>
