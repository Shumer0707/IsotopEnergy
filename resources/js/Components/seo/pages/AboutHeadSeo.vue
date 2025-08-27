<script setup>
  import { Head, usePage } from '@inertiajs/vue3'
  import { computed } from 'vue'
  import { useTranslations } from '@/composables/useTranslations'
  import SeoJSONLD from '@/Components/seo/SeoJSONLD.vue'

  const t = useTranslations()
  const page = usePage()
  const locale = computed(() => page.props.locale)

  // Текущий адрес
  const siteUrl = typeof window !== 'undefined' ? window.location.origin : ''
  const pathname = typeof window !== 'undefined' ? window.location.pathname : '/'
  const search = typeof window !== 'undefined' ? window.location.search : ''
  const href = typeof window !== 'undefined' ? window.location.href : ''

  // Базовый путь БЕЗ префикса языка
  const supported = ['ru', 'ro']
  const parts = pathname.split('/').filter(Boolean)
  const hasLocale = parts.length && supported.includes(parts[0])
  const basePath = '/' + (hasLocale ? parts.slice(1).join('/') : parts.join('/'))
  const normalizedBase = basePath === '//' || basePath === '' ? '/' : basePath

  // canonical (включаем query, без hash)
  const canonical = computed(() => siteUrl + pathname + search)

  // hreflang-альтернативы
  const altRu = computed(() => siteUrl + (normalizedBase === '/' ? '/ru' : '/ru' + normalizedBase))
  const altRo = computed(() => siteUrl + (normalizedBase === '/' ? '/ro' : '/ro' + normalizedBase))

  // JSON-LD: AboutPage (крошки генерит Breadcrumbs)
  const aboutGraph = computed(() => [
    {
      '@type': 'AboutPage',
      '@id': href + '#webpage',
      url: href,
      name: t.value.about_title ?? 'О компании IsotopEnergy',
      isPartOf: { '@id': siteUrl + '#website' },
      inLanguage: locale.value || 'ru',
      dateModified: new Date().toISOString().split('T')[0],
    },
  ])
</script>

<template>
  <Head>
    <title>{{ t['about_meta_title'] }}</title>
    <meta name="description" :content="t['about_meta_description']" />

    <!-- canonical -->
    <link rel="canonical" :href="canonical" />

    <!-- alternate (только для локализованных путей) -->
    <template v-if="hasLocale">
      <link rel="alternate" hreflang="ru" :href="altRu" />
      <link rel="alternate" hreflang="ro" :href="altRo" />
      <link rel="alternate" hreflang="x-default" :href="altRu" />
    </template>

    <!-- OG -->
    <meta property="og:type" content="website" />
    <meta property="og:url" :content="canonical" />
    <meta property="og:title" :content="t['about_meta_title']" />
    <meta property="og:description" :content="t['about_meta_description']" />
    <meta property="og:locale" :content="hasLocale ? parts[0] : 'ru'" />
  </Head>

  <SeoJSONLD :id="`${locale}-about`" :graph="aboutGraph" />
</template>
