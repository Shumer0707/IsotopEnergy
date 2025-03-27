<template>
    <footer class="bg-gray-900 text-white p-4 text-center">
      <nav class="flex justify-center space-x-4">
        <Link href="/" class="px-2">Главная</Link>
        <Link href="/about" class="px-2">О нас</Link>
        <Link href="/contacts" class="px-2">Контакты</Link>
        <Link href="/cart" class="px-2">Корзина</Link>
        <Link href="/admin/dashboard" class="px-2">Кабинет</Link>
      </nav>
      <div class="mt-2">&copy; 2025 IsotopEnergy</div>
    </footer>
  </template>

  <script setup>
  import { Link } from '@inertiajs/vue3'
  </script>
