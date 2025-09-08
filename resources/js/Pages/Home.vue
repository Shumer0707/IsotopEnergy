<template>
  <HomeHeadSeo />

  <!-- 🔹 Hero Section -->
  <section>
    <div class="mx-auto">
      <PromoSlider />
    </div>
  </section>

  <section class="mt-4 py-12 px-4 max-w-4xl mx-auto text-center bg-my_white rounded-xl shadow-md">
    <h1 class="font-heading text-2xl lg:text-3xl font-bold text-my_black mb-4 border-b-4 border-my_green inline-block pb-2">
      {{ t['h1-title'] }}
    </h1>
    <p class="lg:text-lg text-md text-more">
      {{ t['h1-description'] }}
    </p>
    <div class="mt-6">
      <a
        :href="`/${$page.props.locale}/about`"
        class="bg-my_green hover:bg-my_green_op text-my_white px-6 py-3 rounded-lg font-semibold transition"
      >
        Подробнее о нас
      </a>
    </div>
  </section>

  <!-- 🔹 Категории -->
  <section class="py-10">
    <div class="max-w-7xl mx-auto px-4">
      <h3 class="text-2xl font-semibold mb-6 text-center">{{ t['catalog'] }}</h3>
      <CategoryGrid />
    </div>
  </section>

  <!-- 🔹 Преимущества -->
  <section>
    <AdvantagesBlock />
  </section>

  <!-- 🔹 Акционные товары -->
  <section class="py-10">
    <div class="max-w-7xl mx-auto px-4">
      <h3 class="text-xl font-semibold mb-6 text-center">{{ t['sale_title'] }}</h3>
      <PromoProducts />
    </div>
  </section>

  <!-- 🔹 Партнёры -->
  <!-- <section class="bg-gray-100 py-10">
    <div class="max-w-7xl mx-auto px-4 text-center">
      <h3 class="text-xl font-semibold mb-6">{{ t['partners_title'] }}</h3>
      <PartnersCarousel />
    </div>
  </section> -->
</template>

<script setup>
  import { Head } from '@inertiajs/vue3'
  import CategoryGrid from '@/Components/shared/CategoryGrid.vue'
  import PromoSlider from '@/Components/shared/PromoSlider.vue'
  import PartnersCarousel from '@/Components/shared/PartnersCarousel.vue'
  import PromoProducts from '@/Components/shared/PromoProducts.vue'
  import AdvantagesBlock from '@/Components/common/AdvantagesBlock.vue'
  import { useTranslations } from '@/composables/useTranslations'
  import HomeHeadSeo from '@/Components/seo/pages/HomeHeadSeo.vue'

  const t = useTranslations()
</script>
