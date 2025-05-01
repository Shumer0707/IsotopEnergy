<script setup>
// Пока без axios — мок-данные
const promoProducts = [
  {
    id: 1,
    name: 'Клей для плитки',
    short: 'Пояснение товара',
    price: '105.0 mdl',
  },
  {
    id: 2,
    name: 'Грунтовка',
    short: 'Хорошее сцепление',
    price: '89.5 mdl',
  },
  {
    id: 3,
    name: 'Шпатлевка',
    short: 'Финишная отделка',
    price: '72.3 mdl',
  },
  {
    id: 4,
    name: 'Гипс',
    short: 'Для выравнивания',
    price: '60.0 mdl',
  },
];
</script>

<template>
  <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-6">
    <div
      v-for="product in promoProducts"
      :key="product.id"
      class="bg-white shadow p-4 rounded-xl"
    >
      <div class="h-40 bg-gray-200 rounded mb-4"></div>
      <h4 class="font-semibold text-sm mb-1">{{ product.name }}</h4>
      <p class="text-xs text-gray-500">{{ product.short }}</p>
      <div class="mt-2 text-pink-600 font-bold">{{ product.price }}</div>
    </div>
  </div>
</template>
