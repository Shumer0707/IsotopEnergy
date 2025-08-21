import { computed } from 'vue'

export function useSiteSchema() {
  const siteUrl = typeof window !== 'undefined' ? window.location.origin : ''

  const orgId = siteUrl + '#org'

  const graph = computed(() => [
    // Organization
    {
      '@type': 'Organization',
      '@id': orgId,
      name: 'IsotopEnergy',
      url: siteUrl || undefined,
      logo: siteUrl ? siteUrl + '/storage/logo/logo.png' : undefined, // ← при необходимости поменяй путь
      areaServed: 'MD',
    },
    // WebSite (+ поиск, если есть /search?q=)
    {
      '@type': 'WebSite',
      '@id': siteUrl + '#website',
      url: siteUrl || undefined,
      name: 'IsotopEnergy',
      potentialAction: {
        '@type': 'SearchAction',
        target: siteUrl ? siteUrl + '/search?q={q}' : undefined,
        'query-input': 'required name=q',
      },
    },
    // Производство
    {
      '@type': 'LocalBusiness',
      '@id': siteUrl + '#production',
      name: 'IsotopEnergy — производство',
      parentOrganization: { '@id': orgId },
      address: {
        '@type': 'PostalAddress',
        streetAddress: 'str. Vasile Lupu 191',
        addressLocality: 'Orhei',
        addressCountry: 'MD',
      },
    },
    // Шоу-рум
    {
      '@type': 'LocalBusiness',
      '@id': siteUrl + '#showroom',
      name: 'IsotopEnergy — шоу-рум',
      parentOrganization: { '@id': orgId },
      address: {
        '@type': 'PostalAddress',
        streetAddress: 'str. Unirii 51/e',
        addressCountry: 'MD',
      },
    },
  ])

  return { graph }
}
