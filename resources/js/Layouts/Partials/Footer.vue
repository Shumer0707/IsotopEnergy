<template>
    <footer class="bg-gray-900 text-white px-4 sm:px-6 py-10">
      <!-- Верхняя часть -->
      <div class="max-w-7xl mx-auto grid grid-cols-1 md:grid-cols-4 gap-8">
        <!-- Левая колонка: Логотип и описание -->
        <div>
          <h2 class="text-2xl font-bold mb-2">LOGO</h2>
          <p class="text-sm text-gray-400">
            ISOTOP ENERGY – это современный интернет-магазин, предоставляющий широкий выбор специализированных фасадных и кровельных материалов.
          </p>
        </div>

        <!-- Навигация -->
        <div>
          <h3 class="font-semibold mb-2">Навигация</h3>
          <ul class="space-y-1 text-sm text-gray-300">
            <li><Link href="/about">О компании</Link></li>
            <li><Link href="/payment">Оплата</Link></li>
            <li><Link href="/delivery">Доставка</Link></li>
            <li><Link href="/contacts">Контакты</Link></li>
          </ul>
        </div>

        <!-- Основные товары -->
        <div>
          <h3 class="font-semibold mb-2">Основные товары</h3>
          <ul class="space-y-1 text-sm text-gray-300">
            <li><Link href="/catalog/siding">Сайдинг</Link></li>
            <li><Link href="/catalog/panels">Сэндвич панели</Link></li>
            <li><Link href="/catalog/plaster">Штукатурка</Link></li>
            <li><Link href="/catalog/drainage">Водосточная система</Link></li>
            <li><Link href="/catalog/facade">Фасадные панели</Link></li>
            <li><Link href="/catalog/insulation">Утепление</Link></li>
          </ul>
        </div>

        <!-- Контакты -->
        <div>
          <h3 class="font-semibold mb-2">Свяжитесь с нами</h3>
          <ul class="text-sm text-gray-300 space-y-1">
            <li>Бельцы, ул. Улица 90</li>
            <li>+373 699 77 771</li>
            <li>email@gmail.com</li>
          </ul>
        </div>
      </div>

      <!-- Нижняя часть -->
      <div class="max-w-7xl mx-auto mt-10 flex flex-col md:flex-row items-center justify-between border-t border-gray-700 pt-6 text-sm text-gray-400 px-4 sm:px-0">
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
  import { Link } from '@inertiajs/vue3';
  </script>
