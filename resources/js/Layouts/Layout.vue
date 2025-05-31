<template>
  <div class="min-h-screen flex flex-col">
    <Header />

    <main class="bg-my_white flex-1 w-full py-4 max-w-screen-3xl mx-auto relative">
      <!-- Загрузка поверх контента -->
      <transition name="fade">
        <div v-show="!isReady" class="flex justify-center items-center h-60 text-gray-400 absolute inset-0 z-10 bg-white">
          <svg class="animate-spin h-8 w-8 text-gray-400" viewBox="0 0 24 24" fill="none">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" />
          </svg>
        </div>
      </transition>

      <!-- Контент всегда в DOM -->
      <transition name="fade" appear>
        <div
          :class="{
            'opacity-0 pointer-events-none': !isReady,
            'opacity-100': isReady,
            'transition-opacity duration-500': true,
          }"
        >
          <Breadcrumbs v-if="usePage().url !== '/'" :segments="usePage().url.split('/').filter(Boolean)" />
          <slot />
        </div>
      </transition>
    </main>

    <Footer />
    <WhatsAppButton />
  </div>
</template>

<script setup>
  import Header from '@/Layouts/Partials/Header.vue'
  import Footer from '@/Layouts/Partials/Footer.vue'
  import WhatsAppButton from '@/Components/common/WhatsAppButton.vue'
  import Breadcrumbs from '@/Components/common/Breadcrumbs.vue'
 import PageLoaderWrapper from '@/Components/common/PageLoaderWrapper.vue'

  import { ref, onMounted, computed, watch } from 'vue'
  import { usePage } from '@inertiajs/vue3'
  import { useCartStore } from '@/Stores/cart'
  import { useCategoryStore } from '@/Stores/category'
  import { useInitialLoad } from '@/composables/useInitialLoad'

  const categoryStore = useCategoryStore()
  const cart = useCartStore()
  const page = usePage()
  const locale = computed(() => page.props.locale)
  const { cartFromServer } = page.props
  const { isReady, load } = useInitialLoad()

  onMounted(() => {
    load()
  })

  watch(locale, () => {
    load()
  })
</script>
