<script setup>
  import { Head, usePage } from '@inertiajs/vue3'
  import { computed } from 'vue'
  import SeoJSONLD from '@/Components/seo/SeoJSONLD.vue'

  const props = defineProps({
    category: { type: Object, required: true },
    products: { type: Object, required: true },
  })

  const page = usePage()
  const locale = computed(() => page.props.locale || 'ru')

  // ── URL
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
  const normalizedBase = !basePath || basePath === '//' ? '/' : basePath

  // canonical: оставляем ТОЛЬКО ?page=n (фильтры не канонизируем)
  const canonical = computed(() => {
    const p = new URLSearchParams(search)
    const onlyPage = p.get('page')
    return siteUrl + pathname + (onlyPage ? `?page=${onlyPage}` : '')
  })

  // hreflang
  const altRu = computed(() => siteUrl + (normalizedBase === '/' ? '/ru' : '/ru' + normalizedBase))
  const altRo = computed(() => siteUrl + (normalizedBase === '/' ? '/ro' : '/ro' + normalizedBase))

  // данные категории
  const name = computed(() => props.category?.translation?.name || 'Категория')

  // мета
  const metaTitle = computed(() =>
    locale.value === 'ro'
      ? `${name.value} — cumpără în Moldova | IsotopEnergy`
      : `${name.value} — купить в Молдове | IsotopEnergy`
  )

  const metaDescription = computed(() => {
    const count = props.products?.total
    if (locale.value === 'ro') {
      return `Categorie: ${name.value}${
        count ? ` (${count} produse)` : ''
      }. Livrare în toată Moldova; gratuit de la 60 m² de termopanele.`
    }
    return `Категория: ${name.value}${
      count ? ` (${count} товаров)` : ''
    }. Доставка по всей Молдове; от 60 м² термопанелей — бесплатно.`
  })

  // noindex для любых параметров кроме page
  const noindex = computed(() => {
    if (typeof window === 'undefined') return false
    const p = new URLSearchParams(window.location.search)
    p.delete('page')
    return p.toString().length > 0
  })

  // JSON-LD (важно: URL товара с префиксом языка!)
  const offset = computed(() => (props.products?.from ? Number(props.products.from) - 1 : 0))
  const productItems = computed(() =>
    (props.products?.data || []).map((p, i) => ({
      '@type': 'ListItem',
      position: offset.value + i + 1,
      url: `${siteUrl}/${locale.value}/product/${p.id}`,
      name: p?.description?.title || undefined,
    }))
  )

  const categoryGraph = computed(() => [
    {
      '@type': 'CollectionPage',
      '@id': href + '#webpage',
      url: href,
      name: name.value,
      isPartOf: { '@id': siteUrl + '#website' },
      inLanguage: locale.value,
      dateModified: new Date().toISOString().split('T')[0],
    },
    {
      '@type': 'ItemList',
      '@id': href + '#itemlist',
      url: href,
      name: name.value,
      numberOfItems: props.products?.total ?? undefined,
      itemListElement: productItems.value,
    },
  ])
</script>

<template>
  <Head>
    <title>{{ metaTitle }}</title>
    <meta name="description" :content="metaDescription" />

    <link rel="canonical" :href="canonical" />
    <template v-if="hasLocale">
      <link rel="alternate" hreflang="ru" :href="altRu" />
      <link rel="alternate" hreflang="ro" :href="altRo" />
      <link rel="alternate" hreflang="x-default" :href="altRu" />
    </template>

    <meta property="og:type" content="website" />
    <meta property="og:url" :content="canonical" />
    <meta property="og:title" :content="metaTitle" />
    <meta property="og:description" :content="metaDescription" />
    <meta property="og:locale" :content="hasLocale ? parts[0] : 'ru'" />

    <meta v-if="noindex" name="robots" content="noindex,follow" />
  </Head>

  <SeoJSONLD :id="`cat-${props.category?.id || 'list'}`" :graph="categoryGraph" />
</template>
