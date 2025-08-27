<script setup>
  import { Head, usePage } from '@inertiajs/vue3'
  import { computed } from 'vue'
  import SeoJSONLD from '@/Components/seo/SeoJSONLD.vue'

  const props = defineProps({
    product: { type: Object, required: true },
  })

  // locale
  const page = usePage()
  const locale = computed(() => page.props.locale || 'ru')

  // URLs
  const siteUrl = typeof window !== 'undefined' ? window.location.origin : ''
  const pathname = typeof window !== 'undefined' ? window.location.pathname : '/'
  const search = typeof window !== 'undefined' ? window.location.search : ''
  const href = typeof window !== 'undefined' ? window.location.href : ''

  // локаль в URL
  const supported = ['ru', 'ro']
  const parts = pathname.split('/').filter(Boolean)
  const hasLocale = parts.length && supported.includes(parts[0])

  // базовый путь БЕЗ языкового префикса
  const basePath = '/' + (hasLocale ? parts.slice(1).join('/') : parts.join('/'))
  const normalizedBase = !basePath || basePath === '//' ? '/' : basePath

  // canonical: для товара обычно без query
  const canonical = computed(() => siteUrl + pathname)

  // hreflang
  const altRu = computed(() => siteUrl + (normalizedBase === '/' ? '/ru' : '/ru' + normalizedBase))
  const altRo = computed(() => siteUrl + (normalizedBase === '/' ? '/ro' : '/ro' + normalizedBase))

  // данные товара
  const title = computed(() => props.product?.description?.title || 'Товар')
  const fullDesc = computed(() => props.product?.description?.full_description || '')
  const metaDescription = computed(() => {
    const s = (fullDesc.value || title.value).replace(/\s+/g, ' ').trim()
    return s.length > 160 ? s.slice(0, 157) + '…' : s
  })
  const brandName = computed(() => props.product?.brand?.translation?.name || 'IsotopEnergy')
  const sku = computed(() => props.product?.sku || String(props.product?.id || ''))
  const price = computed(() => props.product?.discounted_price ?? props.product?.price)
  const currency = computed(() => props.product?.currency || 'MDL')

  // наличие
  const availability = computed(() => {
    const p = props.product || {}
    const inStock = (typeof p.stock === 'number' ? p.stock > 0 : undefined) ?? p.available ?? true
    return inStock ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock'
  })

  // изображения (абсолютные)
  const images = computed(() => {
    const arr = []
    if (props.product?.main_image) arr.push(`/storage/${props.product.main_image}`)
    if (Array.isArray(props.product?.images)) {
      arr.push(...props.product.images.map((i) => `/storage/${i.path}`))
    }
    return [...new Set(arr)].map((u) => (u.startsWith('http') ? u : siteUrl + u))
  })

  // noindex для любых query (если вдруг появятся параметры варианта и т.п.)
  const noindex = computed(() => {
    if (typeof window === 'undefined') return false
    const q = new URLSearchParams(search)
    return q.toString().length > 0
  })

  // рейтинг (если есть)
  const rating = computed(() => {
    const r = props.product?.rating
    if (!r) return null
    const avg = Number(r.avg ?? r.average ?? r.value)
    const count = Number(r.count ?? r.reviews)
    if (!avg || !count) return null
    return { ratingValue: avg, reviewCount: count }
  })

  // JSON-LD Product
  const productGraph = computed(() => {
    const node = {
      '@type': 'Product',
      '@id': href + '#product',
      name: title.value,
      description: fullDesc.value || undefined,
      sku: sku.value || undefined,
      brand: { '@type': 'Brand', name: brandName.value },
      image: images.value.length ? images.value : undefined,
      offers: [
        {
          '@type': 'Offer',
          url: href, // локализованный URL товара
          price: price.value,
          priceCurrency: currency.value,
          availability: availability.value,
          itemCondition: 'https://schema.org/NewCondition',
          seller: { '@type': 'Organization', name: 'IsotopEnergy' },
        },
      ],
    }
    if (rating.value) {
      node.aggregateRating = {
        '@type': 'AggregateRating',
        ratingValue: rating.value.ratingValue,
        reviewCount: rating.value.reviewCount,
      }
    }
    return [node]
  })
</script>

<template>
  <Head>
    <title>{{ title }} — {{ locale === 'ro' ? 'cumpără în Moldova' : 'купить в Молдове' }} | IsotopEnergy</title>
    <meta name="description" :content="metaDescription" />

    <link rel="canonical" :href="canonical" />
    <template v-if="hasLocale">
      <link rel="alternate" hreflang="ru" :href="altRu" />
      <link rel="alternate" hreflang="ro" :href="altRo" />
      <link rel="alternate" hreflang="x-default" :href="altRu" />
    </template>

    <meta property="og:type" content="product" />
    <meta property="og:url" :content="canonical" />
    <meta property="og:title" :content="title + ' | IsotopEnergy'" />
    <meta property="og:description" :content="metaDescription" />
    <meta v-if="images.length" property="og:image" :content="images[0]" />
    <meta property="og:locale" :content="hasLocale ? parts[0] : 'ru'" />

    <meta v-if="noindex" name="robots" content="noindex,follow" />
  </Head>

  <SeoJSONLD :id="`prod-${props.product?.id || 'item'}`" :graph="productGraph" />
</template>
