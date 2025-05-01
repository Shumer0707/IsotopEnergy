<template>
    <Head>
        <title>IsotopEnergy — Стройматериалы в Оргееве</title>
        <meta name="description" content="Интернет-магазин стройматериалов: цемент, клей, блоки, утеплитель. Скидки, акции, доставка по Оргееву и всей Молдове." />
    </Head>

    <!-- 🔹 Hero Section -->
    <section class="bg-gray-100">
        <div class="mx-auto">
            <PromoSlider />
        </div>
    </section>

    <!-- 🔹 Категории -->
    <section class="py-10">
    <div class="mx-auto px-4">
        <h3 class="text-2xl font-semibold mb-6 text-center">Каталог</h3>
        <CategoryGrid />
    </div>
    </section>

    <!-- 🔹 Преимущества -->
    <section class="bg-gray-50 py-10">
      <div class="mx-auto px-4">
        <h3 class="text-xl font-semibold mb-6 text-center ">Наши преимущества</h3>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6 text-center divide-x-4 divide-gray-300">
          <div v-for="i in 4" :key="i">
            <div class="w-36 h-36 bg-gray-300 rounded-full mx-auto"></div>
            <p class="mt-2 text-md">ДОСТУПНЫЕ ЦЕНЫ</p>
            <p class="mt-2 text-sm">Короткий слоган или описание преимушества</p>
          </div>
        </div>
      </div>
    </section>

    <!-- 🔹 Акционные товары -->
    <section class="py-10">
        <div class="max-w-7xl mx-auto px-4">
            <h3 class="text-xl font-semibold mb-6 text-center">Товары на акции</h3>
            <PromoProducts />
        </div>
    </section>


    <!-- 🔹 Партнёры -->
    <section class="bg-gray-100 py-10">
        <div class="max-w-7xl mx-auto px-4 text-center">
            <h3 class="text-xl font-semibold mb-6">Наши партнёры</h3>
            <PartnersCarousel />
        </div>
    </section>
  </template>

  <script setup>
  import { Head } from '@inertiajs/vue3';
  import CategoryGrid from '@/Components/shared/CategoryGrid.vue';
  import PromoSlider from '@/Components/shared/PromoSlider.vue';
  import PartnersCarousel from '@/Components/shared/PartnersCarousel.vue';
  import PromoProducts from '@/Components/shared/PromoProducts.vue';
  </script>
