<script setup>
  import { ref, computed, onMounted, onBeforeUnmount, reactive } from 'vue'
  import { Link, usePage } from '@inertiajs/vue3'
  import { useCategoryStore } from '@/Stores/category'
  import { useLayoutStore } from '@/Stores/layout'
  import OverlayLayer from '@/Components/common/OverlayLayer.vue'
  import { useClickOutside } from '@/composables/useClickOutside'
  import { useKeyboardShortcuts } from '@/composables/useKeyboardShortcuts'
  import SubcategoryModal from '@/Components/shared/SubcategoryModal.vue'
  import { useTranslations } from '@/composables/useTranslations'

  const t = useTranslations()
  const layout = useLayoutStore()
  const categoryStore = useCategoryStore()
  const showCategories = ref(false)
  const navRef = ref(null)
  const categoryButtonRef = ref(null)
  const activeCategory = ref(null)
  const triggerRefs = reactive({})

  useKeyboardShortcuts({
    Escape: () => {
      if (showCategories.value) showCategories.value = false
      if (activeCategory.value) activeCategory.value = null
    },
  })

  useClickOutside(
    navRef,
    () => {
      showCategories.value = false
      activeCategory.value = null
    },
    categoryButtonRef
  )

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

  const locale = computed(() => usePage().props.locale)

  const toggleCategories = () => {
    showCategories.value = !showCategories.value

    if (!categoryStore.isLoaded && showCategories.value) {
      categoryStore.loadCategories()
    }
  }

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

  function openSubcategories(category) {
    requestAnimationFrame(() => {
      categoryStore.openCategory(category.id)
    })
  }
</script>

<template>
  <div ref="navRef" class="relative">
    <!-- Кнопка -->
    <button
      ref="categoryButtonRef"
      @click="toggleCategories"
      class="flex items-center gap-2 whitespace-nowrap bg-my_green hover:bg-my_green_op text-white px-4 sm:px-6 py-2 sm:py-3 rounded-xl text-sm lg:text-base"
    >
      <font-awesome-icon icon="chevron-down" class="text-xs" />
      <span>{{ t['btn_category'] }}</span>
    </button>

    <!-- Меню категорий через Teleport -->
    <Teleport to="body">
      <OverlayLayer v-model:show="showCategories" />

      <div
        :style="{ top: `${layout.headerBottom}px`, left: `${layout.categoryButtonLeftOffset}px` }"
        class="fixed z-40 transition-transform duration-300 ease-out"
        :class="showCategories ? 'translate-y-0' : '-translate-y-[120%]'"
      >
        <div class="px-4 py-2 bg-my_white rounded-b-md shadow-xl border-t-0 border border-more text-black">
          <div v-for="category in categories" :key="category.id" class="relative group">
            <button
              class="block w-full px-4 py-2 text-left hover:bg-gray-200 rounded-md"
              @click.prevent="openSubcategories(category)"
              :ref="(el) => (triggerRefs[category.id] = el)"
            >
              {{ category.name }}
            </button>
            <div
              class="absolute px-4 py-2 rounded-xl left-full top-0 bg-white shadow-md opacity-0 group-hover:opacity-100 transition-all duration-300 overflow-hidden max-h-0 group-hover:max-h-[400px] w-0 group-hover:min-w-max border border-more"
            >
              <Link
                v-for="sub in category.children"
                :key="sub.id"
                :href="`/category/${sub.id}`"
                class="block w-full px-4 py-2 text-left hover:bg-gray-200 rounded-md whitespace-nowrap"
              >
                {{ sub.name }}
              </Link>
            </div>
          </div>
        </div>
      </div>
    </Teleport>

    <SubcategoryModal
      v-if="categoryStore.activeCategory"
      :category="categoryStore.activeCategory"
      :button-ref="triggerRefs[categoryStore.activeCategory.id]"
      @close="categoryStore.closeCategory"
    />
  </div>
</template>
