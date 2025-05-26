<template>
  <div class="bg-more mx-auto py-4 text-white">
    <h3 class="text-2xl font-semibold mb-6 text-center">Наши преимущества</h3>
    <div class="grid grid-cols-2 md:grid-cols-4 gap-6 text-center md:divide-x-2 lg:divide-x-4 divide-gray-300">
      <div v-for="(advantage, index) in advantages" :key="index" class="flex flex-col items-center text-center py-10 ">
          <img :src="advantage.image" alt="" class="mb-2" />
          <p class="text-lg font-semibold">{{ advantage.title }}</p>
          <p class="text-sm ">{{ advantage.text }}</p>
      </div>
    </div>
  </div>
</template>

<script setup>
const advantages = [
    {
      title: 'ДОСТУПНЫЕ ЦЕНЫ',
      text: 'Качество, доступное для всех.',
      image: '/storage/varia/group-15.webp',
    },
    {
      title: 'ПРОВЕРЕННЫЕ БРЕНДЫ',
      text: 'Проверено нами, проверено вами.',
      image: '/storage/varia/group-16.webp',
    },
    {
      title: 'ДОСТАВКА ПО МОЛДОВЕ',
      text: 'По всем городам и сёлам.',
      image: '/storage/varia/group-17.webp',
    },
    {
      title: 'ШИРОКИЙ ВЫБОР',
      text: 'Ваш идеальный выбор - здесь.',
      image: '/storage/varia/group-18.webp',
    },
  ]
</script>
