<script setup>
  import { Head, usePage } from '@inertiajs/vue3'
  import { computed } from 'vue'
  import { useTranslations } from '@/composables/useTranslations'
  import SeoJSONLD from '@/Components/seo/SeoJSONLD.vue'

  const t = useTranslations()
  const page = usePage()
  const locale = computed(() => page.props.locale)

  const siteUrl = typeof window !== 'undefined' ? window.location.origin : ''
  const pathname = typeof window !== 'undefined' ? window.location.pathname : '/'
  const search = typeof window !== 'undefined' ? window.location.search : ''
  const href = typeof window !== 'undefined' ? window.location.href : ''

  // локаль в URL
  const supported = ['ru', 'ro']
  const parts = pathname.split('/').filter(Boolean)
  const hasLocale = parts.length && supported.includes(parts[0])

  // базовый путь БЕЗ префикса языка
  const basePath = '/' + (hasLocale ? parts.slice(1).join('/') : parts.join('/'))
  const normalizedBase = basePath === '//' || basePath === '' ? '/' : basePath

  // canonical + hreflang
  const canonical = computed(() => siteUrl + pathname + search)
  const altRu = computed(() => siteUrl + (normalizedBase === '/' ? '/ru' : '/ru' + normalizedBase))
  const altRo = computed(() => siteUrl + (normalizedBase === '/' ? '/ro' : '/ro' + normalizedBase))

  // координаты производства (Vasile Lupu 191)
  const productionGeo = { lat: 47.402578, lng: 28.817316 }

  // JSON-LD: ContactPage + LocalBusiness
  const contactGraph = computed(() => [
    {
      '@type': 'ContactPage',
      '@id': href + '#webpage',
      url: href,
      name: t.value.contacts ?? 'Контакты',
      isPartOf: { '@id': siteUrl + '#website' },
      inLanguage: locale.value || 'ru',
      dateModified: new Date().toISOString().split('T')[0],
    },
    {
      '@type': 'LocalBusiness',
      '@id': siteUrl + '#production',
      telephone: '+37360838688',
      openingHoursSpecification: [
        {
          '@type': 'OpeningHoursSpecification',
          dayOfWeek: ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'],
          opens: '09:00',
          closes: '20:00',
        },
        { '@type': 'OpeningHoursSpecification', dayOfWeek: ['Saturday'], opens: '09:00', closes: '18:00' },
      ],
      geo: { '@type': 'GeoCoordinates', latitude: productionGeo.lat, longitude: productionGeo.lng },
    },
    {
      '@type': 'LocalBusiness',
      '@id': siteUrl + '#showroom',
      telephone: '+37360838688',
      openingHoursSpecification: [
        {
          '@type': 'OpeningHoursSpecification',
          dayOfWeek: ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'],
          opens: '09:00',
          closes: '20:00',
        },
        { '@type': 'OpeningHoursSpecification', dayOfWeek: ['Saturday'], opens: '09:00', closes: '18:00' },
      ],
    },
  ])
</script>

<template>
  <Head>
    <title>{{ t['contacts_meta_title'] }}</title>
    <meta name="description" :content="t['contacts_meta_description']" />

    <!-- canonical -->
    <link rel="canonical" :href="canonical" />

    <!-- hreflang только если есть /ru или /ro в пути -->
    <template v-if="hasLocale">
      <link rel="alternate" hreflang="ru" :href="altRu" />
      <link rel="alternate" hreflang="ro" :href="altRo" />
      <link rel="alternate" hreflang="x-default" :href="altRu" />
    </template>

    <!-- OG -->
    <meta property="og:type" content="website" />
    <meta property="og:url" :content="canonical" />
    <meta property="og:title" :content="t['contacts_meta_title']" />
    <meta property="og:description" :content="t['contacts_meta_description']" />
    <meta property="og:locale" :content="hasLocale ? parts[0] : 'ru'" />
  </Head>

  <SeoJSONLD :id="`${locale}-contacts`" :graph="contactGraph" />
</template>
