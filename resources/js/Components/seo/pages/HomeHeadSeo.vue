<script setup>
  import { Head, usePage } from '@inertiajs/vue3'
  import { computed } from 'vue'
  import { useTranslations } from '@/composables/useTranslations'
  import SeoJSONLD from '@/Components/seo/SeoJSONLD.vue'
  import { useSeoLinks } from '@/composables/useSeoLinks'

  const t = useTranslations()
  const page = usePage()
  const locale = computed(() => page.props.locale)

  const { siteUrl, href, canonical, alternates, og, isLocalized } = useSeoLinks()

  // JSON-LD (как было)
  const homeGraph = computed(() => [
    {
      '@type': 'HomePage',
      '@id': href + '#webpage',
      url: href,
      name: t.value['h1-title'] ?? 'Термопанели и архитектурный декор в Молдове',
      isPartOf: { '@id': siteUrl + '#website' },
      inLanguage: locale.value || 'ru',
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

    <!-- canonical -->
    <link rel="canonical" :href="canonical" />

    <!-- hreflang (только если есть локализованный путь) -->
    <template v-if="isLocalized">
      <link rel="alternate" hreflang="ru" :href="alternates.ru" />
      <link rel="alternate" hreflang="ro" :href="alternates.ro" />
      <link rel="alternate" hreflang="x-default" :href="alternates.xDefault" />
    </template>

    <!-- OG -->
    <meta property="og:type" content="website" />
    <meta property="og:url" :content="og.url" />
    <meta property="og:title" :content="t['home_meta_title']" />
    <meta property="og:description" :content="t['home_meta_description']" />
    <!-- og:locale и alternate (необязательно, но можно оставить) -->
    <meta property="og:locale" :content="og.locale" />
    <meta v-for="alt in og.alternates" :key="alt" property="og:locale:alternate" :content="alt" />
  </Head>

  <SeoJSONLD :id="`${locale}-home`" :graph="homeGraph" />
</template>
