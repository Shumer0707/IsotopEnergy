<template>
  <div class="min-h-screen flex flex-col">
    <GlobalSchema />
    <Header />

    <main class="bg-my_white flex-1 w-full py-4 max-w-screen-3xl mx-auto">
      <PageLoaderWrapper :loading="!isReady">
        <Breadcrumbs v-if="usePage().url !== '/'" />
        <slot />
      </PageLoaderWrapper>
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
  // import PageLoaderWrapper from '@/Components/common/PageLoaderWrapper.vue'

  import { ref, onMounted, computed, watch } from 'vue'
  import { usePage } from '@inertiajs/vue3'
  import { useCartStore } from '@/Stores/cart'
  import { useCategoryStore } from '@/Stores/category'
  import { useInitialLoad } from '@/composables/useInitialLoad'
  import PageLoaderWrapper from '@/Components/common/PageLoaderWrapper.vue'
  import GlobalSchema from '@/Components/seo/GlobalSchema.vue'

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
