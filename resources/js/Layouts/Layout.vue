<template>
  <div class="min-h-screen flex flex-col">
    <Header />

    <main class="flex-1 p-4 w-full">
      <Breadcrumbs v-if="usePage().url !== '/'" :segments="usePage().url.split('/').filter(Boolean)" />
      <slot />
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

  import { onMounted, computed, watch } from 'vue'
  import { usePage } from '@inertiajs/vue3'
  import { useCartStore } from '@/Stores/cart'
  import SubcategoryModal from '@/Components/shared/SubcategoryModal.vue'
  import { useCategoryStore } from '@/Stores/category'

  const categoryStore = useCategoryStore()
  const cart = useCartStore()
  const page = usePage()
  const locale = computed(() => page.props.locale)
  const { cartFromServer } = page.props

  onMounted(() => {
    cart.set(cartFromServer)
    categoryStore.reset()
    categoryStore.loadCategories()
  })

  // реактивно следим за сменой языка
  watch(locale, () => {
    categoryStore.reset()
    categoryStore.loadCategories()
  })
</script>
