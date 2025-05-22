<template>
  <div class="mx-auto px-4">
    <h3 class="text-xl font-semibold mb-6 text-center">Наши преимущества</h3>
    <div class="grid grid-cols-2 md:grid-cols-4 gap-6 text-center divide-x-4 divide-gray-300">
      <div v-for="i in 4" :key="i">
        <div class="w-36 h-36 bg-gray-300 rounded-full mx-auto"></div>
        <p class="mt-2 text-md">ДОСТУПНЫЕ ЦЕНЫ</p>
        <p class="mt-2 text-sm">Короткий слоган или описание преимушества</p>
      </div>
    </div>
  </div>
</template>

<script setup></script>
