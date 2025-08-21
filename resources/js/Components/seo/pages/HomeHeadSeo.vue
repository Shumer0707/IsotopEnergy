<script setup>
  import { Head, usePage } from '@inertiajs/vue3'
  import { computed } from 'vue'
  import { useTranslations } from '@/composables/useTranslations'
  import SeoJSONLD from '@/Components/seo/SeoJSONLD.vue' // уже сделали ранее

  const t = useTranslations()
  const page = usePage()
  const locale = computed(() => page.props.locale)

  const siteUrl = typeof window !== 'undefined' ? window.location.origin : ''
  const path = typeof window !== 'undefined' ? window.location.pathname : '/'
  const href = typeof window !== 'undefined' ? window.location.href : ''

  // hreflang
  const ruPath = computed(() => path.replace(/^\/ro(\/|$)/, '/'))
  const roPath = computed(() =>
    ruPath.value === '/' ? '/ro' : '/ro' + (ruPath.value.endsWith('/') ? ruPath.value.slice(0, -1) : ruPath.value)
  )
  const canonical = computed(() => siteUrl + path)
  const hrefLang = (lang) => (lang === 'ro' ? siteUrl + roPath.value : siteUrl + ruPath.value)

  // JSON-LD: HomePage + оффер доставки
  const homeGraph = computed(() => [
    {
      '@type': 'HomePage',
      '@id': href + '#webpage',
      url: href,
      name: t.value['h1-title'] ?? 'Термопанели и архитектурный декор в Молдове',
      isPartOf: { '@id': siteUrl + '#website' },
      inLanguage: t.value?.__locale ?? (locale.value || 'ru'),
      dateModified: new Date().toISOString().split('T')[0],
    },
    {
      '@type': 'Offer',
      '@id': siteUrl + '#free-delivery',
      name: t.value?.free_delivery ?? 'Доставка бесплатно от 60 м² термопанелей',
      areaServed: 'MD',
      eligibleQuantity: { '@type': 'QuantitativeValue', minValue: 60, unitCode: 'MTK' },
      seller: { '@id': siteUrl + '#org' },
    },
  ])
</script>

<template>
  <Head>
    <title>{{ t['home_meta_title'] }}</title>
    <meta name="description" :content="t['home_meta_description']" />

    <!-- canonical + hreflang -->
    <link rel="canonical" :href="canonical" />
    <link rel="alternate" hreflang="ru" :href="hrefLang('ru')" />
    <link rel="alternate" hreflang="ro" :href="hrefLang('ro')" />
    <link rel="alternate" hreflang="x-default" :href="hrefLang('ru')" />

    <!-- OG -->
    <meta property="og:type" content="website" />
    <meta property="og:url" :content="canonical" />
    <meta property="og:title" :content="t['home_meta_title']" />
    <meta property="og:description" :content="t['home_meta_description']" />
  </Head>

  <!-- JSON-LD в <head> добавит наш инжектор -->
  <SeoJSONLD :id="`${locale}-home`" :graph="homeGraph" />
</template>
