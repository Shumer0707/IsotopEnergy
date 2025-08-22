<template>
  <header ref="headerRef" class="sticky top-0 left-0 w-full z-50 text-white">
    <!-- Верхний бар -->
    <div
      class="bg-more text-sm sm:text-base lg:text-md py-3 px-4 sm:px-6 md:px-12 flex flex-row justify-between items-center gap-2 sm:gap-0 text-center sm:text-left"
    >
      <!-- Навигация -->
      <nav class="flex flex-wrap justify-center sm:justify-start gap-4 sm:gap-12">
        <Link :href="`/${$page.props.locale}/`" class="hover:underline">{{ t['home'] }}</Link>
        <Link :href="`/${$page.props.locale}/about`" class="hover:underline">{{ t['about'] }}</Link>
        <Link :href="`/${$page.props.locale}/contacts`" class="hover:underline">{{ t['contacts'] }}</Link>
      </nav>

      <!-- Телефон -->
      <div class="flex justify-center sm:justify-end items-center gap-2">
        <span class="sm:text-lg"><font-awesome-icon icon="phone" class="lg:text-lg text-md" /></span>
        <!-- 📞 -->
        <span class="lg:text-lg">+373 608 38 688</span>
      </div>
    </div>

    <!-- Основной хедер -->
    <div
      class="bg-main py-4 sm:py-6 px-4 sm:px-6 md:px-12 flex flex-col md:flex-row lg:justify-between lg:items-center gap-3 sm:gap-6"
    >
      <!-- Левая часть -->
      <div class="flex flex-row-reverse sm:flex-row items-center justify-between sm:justify-center gap-4 sm:gap-6">
        <Link href="/" class="hover:underline">
          <span class="block">
            <img :src="`/storage/logo/logo.png`" alt="Logo" class="h-9 w-auto sm:h-auto sm:min-w-[140px] lg:sm:min-w-[180px]" />
          </span>
        </Link>
        <CategoryNav />
      </div>

      <!-- Центр -->
      <div class="flex flex-col sm:flex-row items-center justify-center gap-3 w-full">
        <ProductSearch />
      </div>

      <!-- Правая часть -->
      <div class="flex justify-end items-center 2xl:gap-10 lg:gap-6 gap-4">
        <LanguageSwitcher />
        <div class="relative">
          <Link :href="`/${$page.props.locale}/favorites`" class="hover:text-gray-300">
            <font-awesome-icon :icon="['far', 'heart']" class="text-white hover:text-my_red lg:text-3xl text-2xl" />
          </Link>
          <span
            v-if="favoritesCount > 0"
            class="absolute -top-1.5 -left-3 bg-my_red text-white text-xs font-bold rounded-full w-5 h-5 flex items-center justify-center"
          >
            {{ favoritesCount }}
          </span>
        </div>
        <MiniCart />
      </div>
    </div>
  </header>
</template>

<script setup>
  import { Link, router } from '@inertiajs/vue3'
  import CategoryNav from '@/Components/shared/CategoryNav.vue'
  import { useCartMiniUiStore } from '@/Stores/cartMiniUi'
  import MiniCart from '@/Components/common/MiniCart.vue'
  import { ref, onMounted, computed } from 'vue'
  import { useLayoutStore } from '@/Stores/layout'
  import ProductSearch from '@/Components/common/ProductSearch.vue'
  import LanguageSwitcher from '@/Components/shared/LanguageSwitcher.vue'
  import { useFavoritesStore } from '@/Stores/favorites'
  import { useTranslations } from '@/composables/useTranslations'

  const t = useTranslations()
  const layout = useLayoutStore()
  const headerRef = ref(null)
  const cartMiniUi = useCartMiniUiStore()
  const showLangs = ref(false)

  const favorites = useFavoritesStore()
  const favoritesCount = computed(() => favorites.count)

  onMounted(() => {
    if (headerRef.value) {
      const rect = headerRef.value.getBoundingClientRect()
      layout.setHeaderBottom(rect.bottom)
    }
  })

  function changeLocale(lang) {
    router.visit(route('set-locale', lang), {
      method: 'get',
      preserveScroll: true,
    })
  }

  function toggleLangs() {
    showLangs.value = !showLangs.value
  }
</script>
