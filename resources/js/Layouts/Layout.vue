<template>
    <div class="min-h-screen flex flex-col">
      <header class="bg-gray-800 text-white p-4">
        <h1 class="text-xl">Мой сайт</h1>
        <nav>
          <Link href="/" class="px-2">Главная</Link>
          <Link href="/admin/dashboard" class="px-2">Кабинет</Link>
        </nav>
      </header>

      <main class="flex-1 p-4">
        <slot />
      </main>

      <footer class="bg-gray-900 text-white p-4 text-center">
        &copy; 2025 My Website
      </footer>
    </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
</script>
