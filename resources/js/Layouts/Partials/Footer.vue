<template>
  <footer class="bg-gray-900 text-white px-4 sm:px-6 py-10">
    <!-- Верхняя часть -->
    <div class="max-w-7xl mx-auto grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
      <!-- Левая колонка: Логотип и описание -->
      <div>
        <div class="flex justify-center p-4">
          <img :src="`/storage/logo/logo-1.png`" alt="Logo" class="h-auto min-w-[100px] sm:min-w-[140px] lg:sm:min-w-[180px]" />
        </div>
        <p class="text-sm text-gray-400">
          ISOTOP ENERGY – это современный интернет-магазин, предоставляющий широкий выбор специализированных фасадных и кровельных
          материалов.
        </p>
      </div>

      <!-- Навигация -->
      <div class="flex flex-col items-center py-4 lg:p-4">
        <h3 class="font-semibold mb-2 text-center">Навигация</h3>
        <ul class="space-y-1 text-sm text-gray-300 text-left">
          <li><Link href="/about">О компании</Link></li>
          <li><Link href="/payment">Оплата</Link></li>
          <li><Link href="/delivery">Доставка</Link></li>
          <li><Link href="/contacts">Контакты</Link></li>
        </ul>
      </div>

      <!-- Основные товары -->
      <div class="flex flex-col items-center py-4 lg:p-4">
        <h3 class="font-semibold mb-2 text-center">Основные товары</h3>
        <ul class="space-y-1 text-sm text-gray-300 text-left">
          <li><Link href="/catalog/siding">Сайдинг</Link></li>
          <li><Link href="/catalog/panels">Сэндвич панели</Link></li>
          <li><Link href="/catalog/plaster">Штукатурка</Link></li>
          <li><Link href="/catalog/drainage">Водосточная система</Link></li>
          <li><Link href="/catalog/facade">Фасадные панели</Link></li>
          <li><Link href="/catalog/insulation">Утепление</Link></li>
        </ul>
      </div>

      <!-- Контакты -->
      <div class="flex flex-col items-center py-4 lg:p-4">
        <h3 class="font-semibold mb-2 text-center">Свяжитесь с нами</h3>
        <ul class="text-sm text-gray-300 space-y-1 text-left">
          <li>Бельцы, ул. Улица 90</li>
          <li>+373 699 77 771</li>
          <li>email@gmail.com</li>
        </ul>
      </div>
    </div>

    <!-- Нижняя часть -->
    <div
      class="max-w-7xl mx-auto mt-10 flex flex-col md:flex-row items-center justify-between border-t border-gray-700 pt-6 text-sm text-gray-400 px-4 sm:px-0"
    >
      <div>2025. Все права защищены.</div>
      <div class="flex space-x-4 my-4 md:my-0">
        <span>📘</span>
        <span>💬</span>
        <span>✈️</span>
      </div>
      <div class="flex space-x-4">
        <span>VISA</span>
        <span>💳</span>
      </div>
    </div>
  </footer>
</template>

<script setup>
  import { Link } from '@inertiajs/vue3'
</script>
