<template>
  <ContactsHeadSeo />

  <section class="max-w-7xl mx-auto px-4 py-12">
    <h1 class="text-2xl lg:text-3xl font-bold mb-6">
      {{ t['contact_title'] }}
    </h1>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-12 items-stretch">
      <!-- Инфо-блок -->
      <div
        class="bg-white shadow-md border border-gray-200 rounded-xl p-6 space-y-6 text-base text-gray-800 leading-6 h-full flex flex-col min-h-[360px]"
      >
        <!-- Шоу-рум -->
        <div>
          <p class="font-semibold mb-1">{{ t['contact_showroom'] }}</p>
          <p class="flex items-start gap-3">
            <font-awesome-icon icon="fa-solid fa-location-dot" class="text-gray-600 mt-1 shrink-0" />
            <span>{{ showroomAddr }}</span>
          </p>
          <a
            :href="mapsAddrLink(showroomAddr)"
            target="_blank"
            rel="noopener noreferrer"
            class="text-main hover:underline text-sm"
          >
            {{ t['open_in_maps'] }}
          </a>
        </div>

        <!-- Производство -->
        <div class="pt-4 border-t border-gray-200">
          <p class="font-semibold mb-1">{{ t['contact_production'] }}</p>
          <p class="flex items-start gap-3">
            <font-awesome-icon icon="fa-solid fa-location-dot" class="text-gray-600 mt-1 shrink-0" />
            <span>str. Vasile Lupu 191, Orhei, Moldova</span>
          </p>
          <a
            :href="coordsLink(productionCoords)"
            target="_blank"
            rel="noopener noreferrer"
            class="text-main hover:underline text-sm"
          >
            {{ t['open_in_maps'] }}
          </a>
        </div>

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
            <a href="tel:+37360838688" class="hover:underline">+373 608 38 688</a>
          </span>
        </p>

        <!-- Почта -->
        <p class="flex items-start gap-3">
          <font-awesome-icon icon="fa-solid fa-envelope" class="text-gray-600 mt-1 shrink-0" />
          <span>
            <span class="font-semibold">{{ t['contact_mail'] }}</span>
            <br />
            <a href="mailto:isotopenergy@gmail.com" class="hover:underline">isotopenergy@gmail.com</a>
          </span>
        </p>

        <!-- Соцсети -->
        <div class="pt-2 border-t border-gray-200">
          <p class="font-semibold mb-2">{{ t['contact_soc'] }}</p>
          <div class="flex gap-4 text-xl text-gray-600">
            <a href="https://www.tiktok.com" target="_blank" rel="noopener noreferrer" class="hover:text-black">
              <font-awesome-icon :icon="['fab', 'tiktok']" />
            </a>
            <a href="https://www.instagram.com" target="_blank" rel="noopener noreferrer" class="hover:text-black">
              <font-awesome-icon :icon="['fab', 'instagram']" />
            </a>
            <a href="https://www.facebook.com" target="_blank" rel="noopener noreferrer" class="hover:text-black">
              <font-awesome-icon :icon="['fab', 'facebook']" />
            </a>
          </div>
        </div>
      </div>

      <!-- Карта с переключателем -->
      <div class="h-full flex flex-col">
        <div class="flex gap-2 mb-3">
          <button
            @click="active = 'showroom'"
            :class="[
              'px-3 py-2 rounded-lg text-sm font-medium transition',
              active === 'showroom' ? 'bg-my_green text-white' : 'bg-gray-200 text-gray-700',
            ]"
          >
            {{ t['contact_showroom'] }}
          </button>
          <button
            @click="active = 'production'"
            :class="[
              'px-3 py-2 rounded-lg text-sm font-medium transition',
              active === 'production' ? 'bg-my_green text-white' : 'bg-gray-200 text-gray-700',
            ]"
          >
            {{ t['contact_production'] }}
          </button>
        </div>

        <div class="rounded-xl overflow-hidden shadow-md w-full flex-1 min-h-[360px]">
          <iframe
            :src="mapSrc"
            class="w-full h-full block"
            style="border: 0"
            :title="active === 'showroom' ? t['map_title_showroom'] : t['map_title_production']"
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
  import { Head } from '@inertiajs/vue3'
  import { ref, computed } from 'vue'
  import ContactForm from '@/Components/shared/ContactForm.vue'
  import { useTranslations } from '@/composables/useTranslations'
  import ContactsHeadSeo from '@/Components/seo/pages/ContactsHeadSeo.vue'

  const t = useTranslations()

  // адрес шоу-рума строкой
  const showroomAddr = 'str. Unirii 51/e, Orhei, Moldova'

  // координаты производства (точка на str. Vasile Lupu 191)
  const productionCoords = { lat: 47.402578, lng: 28.817316 }

  // переключатель активной карты
  const active = ref('showroom')

  // helpers
  const mapsAddrLink = (addr) => `https://www.google.com/maps?q=${encodeURIComponent(addr)}`

  const mapsAddrEmbed = (addr) => `https://www.google.com/maps?q=${encodeURIComponent(addr)}&output=embed`

  const coordsEmbed = (coords, label = 'IsotopEnergy — producție', zoom = 18) =>
    `https://www.google.com/maps?q=${encodeURIComponent(`${coords.lat},${coords.lng} (${label})`)}&z=${zoom}&output=embed`

  const coordsLink = (coords) => `https://www.google.com/maps?q=${coords.lat},${coords.lng}`

  // текущий src для iframe (адрес для шоу-рума, координаты для производства)
  const mapSrc = computed(() =>
    active.value === 'showroom' ? mapsAddrEmbed(showroomAddr) : coordsEmbed(productionCoords, t.value['contact_production'])
  )
</script>
