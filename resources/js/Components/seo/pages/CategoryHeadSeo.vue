<script setup>
import { Head, usePage } from '@inertiajs/vue3'
import { computed } from 'vue'
import SeoJSONLD from '@/Components/seo/SeoJSONLD.vue'

// ⚙️ Принимаем нужные данные со страницы
const props = defineProps({
  category: { type: Object, required: true },
  products: { type: Object, required: true }, // пагинация + список
})

// 🔤 Локаль
const page = usePage()
const locale = computed(() => page.props.locale || 'ru')

// 🌐 URLы (каноникал без query, hreflang с /ro)
const siteUrl = typeof window !== 'undefined' ? window.location.origin   : ''
const path    = typeof window !== 'undefined' ? window.location.pathname : '/'
const href    = typeof window !== 'undefined' ? window.location.href     : ''

const ruPath = computed(() => path.replace(/^\/ro(\/|$)/, '/'))
const roPath = computed(() => (ruPath.value === '/' ? '/ro' : '/ro' + (ruPath.value.endsWith('/') ? ruPath.value.slice(0, -1) : ruPath.value)))
const canonical = computed(() => siteUrl + path)
const hrefLang  = (lang) => (lang === 'ro' ? siteUrl + roPath.value : siteUrl + ruPath.value)

// 🧠 Данные категории
const name = computed(() => props.category?.translation?.name || 'Категория')

// 📝 Мета (локализованные фоллбеки; если заведёте seo_title/seo_desc — можно подставить тут)
const metaTitle = computed(() =>
  locale.value === 'ro'
    ? `${name.value} — cumpără în Moldova | IsotopEnergy`
    : `${name.value} — купить в Молдове | IsotopEnergy`
)

const metaDescription = computed(() => {
  const count = props.products?.total ?? undefined
  if (locale.value === 'ro') {
    return `Categorie: ${name.value}${count ? ` (${count} produse)` : ''}. Livrare în toată Moldova; gratuit de la 60 m² de termopanele.`
  }
  return `Категория: ${name.value}${count ? ` (${count} товаров)` : ''}. Доставка по всей Молдове; от 60 м² термопанелей — бесплатно.`
})

// 🚫 noindex для страниц с фильтрами/сортировкой (чтобы не плодить дублей)
const noindex = computed(() => {
  if (typeof window === 'undefined') return false
  const p = new URLSearchParams(window.location.search)
  p.delete('page')
  // Если есть любые параметры (фильтры/сорт) — noindex
  return p.toString().length > 0
})

// 🧾 JSON-LD: страница коллекции + список товаров
const offset = computed(() => (props.products?.from ? Number(props.products.from) - 1 : 0))
const productItems = computed(() =>
  (props.products?.data || []).map((p, i) => ({
    '@type': 'ListItem',
    position: offset.value + i + 1,
    url: `${siteUrl}/product/${p.id}`,
    name: p?.description?.title || undefined,
  }))
)

const categoryGraph = computed(() => ([
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
]))
</script>

<template>
  <Head>
    <title>{{ metaTitle }}</title>
    <meta name="description" :content="metaDescription" />

    <link rel="canonical" :href="canonical" />
    <link rel="alternate" hreflang="ru" :href="hrefLang('ru')" />
    <link rel="alternate" hreflang="ro" :href="hrefLang('ro')" />
    <link rel="alternate" hreflang="x-default" :href="hrefLang('ru')" />

    <meta property="og:type" content="website" />
    <meta property="og:url"  :content="canonical" />
    <meta property="og:title" :content="metaTitle" />
    <meta property="og:description" :content="metaDescription" />

    <meta v-if="noindex" name="robots" content="noindex,follow" />
  </Head>

  <SeoJSONLD :id="`cat-${props.category?.id || 'list'}`" :graph="categoryGraph" />
</template>
