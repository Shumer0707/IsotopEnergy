<script setup>
  import { ref, computed, onMounted, onBeforeUnmount } from 'vue'
  import { Link, usePage } from '@inertiajs/vue3'
  import { useCategoryStore } from '@/Stores/category'

  const categoryStore = useCategoryStore()
  const showCategories = ref(false)
  const navRef = ref(null)
  const navButtonRef = ref(null)
  const menuTop = ref(0)

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

    if (navButtonRef.value) {
      const rect = navButtonRef.value.getBoundingClientRect()
      menuTop.value = rect.bottom + 24
    }
  })

  onBeforeUnmount(() => {
    document.removeEventListener('click', handleClickOutside)
  })
</script>

<template>
  <div ref="navRef" class="relative">
    <button
      ref="navButtonRef"
      @click="toggleCategories"
      class="flex items-center gap-4 whitespace-nowrap bg-bg_sc hover:bg-pink-500 text-white px-6 sm:px-8 py-2 sm:py-3 rounded-xl text-sm lg:text-base"
    >
      <span>Все категории</span>
      <font-awesome-icon icon="chevron-down" class="text-xs" />
    </button>

    <div
      v-if="showCategories"
      :style="{ top: `${menuTop}px` }"
      class="fixed left-0 bg-white text-gray-800 shadow-xl z-50 transition-all duration-300 px-4 py-2"
    >
      <div v-for="category in categories" :key="category.id" class="relative group">
        <button class="block w-full px-4 py-2 text-left hover:bg-gray-200" @click.prevent="openSubcategories(category)">
          {{ category.name }}
        </button>
        <div
          class="absolute left-full top-0 mt-0 max-h-0 w-0 bg-white shadow-md opacity-0 group-hover:opacity-100 transition-all duration-300 overflow-hidden group-hover:max-h-[400px] group-hover:w-64"
        >
          <Link
            v-for="sub in category.children"
            :key="sub.id"
            :href="`/category/${sub.id}`"
            class="block px-4 py-2 hover:bg-gray-200"
          >
            {{ sub.name }}
          </Link>
        </div>
      </div>
    </div>
  </div>
</template>
