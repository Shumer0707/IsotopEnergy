<template>
  <header ref="headerRef" class="sticky top-0 left-0 w-full z-50 text-white">
    <!-- Верхний бар -->
    <div
      class="bg-more text-sm sm:text-base lg:text-md py-3 px-4 sm:px-6 md:px-12 flex flex-row justify-between items-center gap-2 sm:gap-0 text-center sm:text-left"
    >
      <!-- Навигация -->
      <nav class="flex flex-wrap justify-center sm:justify-start gap-4 sm:gap-12">
        <Link href="/" class="hover:underline">Главная</Link>
        <Link href="/about" class="hover:underline">О нас</Link>
        <Link href="/contacts" class="hover:underline">Контакты</Link>
      </nav>

      <!-- Телефон -->
      <div class="flex justify-center sm:justify-end items-center gap-2">
        <span class="sm:text-lg"><font-awesome-icon icon="phone" class="lg:text-lg text-md" /></span>
        <!-- 📞 -->
        <span class="lg:text-lg">+373 699 77 771</span>
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
        <div class="relative w-full max-w-sm sm:max-w-md md:max-w-lg">
          <input
            type="text"
            placeholder="Поиск по товарам..."
            class="w-full pl-4 pr-10 py-1.5 sm:py-3 rounded-xl text-black placeholder-gray-500 focus:outline-none"
          />
          <button class="absolute right-2 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-gray-700 pr-2">
            <font-awesome-icon icon="magnifying-glass" class="lg:text-xl text-lg" />
            <!-- 🔍 -->
          </button>
        </div>
      </div>

      <!-- Правая часть -->
      <div class="flex justify-end items-center 2xl:gap-10 lg:gap-6 gap-4">
        <div class="relative">
          <button @click="toggleLangs" class="flex flex-row items-center gap-2 text-2xl hover:text-gray-300">
            <font-awesome-icon icon="globe" class="lg:text-2xl text-xl" />
            <span class="text-lg">{{ t['lang'] }}</span>
            <font-awesome-icon icon="chevron-down" class="text-xs" />
            <!-- 🌐 -->
          </button>

          <div v-if="showLangs" class="absolute z-50 mt-2 right-0 bg-white text-black rounded shadow-md py-1 w-24 text-center">
            <button @click="changeLocale('ro')" class="block w-full px-2 py-1 hover:bg-gray-100">RO</button>
            <button @click="changeLocale('ru')" class="block w-full px-2 py-1 hover:bg-gray-100">RU</button>
            <button @click="changeLocale('en')" class="block w-full px-2 py-1 hover:bg-gray-100">EN</button>
          </div>
        </div>
        <Link href="/favorites" class="hover:text-gray-300">
          <font-awesome-icon :icon="['far', 'heart']" class="text-white hover:text-pink-600 lg:text-3xl text-2xl" />
        </Link>
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
  import { ref, onMounted } from 'vue'
  import { useLayoutStore } from '@/Stores/layout'
  import { useTranslations } from '@/composables/useTranslations'

  const t = useTranslations()
  const layout = useLayoutStore()
  const headerRef = ref(null)
  const cartMiniUi = useCartMiniUiStore()
  const showLangs = ref(false)

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
