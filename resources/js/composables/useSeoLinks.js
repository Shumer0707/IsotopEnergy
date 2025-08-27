import { computed } from 'vue'

export function useSeoLinks() {
  const siteUrl = typeof window !== 'undefined' ? window.location.origin : ''
  const path    = typeof window !== 'undefined' ? window.location.pathname : '/'
  const search  = typeof window !== 'undefined' ? window.location.search : ''
  const href    = typeof window !== 'undefined' ? window.location.href : ''

  const supported = ['ru', 'ro']
  const parts = path.split('/').filter(Boolean)
  const hasLocale = parts.length && supported.includes(parts[0])

  // путь БЕЗ языкового префикса, но с ведущим слэшем
  const basePath = '/' + (hasLocale ? parts.slice(1).join('/') : parts.join('/'))
  const normalizedBase = basePath === '//' ? '/' : basePath

  const urlFor = (lang) => {
    const prefix = lang === 'ru' ? '/ru' : '/ro'
    return siteUrl + (normalizedBase === '/' ? prefix : prefix + normalizedBase)
  }

  const canonical = computed(() => {
    // Каноникал: текущий URL, без hash; query оставляем (напр., для ?page)
    return siteUrl + path + search
  })

  const alternates = computed(() => ({
    ru: urlFor('ru'),
    ro: urlFor('ro'),
    xDefault: urlFor('ru'), // дефолт — ru
  }))

  const og = computed(() => ({
    url: canonical.value,
    // og:locale — можно просто "ru" / "ro" (необязательно указывать регион)
    locale: hasLocale ? parts[0] : 'ru',
    alternates: supported.filter(l => (hasLocale ? l !== parts[0] : true)),
  }))

  // Флаг: на страницах без префикса (напр., /login) не рендерим hreflang
  const isLocalized = hasLocale

  return { siteUrl, href, canonical, alternates, og, isLocalized }
}
