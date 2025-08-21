<script setup>
import { Head, usePage } from '@inertiajs/vue3'
import { computed } from 'vue'
import { useTranslations } from '@/composables/useTranslations'
import SeoJSONLD from '@/Components/seo/SeoJSONLD.vue'

const t = useTranslations()
const page = usePage()
const locale = computed(() => page.props.locale)

const siteUrl = typeof window !== 'undefined' ? window.location.origin  : ''
const path    = typeof window !== 'undefined' ? window.location.pathname : '/'
const href    = typeof window !== 'undefined' ? window.location.href     : ''

// hreflang + canonical
const ruPath = computed(() => path.replace(/^\/ro(\/|$)/, '/'))
const roPath = computed(() => (ruPath.value === '/' ? '/ro' : '/ro' + (ruPath.value.endsWith('/') ? ruPath.value.slice(0, -1) : ruPath.value)))
const canonical = computed(() => siteUrl + path)
const hrefLang  = (lang) => (lang === 'ro' ? siteUrl + roPath.value : siteUrl + ruPath.value)

// Координаты производства (Vasile Lupu 191)
const productionGeo = { lat: 47.402578, lng: 28.817316 }

// JSON-LD: ContactPage + обогащаем LocalBusiness узлы (телефон/часы/geo)
const contactGraph = computed(() => ([
  {
    '@type': 'ContactPage',
    '@id' : href + '#webpage',
    url  : href,
    name : t.value.contacts ?? 'Контакты',
    isPartOf: { '@id': siteUrl + '#website' },
    inLanguage : t.value?.__locale ?? (locale.value || 'ru'),
    dateModified: new Date().toISOString().split('T')[0],
  },
  {
    '@type': 'LocalBusiness',
    '@id': siteUrl + '#production',
    telephone: '+37360838688',
    openingHoursSpecification: [
      { '@type': 'OpeningHoursSpecification', dayOfWeek: ['Monday','Tuesday','Wednesday','Thursday','Friday'], opens: '09:00', closes: '20:00' },
      { '@type': 'OpeningHoursSpecification', dayOfWeek: ['Saturday'], opens: '09:00', closes: '18:00' }
      // воскресенье опущено как выходной
    ],
    geo: { '@type': 'GeoCoordinates', latitude: productionGeo.lat, longitude: productionGeo.lng }
  },
  {
    '@type': 'LocalBusiness',
    '@id': siteUrl + '#showroom',
    telephone: '+37360838688',
    openingHoursSpecification: [
      { '@type': 'OpeningHoursSpecification', dayOfWeek: ['Monday','Tuesday','Wednesday','Thursday','Friday'], opens: '09:00', closes: '20:00' },
      { '@type': 'OpeningHoursSpecification', dayOfWeek: ['Saturday'], opens: '09:00', closes: '18:00' }
    ]
  }
]))
</script>

<template>
  <Head>
    <title>{{ t['contacts_meta_title'] }}</title>
    <meta name="description" :content="t['contacts_meta_description']" />

    <link rel="canonical" :href="canonical" />
    <link rel="alternate" hreflang="ru" :href="hrefLang('ru')" />
    <link rel="alternate" hreflang="ro" :href="hrefLang('ro')" />
    <link rel="alternate" hreflang="x-default" :href="hrefLang('ru')" />

    <meta property="og:type" content="website" />
    <meta property="og:url"  :content="canonical" />
    <meta property="og:title" :content="t['contacts_meta_title']" />
    <meta property="og:description" :content="t['contacts_meta_description']" />
  </Head>

  <SeoJSONLD :id="`${locale}-contacts`" :graph="contactGraph" />
</template>
