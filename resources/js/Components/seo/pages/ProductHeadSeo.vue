<script setup>
  import { Head, usePage } from '@inertiajs/vue3'
  import { computed } from 'vue'
  import SeoJSONLD from '@/Components/seo/SeoJSONLD.vue'

  const props = defineProps({
    product: { type: Object, required: true },
  })

  /* locale */
  const page = usePage()
  const locale = computed(() => page.props.locale || 'ru')

  /* URLs */
  const siteUrl = typeof window !== 'undefined' ? window.location.origin : ''
  const path = typeof window !== 'undefined' ? window.location.pathname : '/'
  const href = typeof window !== 'undefined' ? window.location.href : ''

  /* canonical + hreflang (ru=/..., ro=/ro/...) */
  const ruPath = computed(() => path.replace(/^\/ro(\/|$)/, '/'))
  const roPath = computed(() =>
    ruPath.value === '/' ? '/ro' : '/ro' + (ruPath.value.endsWith('/') ? ruPath.value.slice(0, -1) : ruPath.value)
  )
  const canonical = computed(() => siteUrl + path)
  const hrefLang = (lang) => (lang === 'ro' ? siteUrl + roPath.value : siteUrl + ruPath.value)

  /* данные товара */
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

  /* наличие (фоллбек — в наличии) */
  const availability = computed(() => {
    const p = props.product || {}
    const inStock = (typeof p.stock === 'number' ? p.stock > 0 : undefined) ?? p.available ?? true
    return inStock ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock'
  })

  /* изображения */
  const images = computed(() => {
    const arr = []
    if (props.product?.main_image) arr.push(`/storage/${props.product.main_image}`)
    if (Array.isArray(props.product?.images)) {
      arr.push(...props.product.images.map((i) => `/storage/${i.path}`))
    }
    // абсолютные URL для JSON-LD
    return [...new Set(arr)].map((u) => (u.startsWith('http') ? u : siteUrl + u))
  })

  /* noindex для URL с лишними query (варианты/фильтры и т.п.) */
  const noindex = computed(() => {
    if (typeof window === 'undefined') return false
    const q = new URLSearchParams(window.location.search)
    // оставим только page (пагинация), остальное — повод для noindex
    q.delete('page')
    return q.toString().length > 0
  })

  /* рейтинг (если есть) */
  const rating = computed(() => {
    const r = props.product?.rating
    if (!r) return null
    const avg = Number(r.avg ?? r.average ?? r.value)
    const count = Number(r.count ?? r.reviews)
    if (!avg || !count) return null
    return { ratingValue: avg, reviewCount: count }
  })

  /* JSON-LD Product */
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
          url: href,
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
    <link rel="alternate" hreflang="ru" :href="hrefLang('ru')" />
    <link rel="alternate" hreflang="ro" :href="hrefLang('ro')" />
    <link rel="alternate" hreflang="x-default" :href="hrefLang('ru')" />

    <meta property="og:type" content="product" />
    <meta property="og:url" :content="canonical" />
    <meta property="og:title" :content="title + ' | IsotopEnergy'" />
    <meta property="og:description" :content="metaDescription" />
    <meta v-if="images.length" property="og:image" :content="images[0]" />

    <meta v-if="noindex" name="robots" content="noindex,follow" />
  </Head>

  <SeoJSONLD :id="`prod-${props.product?.id || 'item'}`" :graph="productGraph" />
</template>
