<template>
  <footer class="bg-main text-my_white pt-10">
    <!-- Верхняя часть -->
    <div class="max-w-7xl mx-auto grid grid-cols-2 md:grid-cols-4 gap-8 text-center px-4 sm:px-6">
      <!-- Левая колонка: Логотип и описание -->
      <div>
        <div class="flex justify-center p-4">
          <img :src="`/storage/logo/logo.png`" alt="Logo" class="h-auto min-w-[100px] sm:min-w-[140px] lg:sm:min-w-[180px]" />
        </div>
        <p class="text-sm">
          {{ t['footer_text'] }}
        </p>
      </div>

      <!-- Навигация -->
      <div class="flex flex-col items-center py-4 lg:p-4">
        <h3 class="font-semibold mb-2 text-center">{{ t['footer_navigation'] }}</h3>
        <ul class="space-y-1 text-sm text-left">
          <li><Link href="/" class="hover:underline">{{ t['home'] }}</Link></li>
          <li><Link href="/about" class="hover:underline">{{ t['about'] }}</Link></li>
          <li><Link href="/contacts" class="hover:underline">{{ t['contacts'] }}</Link></li>
        </ul>
      </div>

      <!-- Основные товары -->
      <div class="flex flex-col items-center py-4 lg:p-4">
        <h3 class="font-semibold mb-2 text-center">{{ t['footer_main'] }}</h3>
        <ul class="space-y-1 text-sm text-left">
          <li><Link href="/catalog/siding">Сайдинг(Заглушка)</Link></li>
          <li><Link href="/catalog/panels">Сэндвич панели(Заглушка)</Link></li>
          <li><Link href="/catalog/plaster">Штукатурка(Заглушка)</Link></li>
          <li><Link href="/catalog/drainage">Водосточная система(Заглушка)</Link></li>
          <li><Link href="/catalog/facade">Фасадные панели(Заглушка)</Link></li>
          <li><Link href="/catalog/insulation">Утепление(Заглушка)</Link></li>
        </ul>
      </div>

      <!-- Контакты -->
      <div class="flex flex-col items-center py-4 lg:p-4">
        <h3 class="font-semibold mb-2 text-center">{{ t['footer_us'] }}</h3>
        <ul class="text-sm space-y-1 text-left">
          <li>{{ t['adress_text'] }}</li>
          <li>+373 699 77 771</li>
          <li>email@gmail.com</li>
        </ul>
        <!-- Соц. сети -->
        <div class="flex gap-4 mt-3 text-xl text-gray-600">
          <a href="https://www.tiktok.com" target="_blank" rel="noopener" class="hover:text-black">
            <font-awesome-icon :icon="['fab', 'tiktok']" />
          </a>
          <a href="https://www.instagram.com" target="_blank" rel="noopener" class="hover:text-black">
            <font-awesome-icon :icon="['fab', 'instagram']" />
          </a>
          <a href="https://www.facebook.com" target="_blank" rel="noopener" class="hover:text-black">
            <font-awesome-icon :icon="['fab', 'facebook']" />
          </a>
        </div>
      </div>
    </div>

    <!-- Нижняя часть -->
    <div class="max-w-8xl mt-10 bg-more">
      <div
        class="max-w-7xl mx-auto flex flex-col md:flex-row items-center justify-between border-t border-gray-700 py-6 text-sm text-gray-400 px-20"
      >
        <div>2025. {{ t['rights'] }}</div>
        <div class="flex space-x-4 my-4 md:my-0">
          <!-- <span>📘</span>
        <span>💬</span>
        <span>✈️</span> -->
        </div>
        <div class="flex space-x-4">
          <span>VISA</span>
          <span>Mastercard</span>
          <!-- <span>💳</span> -->
        </div>
      </div>
    </div>
  </footer>
</template>

<script setup>
  import { Link } from '@inertiajs/vue3'
  import { useTranslations } from '@/composables/useTranslations'

  const t = useTranslations()
</script>
