<template>
  <Head>
    <title>IsotopEnergy — Стройматериалы в Оргееве</title>
    <meta
      name="description"
      content="Интернет-магазин стройматериалов: цемент, клей, блоки, утеплитель. Скидки, акции, доставка по Оргееву и всей Молдове."
    />
  </Head>

  <!-- 🔹 Hero Section -->
  <section>
    <div class="mx-auto">
      <PromoSlider />
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
  <section class="bg-gray-100 py-10">
    <div class="max-w-7xl mx-auto px-4 text-center">
      <h3 class="text-xl font-semibold mb-6">{{ t['partners_title'] }}</h3>
      <PartnersCarousel />
    </div>
  </section>
</template>

<script setup>
  import { Head } from '@inertiajs/vue3'
  import CategoryGrid from '@/Components/shared/CategoryGrid.vue'
  import PromoSlider from '@/Components/shared/PromoSlider.vue'
  import PartnersCarousel from '@/Components/shared/PartnersCarousel.vue'
  import PromoProducts from '@/Components/shared/PromoProducts.vue'
  import AdvantagesBlock from '@/Components/common/AdvantagesBlock.vue'
  import { useTranslations } from '@/composables/useTranslations'

  const t = useTranslations()
</script>
