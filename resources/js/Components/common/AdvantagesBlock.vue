<template>
  <section aria-labelledby="advantages-title" class="bg-more mx-auto py-8 text-white">
    <h2 id="advantages-title" class="text-2xl font-semibold mb-6 text-center">{{ t['advantages'] }}</h2>

    <div class="grid grid-cols-2 md:grid-cols-4 gap-6 text-center md:divide-x divide-my_white/10">
      <div v-for="(adv, i) in advantages" :key="i" class="flex flex-col items-center text-center py-8 px-4">
        <img :src="adv.image" :alt="adv.title" loading="lazy" decoding="async" class="w-16 h-16 mb-3" />
        <p class="text-lg font-semibold font-heading">{{ adv.title }}</p>
        <p class="text-sm text-my_white/80">{{ adv.text }}</p>
      </div>
    </div>
  </section>
</template>

<script setup>
  import { computed } from 'vue'
  import { useTranslations } from '@/composables/useTranslations'
  const t = useTranslations()

  const advantages = computed(() => [
    { title: t.value.advantages_title_1, text: t.value.advantages_text_1, image: '/storage/varia/group-15.webp' },
    { title: t.value.advantages_title_2, text: t.value.advantages_text_2, image: '/storage/varia/group-16.webp' },
    { title: t.value.advantages_title_3, text: t.value.advantages_text_3, image: '/storage/varia/group-17.webp' },
    { title: t.value.advantages_title_4, text: t.value.advantages_text_4, image: '/storage/varia/group-18.webp' },
  ])
</script>
