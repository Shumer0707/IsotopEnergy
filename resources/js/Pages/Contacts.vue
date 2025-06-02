<template>
  <Head title="Контакты" />

  <!-- Хлебные крошки уже есть выше -->

  <section class="max-w-7xl mx-auto px-4 py-12">
    <h2 class="text-2xl font-bold mb-4">{{ t['contact_title'] }}</h2>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-12">
      <div
        class="bg-white shadow-md border border-gray-200 rounded-xl p-6 space-y-6 text-base text-gray-800 leading-6 min-h-[300px] h-full"
      >
        <!-- Адрес -->
        <p class="flex items-start gap-3">
          <font-awesome-icon icon="fa-solid fa-location-dot" class="text-gray-600 mt-1 shrink-0" />
          <span>
            <span class="font-semibold">{{ t['adress_title'] }}</span>
            <br />
            {{ t['adress_text'] }}
          </span>
        </p>

        <!-- График -->
        <p class="flex items-start gap-3">
          <font-awesome-icon icon="fa-solid fa-clock" class="text-gray-600 mt-1 shrink-0" />
          <span>
            <span class="font-semibold">{{ t['contact_schedule'] }}</span>
            <br />
            {{ t['contact_schedule_1'] }}: 9:00–20:00
            <br />
            {{ t['contact_schedule_2'] }}: 9:00–18:00
            <br />
            {{ t['contact_schedule_3'] }}
          </span>
        </p>

        <!-- Телефон -->
        <p class="flex items-start gap-3">
          <font-awesome-icon icon="fa-solid fa-phone" class="text-gray-600 mt-1 shrink-0" />
          <span>
            <span class="font-semibold">{{ t['contact_phone'] }}</span>
            <br />
            +373 699 77 777
          </span>
        </p>

        <!-- Почта -->
        <p class="flex items-start gap-3">
          <font-awesome-icon icon="fa-solid fa-envelope" class="text-gray-600 mt-1 shrink-0" />
          <span>
            <span class="font-semibold">{{ t['contact_mail'] }}</span>
            <br />
            isotopenergy@gmail.com
          </span>
        </p>

        <!-- Социальные сети -->
        <div class="pt-2 border-t border-gray-200">
          <p class="font-semibold mb-2">{{ t['contact_soc'] }}</p>
          <div class="flex gap-4 text-xl text-gray-600">
            <a href="https://www.tiktok.com" target="_blank" class="hover:text-black">
              <font-awesome-icon :icon="['fab', 'tiktok']" />
            </a>
            <a href="https://www.instagram.com" target="_blank" class="hover:text-black">
              <font-awesome-icon :icon="['fab', 'instagram']" />
            </a>
            <a href="https://www.facebook.com" target="_blank" class="hover:text-black">
              <font-awesome-icon :icon="['fab', 'facebook']" />
            </a>
          </div>
        </div>
      </div>

      <div class="h-full">
        <div class="rounded-xl overflow-hidden shadow-md w-full h-full">
          <iframe
            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2682.136172755125!2d27.9274437922014!3d47.75941300317316!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x40cb672c9938c16b%3A0x80e2057e0c88a1!2zQWxpxZ9hciBOb3ZhaSAzLCBNRC0zMTAwLCBCxINsyJtpLCDQnNC-0LvQtNC-0LLQsA!5e0!3m2!1sru!2s!4v1747054732023!5m2!1sru!2s"
            width="100%"
            height="100%"
            style="border: 0"
            allowfullscreen=""
            loading="lazy"
            referrerpolicy="no-referrer-when-downgrade"
          ></iframe>
        </div>
      </div>
    </div>

    <ContactForm />
  </section>
</template>

<script setup>
  import ContactForm from '@/Components/shared/ContactForm.vue'
  import { Head } from '@inertiajs/vue3'
  import { useTranslations } from '@/composables/useTranslations'

  const t = useTranslations()
</script>
