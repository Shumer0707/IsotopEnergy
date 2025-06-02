<template>
  <Head title="О нас" />

  <section class="max-w-7xl mx-auto px-4 py-12">
    <h1 class="text-2xl font-bold text-center mb-8">{{ t['about_title'] }}</h1>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-start">
      <!-- Текст -->
      <div class="space-y-4 leading-7 text-justify">
          <p>
            <strong>{{ t['about_text_strong_1'] }}</strong>
            {{ t['about_text_1'] }}
          </p>

          <p>
            {{ t['about_text_2'] }}
          </p>
          <p>
            {{ t['about_text_3'] }}
          </p>
          <p>
            <strong>{{ t['about_text_strong_4'] }}</strong>
            — {{ t['about_text_4'] }}
          </p>
      </div>

      <!-- Картинка -->
      <div>
        <img src='/storage/varia/about.webp' alt="О компании" class="rounded-xl w-full object-cover" />
      </div>
    </div>
  </section>
  <!-- 🔹 Форма -->
  <section class="bg-gray-50 py-12">
    <div class="max-w-7xl mx-auto px-4">
      <ContactForm />
    </div>
  </section>

  <!-- 🔹 Партнёры -->
  <section class="bg-gray-100 py-12">
    <div class="max-w-7xl mx-auto px-4 text-center">
      <h3 class="text-xl font-semibold mb-6">{{ t['partners_title'] }}</h3>
      <PartnersCarousel />
    </div>
  </section>
</template>

<script setup>
  import { Head } from '@inertiajs/vue3'
  import { useTranslations } from '@/composables/useTranslations'
  import ContactForm from '@/Components/shared/ContactForm.vue'
  import PartnersCarousel from '@/Components/shared/PartnersCarousel.vue'

  const t = useTranslations()
</script>
