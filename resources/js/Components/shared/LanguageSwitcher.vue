<script setup>
  import { ref, onMounted, onBeforeUnmount } from 'vue'
  import { useLayoutStore } from '@/Stores/layout'
  import { router } from '@inertiajs/vue3'
  import { useTranslations } from '@/composables/useTranslations'

  const t = useTranslations()
  const layout = useLayoutStore()

  const showLangs = ref(false)
  const langButtonRef = ref(null)

  const updateOffset = () => {
    if (langButtonRef.value) {
      const rect = langButtonRef.value.getBoundingClientRect()
      layout.setLangButtonRightOffset(window.innerWidth - rect.right)
    }
  }

  const toggleLangs = () => {
    showLangs.value = !showLangs.value
  }

  const changeLocale = (lang) => {
    router.visit(route('set-locale', lang), {
      method: 'get',
      preserveScroll: true,
    })
    showLangs.value = false
  }

  onMounted(() => {
    updateOffset()
    window.addEventListener('resize', updateOffset)
  })

  onBeforeUnmount(() => {
    window.removeEventListener('resize', updateOffset)
  })
</script>

<template>
  <div v-if="showLangs" class="fixed inset-0 z-40" @click="showLangs = false"></div>

  <button ref="langButtonRef" @click="toggleLangs" class="flex flex-row items-center gap-2 text-2xl hover:text-gray-300">
    <font-awesome-icon icon="globe" class="lg:text-2xl text-xl" />
    <span class="text-lg">{{ t['lang'] }}</span>
    <font-awesome-icon icon="chevron-down" class="text-xs" />
  </button>

  <div
    v-if="showLangs"
    class="absolute z-50 bg-more text-white rounded shadow-md py-1 w-24 text-center"
    :style="{ right: `${layout.langButtonRightOffset}px`, top: `${layout.headerBottom}px` }"
  >
    <button @click="changeLocale('ro')" class="block w-full px-2 py-1 hover:bg-main">RO</button>
    <button @click="changeLocale('ru')" class="block w-full px-2 py-1 hover:bg-main">RU</button>
    <button @click="changeLocale('en')" class="block w-full px-2 py-1 hover:bg-main">EN</button>
  </div>
</template>
