<script setup>
  import { Link, usePage } from '@inertiajs/vue3'
  import { useTranslations } from '@/composables/useTranslations'
  import { computed } from 'vue'
  import { useCategoryStore } from '@/Stores/category'

  const page = usePage()
  const locale = computed(() => usePage().props.locale)
  const navCategories = computed(() => categoryStore.navCategories)
  const categoryStore = useCategoryStore()
  const props = defineProps({
    segments: {
      type: Array,
      required: true,
    },
  })

  const parent = computed(() => {
    const all = navCategories.value
    return all.find((c) => c.children?.some((child) => child.id === sub.value?.id))
  })

  const sub = computed(() => {
    const all = navCategories.value.flatMap((c) => c.children || [])
    return all.find((child) => child.id === page.props.category?.id)
  })

  const translations = useTranslations()

  function openModal() {
    if (parent.value) {
      categoryStore.openCategory(parent.value.id)
    }
  }

  const breadcrumbs = computed(() => {
    const labels = {
      about: translations.value.About,
      contacts: translations.value.Contacts,
      cart: translations.value.Cart,
      favorites: translations.value.favorites,
    }

    const crumbs = [{ name: translations.value.Home || 'Главная', href: '/' }]

    if (parent.value && sub.value) {
      crumbs.push({
        name: parent.value.translation?.name ?? '...',
        href: null,
      })

      crumbs.push({
        name: sub.value.translation?.name ?? '...',
        href: null,
      })

      return crumbs
    }

    // fallback
    props.segments.forEach((segment, index) => {
      const path = '/' + props.segments.slice(0, index + 1).join('/')
      crumbs.push({
        name: labels[segment] || decodeURIComponent(segment),
        href: index === props.segments.length - 1 ? null : path,
      })
    })

    return crumbs
  })
</script>

<template>
  <nav class="text-sm text-gray-500 mb-4">
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
</template>
