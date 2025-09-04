<script setup>
  import { ref, computed } from 'vue'
  import { Link } from '@inertiajs/vue3'
  import OverlayLayer from '@/Components/common/OverlayLayer.vue'
  import { useClickOutside } from '@/composables/useClickOutside'
  import { useKeyboardShortcuts } from '@/composables/useKeyboardShortcuts'
  import { useCategoryStore } from '@/Stores/category'

  const categoryStore = useCategoryStore()

  // Убираем дублирование - используем либо проп, либо стор
  const props = defineProps({
    category: Object,
    buttonRef: Object,
  })

  // Используем категорию из пропсов, если она есть, иначе из стора
  const category = computed(() => props.category || categoryStore.activeCategory)

  const emit = defineEmits(['close'])
  const modalRef = ref(null)

  useClickOutside(
    modalRef,
    () => {
      categoryStore.closeCategory()
      emit('close')
    },
    props.buttonRef
  )

  useKeyboardShortcuts({
    Escape: () => {
      categoryStore.closeCategory()
      emit('close')
    },
  })

  const handleOverlayClick = () => {
    categoryStore.closeCategory()
    emit('close')
  }
</script>

<template>
  <template v-if="category">
    <transition
      name="fade"
      enter-active-class="transition-opacity duration-300"
      leave-active-class="transition-opacity duration-200"
      enter-from-class="opacity-0"
      leave-to-class="opacity-0"
      enter-to-class="opacity-100"
      leave-from-class="opacity-100"
    >
      <div
        v-show="category"
        class="fixed inset-0 flex justify-center items-center z-50"
        @wheel.prevent
        @touchmove.prevent
        @scroll.prevent
      >
        <!-- Затемнение -->
        <OverlayLayer :show="true" @click="handleOverlayClick" />

        <!-- Модалка -->
        <div ref="modalRef" class="bg-white rounded-xl p-6 w-full max-w-4xl shadow-lg relative z-50 shadow-xl border border-more">
          <button @click="handleOverlayClick" class="absolute top-2 right-2 text-gray-500 hover:text-black text-xl">
            &times;
          </button>

          <h2 class="text-black text-xl font-semibold mb-4 text-center">
            {{ category.name }}
          </h2>

          <div class="text-black grid grid-cols-3 sm:grid-cols-4 md:grid-cols-6 gap-4">
            <Link
              v-for="sub in category.children"
              :key="sub.id"
              :href="`/${$page.props.locale}/category/${sub.id}`"
              class="flex flex-col items-center text-center hover:bg-gray-100 p-2 rounded"
              @click="handleOverlayClick"
            >
              <img
                :src="sub.logo ? `/storage/${sub.logo}` : '/images/placeholder.jpg'"
                :alt="sub.name"
                class="w-16 h-16 object-contain bg-white rounded-md"
              />
              <span class="text-sm mt-2">{{ sub.name }}</span>
            </Link>
          </div>
        </div>
      </div>
    </transition>
  </template>
</template>
