<template>
  <div class="mix-h-screen min-h-60 flex flex-col items-center justify-center text-center">
    <h1 class="text-6xl font-bold text-pink-600 mb-4">404</h1>
    <p class="text-xl text-gray-600 mb-6">{{ t['404_title'] }}</p>
    <Link href="/" class="text-blue-600 hover:underline">{{ t['404_btn'] }}</Link>
  </div>
</template>

<script setup>
  import { Link } from '@inertiajs/vue3'
  import { useTranslations } from '@/composables/useTranslations'

  const t = useTranslations()
</script>
