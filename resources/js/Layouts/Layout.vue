<template>
  <div class="min-h-screen flex flex-col">
    <Header />

    <main class="bg-my_white flex-1 w-full py-4 max-w-screen-3xl mx-auto">
      <div v-if="!isReady" class="flex justify-center items-center h-60 text-gray-400 animate-pulse">Загрузка...</div>

      <template v-else>
        <Breadcrumbs v-if="usePage().url !== '/'" :segments="usePage().url.split('/').filter(Boolean)" />
        <slot />
      </template>
    </main>

    <SubcategoryModal :category="categoryStore.activeCategory" @close="categoryStore.closeCategory" />
    <Footer />
    <WhatsAppButton />
  </div>
</template>

<script setup>
  import Header from '@/Layouts/Partials/Header.vue'
  import Footer from '@/Layouts/Partials/Footer.vue'
  import WhatsAppButton from '@/Components/common/WhatsAppButton.vue'
  import Breadcrumbs from '@/Components/common/Breadcrumbs.vue'

  import { ref, onMounted, computed, watch } from 'vue'
  import { usePage } from '@inertiajs/vue3'
  import { useCartStore } from '@/Stores/cart'
  import SubcategoryModal from '@/Components/shared/SubcategoryModal.vue'
  import { useCategoryStore } from '@/Stores/category'
  import { useInitialLoad } from '@/composables/useInitialLoad'

  const categoryStore = useCategoryStore()
  const cart = useCartStore()
  const page = usePage()
  const locale = computed(() => page.props.locale)
  const { cartFromServer } = page.props
  const { isReady, load } = useInitialLoad()

  onMounted(async () => {
    await load() // загружаем все нужные сторы

    cart.set(cartFromServer)

    if (Object.keys(cartFromServer).length > 0) {
      cart.loadProducts()
    }

    categoryStore.reset()
    categoryStore.loadCategories()
  })

  // реактивно следим за сменой языка
  watch(locale, () => {
    categoryStore.reset()
    categoryStore.loadCategories()
  })
</script>
