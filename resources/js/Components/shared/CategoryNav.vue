<script setup>
  import { ref, computed, onMounted, onBeforeUnmount } from 'vue'
  import { Link, usePage } from '@inertiajs/vue3'
  import { useCategoryStore } from '@/Stores/category'
  import { useLayoutStore } from '@/Stores/layout'

  const layout = useLayoutStore()
  const categoryStore = useCategoryStore()
  const showCategories = ref(false)
  const navRef = ref(null)
  const menuTop = ref(0)
  const categoryButtonRef = ref(null)

  const updateCategoryOffset = () => {
    if (categoryButtonRef.value) {
      const rect = categoryButtonRef.value.getBoundingClientRect()
      layout.setCategoryButtonLeftOffset(rect.left)
    }
  }

  onMounted(() => {
    updateCategoryOffset()
    window.addEventListener('resize', updateCategoryOffset)
  })

  onBeforeUnmount(() => {
    window.removeEventListener('resize', updateCategoryOffset)
  })
  // Следим за языком
  const locale = computed(() => usePage().props.locale)

  // Загружаем категории при открытии меню
  const toggleCategories = () => {
    showCategories.value = !showCategories.value

    if (!categoryStore.isLoaded && showCategories.value) {
      categoryStore.loadCategories()
    }
  }

  // Категории для вывода
  const categories = computed(() => {
    return categoryStore.navCategories.map((category) => ({
      id: category.id,
      name: category.translation?.name ?? 'Без названия',
      children: (category.children || []).map((sub) => ({
        id: sub.id,
        name: sub.translation?.name ?? 'Без названия',
      })),
    }))
  })

  // Обработка клика вне меню
  const handleClickOutside = (event) => {
    if (navRef.value && !navRef.value.contains(event.target)) {
      showCategories.value = false
    }
  }

  // Открытие модалки
  function openSubcategories(category) {
    categoryStore.openCategory(category.id)
  }

  // Расчёт позиции меню
  onMounted(() => {
    document.addEventListener('click', handleClickOutside)
  })

  onBeforeUnmount(() => {
    document.removeEventListener('click', handleClickOutside)
  })
</script>

<template>
  <div ref="navRef" class="relative">
    <button
      ref="categoryButtonRef"
      @click="toggleCategories"
      class="flex items-center gap-2 whitespace-nowrap bg-my_green hover:bg-my_green_op text-white px-4 sm:px-6 py-2 sm:py-3 rounded-xl text-sm lg:text-base"
    >
      <font-awesome-icon icon="chevron-down" class="text-xs" />
      <span>Все категории</span>
    </button>

    <div
      v-if="showCategories"
      :style="{ top: `${layout.headerBottom}px`, left: `${layout.categoryButtonLeftOffset}px` }"
      class="fixed bg-white text-gray-800 shadow-xl z-50 transition-all duration-300 px-4 py-2 rounded-xl"
    >
      <div v-for="category in categories" :key="category.id" class="relative group">
        <button class="block  w-full px-4 py-2 text-left hover:bg-gray-200" @click.prevent="openSubcategories(category)">
          {{ category.name }}
        </button>
        <div
          class="absolute rounded-xl left-full top-0 bg-white shadow-md opacity-0 group-hover:opacity-100 transition-all duration-300 overflow-hidden max-h-0 group-hover:max-h-[400px] w-0 group-hover:min-w-max"
        >
          <Link
            v-for="sub in category.children"
            :key="sub.id"
            :href="`/category/${sub.id}`"
            class="block px-4 py-2 hover:bg-gray-200 whitespace-nowrap"
          >
            {{ sub.name }}
          </Link>
        </div>
      </div>
    </div>
  </div>
</template>
