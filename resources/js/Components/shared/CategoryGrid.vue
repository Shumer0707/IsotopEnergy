<script setup>
  import { onMounted, ref, reactive } from 'vue'
  import { useCategoryStore } from '@/Stores/category'
  import SubcategoryModal from '@/Components/shared/SubcategoryModal.vue'

  const categoryStore = useCategoryStore()
  const triggerRefs = reactive({})

  // onMounted(() => {
  //   if (!categoryStore.isLoaded) {
  //     categoryStore.loadCategories()
  //   }
  // })

  function openModal(category) {
    requestAnimationFrame(() => {
      categoryStore.openCategory(category.id)
    })
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
        :ref="(el) => (triggerRefs[category.id] = el)"
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

    <!-- Подключаем модалку -->
    <SubcategoryModal
      v-if="categoryStore.activeCategory"
      :category="categoryStore.activeCategory"
      :button-ref="triggerRefs[categoryStore.activeCategory.id]"
      @close="categoryStore.closeCategory"
    />
  </div>
</template>
