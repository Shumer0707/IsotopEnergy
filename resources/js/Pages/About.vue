<script setup>
  import { useTranslations } from '@/composables/useTranslations'
  import ContactForm from '@/Components/shared/ContactForm.vue'
  import PartnersCarousel from '@/Components/shared/PartnersCarousel.vue'
  import AboutHeadSeo from '@/Components/seo/pages/AboutHeadSeo.vue'

  const t = useTranslations()
</script>

<template>
  <!-- SEO -->
  <AboutHeadSeo />

  <!-- Контент -->
  <section class="max-w-7xl mx-auto px-4 py-12">
    <h1 class="font-heading text-2xl lg:text-3xl font-bold text-center mb-8">
      {{ t['about_title'] }}
    </h1>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-start">
      <!-- Текст -->
      <div class="space-y-4 leading-7 text-justify">
        <p>
          <strong>{{ t['about_text_strong_1'] }}</strong>
          {{ t['about_text_1'] }}
        </p>
        <p>{{ t['about_text_2'] }}</p>
        <p>{{ t['about_text_3'] }}</p>
        <p>
          <strong>{{ t['about_text_strong_4'] }}</strong>
          — {{ t['about_text_4'] }}
        </p>
        <p v-if="t['about_text_5']">{{ t['about_text_5'] }}</p>
        <p v-if="t['about_text_6']">{{ t['about_text_6'] }}</p>
      </div>

      <!-- Картинка -->
      <div>
        <img
          src="/storage/varia/about.webp"
          :alt="t['about_img_alt'] || 'IsotopEnergy — производство и шоу-рум'"
          class="rounded-xl w-full object-cover"
          loading="lazy"
          decoding="async"
        />
      </div>
    </div>
  </section>

  <!-- Форма -->
  <section class="bg-gray-50 py-12" aria-labelledby="contact-title">
    <h2 id="contact-title" class="sr-only">Связаться с IsotopEnergy</h2>
    <div class="max-w-7xl mx-auto px-4">
      <ContactForm />
    </div>
  </section>

  <!-- Партнёры (по желанию) -->
  <!--
  <section class="bg-gray-100 py-12">
    <div class="max-w-7xl mx-auto px-4 text-center">
      <h2 class="font-heading text-xl font-semibold mb-6">{{ t['partners_title'] }}</h2>
      <PartnersCarousel />
    </div>
  </section>
  -->
</template>
