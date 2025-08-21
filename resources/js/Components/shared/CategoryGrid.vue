<script setup>
  import { onMounted, reactive } from 'vue'
  import { useCategoryStore } from '@/Stores/category'
  import SubcategoryModal from '@/Components/shared/SubcategoryModal.vue'
  import { Link } from '@inertiajs/vue3'

  const categoryStore = useCategoryStore()
  const triggerRefs = reactive({})

  onMounted(() => {
    if (!categoryStore.isLoaded) {
      categoryStore.loadCategories()
    }
  })

  function openModalById(id) {
    requestAnimationFrame(() => {
      categoryStore.openCategory(id)
    })
  }
</script>

<template>
  <div>
    <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-6 xl:grid-cols-10 gap-8 sm:gap-4 text-sm sm:text-base">
      <Link
        v-for="sub in categoryStore.allSubcategories"
        :key="sub.id"
        :href="`/category/${sub.id}`"
        class="flex flex-col items-center cursor-pointer"
        :ref="(el) => (triggerRefs[sub.id] = el)"
      >
        <img
          :src="sub.logo ? `/storage/${sub.logo}` : `/images/placeholder.webp`"
          alt="logo"
          class="w-20 h-20 object-contain rounded-md bg-white"
        />
        <span class="text-md mt-2 text-center">
          {{ sub.translation?.name ?? 'Без названия' }}
        </span>
        <span class="text-xs text-gray-500 mt-1 text-center">
          {{ sub.parent_name }}
        </span>
      </Link>
    </div>

    <SubcategoryModal
      v-if="categoryStore.activeCategory"
      :category="categoryStore.activeCategory"
      :button-ref="triggerRefs[categoryStore.activeCategory.id]"
      @close="categoryStore.closeCategory"
    />
  </div>
</template>
