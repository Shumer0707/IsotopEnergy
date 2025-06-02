<template>
  <div class="bg-more mx-auto py-4 text-white">
    <h3 class="text-2xl font-semibold mb-6 text-center">{{ t['advantages'] }}</h3>
    <div class="grid grid-cols-2 md:grid-cols-4 gap-6 text-center md:divide-x-2 lg:divide-x-4 divide-gray-300">
      <div v-for="(advantage, index) in advantages" :key="index" class="flex flex-col items-center text-center py-10">
        <img :src="advantage.image" alt="" class="mb-2" />
        <p class="text-lg font-semibold">{{ advantage.title }}</p>
        <p class="text-sm">{{ advantage.text }}</p>
      </div>
    </div>
  </div>
</template>

<script setup>
  import { useTranslations } from '@/composables/useTranslations'

  const t = useTranslations()
  const advantages = [
    {
      title: t.value.advantages_title_1,
      text: t.value.advantages_text_1,
      image: '/storage/varia/group-15.webp',
    },
    {
      title: t.value.advantages_title_2,
      text: t.value.advantages_text_2,
      image: '/storage/varia/group-16.webp',
    },
    {
      title: t.value.advantages_title_3,
      text: t.value.advantages_text_3,
      image: '/storage/varia/group-17.webp',
    },
    {
      title: t.value.advantages_title_4,
      text: t.value.advantages_text_4,
      image: '/storage/varia/group-18.webp',
    },
  ]
</script>
