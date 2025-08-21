<script setup>
  import { Head, usePage } from '@inertiajs/vue3'
  import { computed } from 'vue'
  import { useTranslations } from '@/composables/useTranslations'
  import SeoJSONLD from '@/Components/seo/SeoJSONLD.vue'

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

  // JSON-LD: только AboutPage (крошки даёт Breadcrumbs.vue)
  const aboutGraph = computed(() => [
    {
      '@type': 'AboutPage',
      '@id': href + '#webpage',
      url: href,
      name: t.value.about_title ?? 'О компании IsotopEnergy',
      isPartOf: { '@id': siteUrl + '#website' },
      inLanguage: t.value?.__locale ?? (locale.value || 'ru'),
      dateModified: new Date().toISOString().split('T')[0],
    },
  ])
</script>

<template>
  <Head>
    <title>{{ t['about_meta_title'] }}</title>
    <meta name="description" :content="t['about_meta_description']" />

    <link rel="canonical" :href="canonical" />
    <link rel="alternate" hreflang="ru" :href="hrefLang('ru')" />
    <link rel="alternate" hreflang="ro" :href="hrefLang('ro')" />
    <link rel="alternate" hreflang="x-default" :href="hrefLang('ru')" />

    <meta property="og:type" content="website" />
    <meta property="og:url" :content="canonical" />
    <meta property="og:title" :content="t['about_meta_title']" />
    <meta property="og:description" :content="t['about_meta_description']" />
  </Head>

  <SeoJSONLD :id="`${locale}-about`" :graph="aboutGraph" />
</template>
