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

  // Ховер-логика для подменю
  const hoveredId = ref(null)
  let hideTimer = null
  function onEnter(id) {
    if (hideTimer) clearTimeout(hideTimer)
    hoveredId.value = id
  }
  function onLeave() {
    hideTimer = setTimeout(() => (hoveredId.value = null), 120)
  }

  // Esc и клик вне области
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

  // Смещение меню относительно кнопки
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

  // Показ/скрытие списка категорий
  const toggleCategories = () => {
    showCategories.value = !showCategories.value
    if (!categoryStore.isLoaded && showCategories.value) {
      categoryStore.loadCategories()
    }
  }

  // Подготовка данных категорий
  const categories = computed(() => {
    return categoryStore.navCategories.map((category) => ({
      id: category.id,
      name: category.translation?.name ?? 'Без названия',
      logo: category.logo,
      children: (category.children || []).map((sub) => ({
        id: sub.id,
        name: sub.translation?.name ?? 'Без названия',
        logo: sub.logo,
      })),
    }))
  })

  // Открытие модалки подкатегорий
  function openSubcategories(category) {
    requestAnimationFrame(() => {
      categoryStore.openCategory(category.id)
    })
  }

  const isClosing = ref(false)

  function closeAfterClick() {
    isClosing.value = true // блокируем мышь
    hoveredId.value = null
    showCategories.value = false
    activeCategory.value = null
    setTimeout(() => (isClosing.value = false), 400) // после анимации
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

    <!-- Меню категорий (Teleport) -->
    <Teleport to="body">
      <OverlayLayer v-model:show="showCategories" />

      <div
        :style="{ top: `${layout.headerBottom}px`, left: `${layout.categoryButtonLeftOffset}px` }"
        class="fixed z-40 transition-transform duration-300 ease-out"
        :class="showCategories ? 'translate-y-0' : '-translate-y-[120%]'"
      >
        <div class="px-4 py-2 bg-my_white rounded-b-md shadow-xl border-t-0 border border-more text-black">
          <div
            v-for="category in categories"
            :key="category.id"
            class="relative"
            @mouseenter="onEnter(category.id)"
            @mouseleave="onLeave"
          >
            <button
              class="block w-full px-4 py-2 text-left hover:bg-gray-200 rounded-md"
              @click.prevent="openSubcategories(category)"
              :ref="(el) => (triggerRefs[category.id] = el)"
            >
              {{ category.name }}
            </button>

            <!-- Подменю -->
            <Transition
              enter-active-class="transition duration-150 ease-out"
              enter-from-class="opacity-0 scale-95"
              enter-to-class="opacity-100 scale-100"
              leave-active-class="transition duration-100 ease-in"
              leave-from-class="opacity-100 scale-100"
              leave-to-class="opacity-0 scale-95"
            >
              <div
                v-show="hoveredId === category.id"
                class="absolute left-full top-0 ml-2 z-50 px-4 py-2 rounded-xl bg-white shadow-md border border-more min-w-56 max-h-[70vh] overflow-auto pointer-events-auto"
                :class="{ 'pointer-events-none': isClosing }"
                @mouseenter="onEnter(category.id)"
                @mouseleave="onLeave"
              >
                <!-- Мостик, чтобы не было разрыва между пунктом и подменю -->
                <span class="absolute -left-2 top-0 h-full w-2"></span>

                <Link
                  v-for="sub in category.children"
                  :key="sub.id"
                  :href="`/${$page.props.locale}/category/${sub.id}`"
                  class="block w-full px-4 py-2 text-left hover:bg-gray-200 rounded-md whitespace-nowrap"
                  @click="closeAfterClick"
                >
                  {{ sub.name }}
                </Link>
              </div>
            </Transition>
          </div>
        </div>
      </div>
    </Teleport>

    <!-- Модалка подкатегорий (по клику) -->
    <SubcategoryModal
      v-if="categoryStore.activeCategory"
      :category="categoryStore.activeCategory"
      :button-ref="triggerRefs[categoryStore.activeCategory.id]"
      @close="categoryStore.closeCategory"
    />
  </div>
</template>
