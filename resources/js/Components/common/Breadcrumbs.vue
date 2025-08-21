<script setup>
  import { Link, usePage } from '@inertiajs/vue3'
  import { computed } from 'vue'
  import { useTranslations } from '@/composables/useTranslations'
  import { useCategoryStore } from '@/Stores/category'
  import SubcategoryModal from '../shared/SubcategoryModal.vue'
  import SeoJSONLD from '@/Components/seo/SeoJSONLD.vue' // ← инжектор JSON-LD

  const page = usePage()
  const locale = computed(() => page.props.locale)

  const categoryStore = useCategoryStore()
  const translations = useTranslations()

  const navCategories = computed(() => categoryStore.navCategories)

  const sub = computed(() => {
    const all = navCategories.value.flatMap((c) => c.children || [])
    return all.find((child) => child.id === page.props.category?.id)
  })

  const parent = computed(() => navCategories.value.find((c) => c.children?.some((child) => child.id === sub.value?.id)))

  const product = computed(() => page.props.product)

  function openModal() {
    if (parent.value) {
      requestAnimationFrame(() => categoryStore.openCategory(parent.value.id))
    }
  }

  const breadcrumbs = computed(() => {
    const labels = {
      about: translations.value.about,
      contacts: translations.value.contacts,
      cart: translations.value.cart,
      favorites: translations.value.favorites,
    }

    const crumbs = [{ name: translations.value.home || 'Главная', href: '/' }]

    if (parent.value && sub.value) {
      crumbs.push({ name: parent.value.translation?.name ?? '...', href: null })
      crumbs.push({ name: sub.value.translation?.name ?? '...', href: route('category.show', sub.value.id) })
    }

    if (product.value?.description?.title) {
      crumbs.push({ name: product.value.description.title, href: null })
    }

    if (crumbs.length === 1) {
      const segments = page.url.split('/').filter(Boolean)
      segments.forEach((segment, index) => {
        const path = '/' + segments.slice(0, index + 1).join('/')
        crumbs.push({
          name: labels[segment] || decodeURIComponent(segment),
          href: index === segments.length - 1 ? null : path,
        })
      })
    }

    return crumbs
  })

  /* ✅ JSON-LD на основе тех же крошек */
  const siteUrl = typeof window !== 'undefined' ? window.location.origin : ''
  const pageUrl = typeof window !== 'undefined' ? window.location.href : ''

  const breadcrumbGraph = computed(() => [
    {
      '@type': 'BreadcrumbList',
      itemListElement: breadcrumbs.value.map((c, idx) => {
        const isLast = idx === breadcrumbs.value.length - 1
        const abs = c.href ? (c.href.startsWith('http') ? c.href : siteUrl + c.href) : isLast ? pageUrl : undefined
        const node = { '@type': 'ListItem', position: idx + 1, name: c.name }
        if (abs) node.item = abs
        return node
      }),
    },
  ])
</script>

<template>
  <div class="max-w-7xl mx-auto px-4">
    <nav class="text-sm text-gray-500 mb-4" aria-label="Breadcrumb">
      <ol class="flex flex-wrap gap-1">
        <li v-for="(item, i) in breadcrumbs" :key="i" class="flex items-center gap-1">
          <template v-if="item.href">
            <Link :href="item.href" class="hover:underline text-gray-500">{{ item.name }}</Link>
          </template>
          <template v-else>
            <span
              v-if="item.name === parent?.translation?.name"
              class="cursor-pointer text-gray-800 hover:underline"
              @click="openModal"
            >
              {{ item.name }}
            </span>
            <span v-else class="font-semibold text-gray-800">
              {{ item.name }}
            </span>
          </template>
          <span v-if="i < breadcrumbs.length - 1" class="mx-1 text-gray-400">/</span>
        </li>
      </ol>
    </nav>
  </div>

  <!-- Невидимый JSON-LD (вставится в <head> через SeoJSONLD) -->
  <SeoJSONLD v-if="breadcrumbs.length > 1" :id="`${locale}-crumbs`" :graph="breadcrumbGraph" />

  <SubcategoryModal
    v-if="categoryStore.activeCategory"
    :category="categoryStore.activeCategory"
    :button-ref="null"
    @close="categoryStore.closeCategory"
  />
</template>
