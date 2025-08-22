<script setup>
  import { ref, onMounted, watch, nextTick, onBeforeUnmount } from 'vue'
  import { useLayoutStore } from '@/Stores/layout'
  import { router } from '@inertiajs/vue3'
  import OverlayLayer from '@/Components/common/OverlayLayer.vue'
  import { useClickOutside } from '@/composables/useClickOutside'
  import { useKeyboardShortcuts } from '@/composables/useKeyboardShortcuts'
  import { useTranslations } from '@/composables/useTranslations'

  const t = useTranslations()
  const layout = useLayoutStore()

  const showLangs = ref(false)
  const langButtonRef = ref(null)
  const langMenuRef = ref(null)

  const updateOffset = () => {
    if (langButtonRef.value) {
      const rect = langButtonRef.value.getBoundingClientRect()
      layout.setLangButtonRightOffset(window.innerWidth - rect.right)
    }
  }

  onMounted(() => {
    nextTick(() => setTimeout(updateOffset, 0))
    window.addEventListener('resize', updateOffset)
  })

  onBeforeUnmount(() => {
    window.removeEventListener('resize', updateOffset)
  })

  // Закрытие по ESC
  useKeyboardShortcuts({
    Escape: () => {
      if (showLangs.value) showLangs.value = false
    },
  })

  // Клик вне
  useClickOutside(
    langMenuRef,
    () => {
      showLangs.value = false
    },
    langButtonRef
  )

  const toggleLangs = () => {
    showLangs.value = !showLangs.value
  }

  const changeLocale = (lang) => {
    const back = window.location.pathname + window.location.search
    window.location.href = route('set-locale', { locale: lang, back })
  }
</script>

<template>
  <button ref="langButtonRef" @click="toggleLangs" class="flex flex-row items-center gap-2 text-2xl hover:text-gray-300">
    <font-awesome-icon icon="globe" class="lg:text-2xl text-xl" />
    <span class="text-lg">{{ t['lang'] }}</span>
    <font-awesome-icon icon="chevron-down" class="text-xs" />
  </button>

  <Teleport to="body">
    <OverlayLayer v-model:show="showLangs" />

    <div
      ref="langMenuRef"
      class="fixed z-40 transition-transform duration-300 ease-out px-4 py-2 bg-my_white rounded-b-md shadow-xl border-t-0 border border-more text-black w-24 text-center"
      :style="{
        right: `${layout.langButtonRightOffset}px`,
        top: `${layout.headerBottom}px`,
      }"
      :class="showLangs ? 'translate-y-0' : '-translate-y-[120%]'"
    >
      <button @click="changeLocale('ro')" class="block w-full px-4 py-2 text-left hover:bg-gray-200 rounded-md whitespace-nowrap">
        RO
      </button>
      <button @click="changeLocale('ru')" class="block w-full px-4 py-2 text-left hover:bg-gray-200 rounded-md whitespace-nowrap">
        RU
      </button>
      <!-- <button @click="changeLocale('en')" class="block w-full px-4 py-2 text-left hover:bg-gray-200 rounded-md whitespace-nowrap">
        EN
      </button> -->
    </div>
  </Teleport>
</template>
