<script setup>
  import axios from 'axios'
  import { router, usePage } from '@inertiajs/vue3'
  import { ref, watch, onMounted, onBeforeUnmount } from 'vue'
  import { useTranslations } from '@/composables/useTranslations'

  const t = useTranslations()
  const query = ref('')
  const suggestions = ref([])
  const wrapperRef = ref(null)
  let debounceTimeout = null
  const selectedIndex = ref(-1)
  const noMatchMessage = ref(false)
  const page = usePage()

  const DEBOUNCE_DELAY = 300

  const openProduct = (id) => {
    query.value = ''
    suggestions.value = []
    selectedIndex.value = -1
    router.visit(route('product.show', { locale: page.props.locale, product: id }))
  }

  const handleSearch = () => {
    const exactMatch = suggestions.value.find(
      (product) => product.description?.title.toLowerCase() === query.value.trim().toLowerCase()
    )

    if (exactMatch) {
      openProduct(exactMatch.id)
    } else {
      noMatchMessage.value = true
      setTimeout(() => (noMatchMessage.value = false), 3000)
    }
  }

  const onKeyDown = (e) => {
    if (e.key === 'ArrowDown' && suggestions.value.length) {
      selectedIndex.value = (selectedIndex.value + 1) % suggestions.value.length
      e.preventDefault()
    } else if (e.key === 'ArrowUp' && suggestions.value.length) {
      selectedIndex.value = (selectedIndex.value - 1 + suggestions.value.length) % suggestions.value.length
      e.preventDefault()
    } else if (e.key === 'Escape') {
      suggestions.value = []
      selectedIndex.value = -1
    } else if (e.key === 'Enter') {
      if (selectedIndex.value >= 0) {
        openProduct(suggestions.value[selectedIndex.value].id)
      } else {
        handleSearch()
      }
    }
  }

  watch(query, (newQuery) => {
    clearTimeout(debounceTimeout)

    if (newQuery.length < 2) {
      suggestions.value = []
      return
    }

    debounceTimeout = setTimeout(() => {
      axios
        .get('/search-products', { params: { q: newQuery } })
        .then(({ data }) => (suggestions.value = data))
        .catch(() => (suggestions.value = []))
    }, DEBOUNCE_DELAY)
  })

  const handleClickOutside = (event) => {
    if (wrapperRef.value && !wrapperRef.value.contains(event.target)) {
      suggestions.value = []
      selectedIndex.value = -1
      noMatchMessage.value = false
      query.value = '' // очищаем, чтобы не показывалось "ничего не найдено"
    }
  }

  onMounted(() => document.addEventListener('click', handleClickOutside))
  onBeforeUnmount(() => document.removeEventListener('click', handleClickOutside))
</script>

<template>
  <div ref="wrapperRef" class="relative w-full max-w-sm sm:max-w-md md:max-w-lg">
    <input
      v-model="query"
      @keydown="onKeyDown"
      type="text"
      :placeholder="t.search_text ?? 'Поиск...'"
      class="w-full bg-my_white pl-4 pr-10 py-1.5 sm:py-3 rounded-xl text-black placeholder-gray-500 focus:outline-none"
    />
    <button
      @click="query.length >= 2 && handleSearch()"
      class="absolute right-2 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-gray-700 pr-2"
    >
      <font-awesome-icon icon="magnifying-glass" class="lg:text-xl text-lg" />
    </button>

    <ul v-if="suggestions.length" class="absolute left-0 right-0 top-full mt-2 bg-my_white rounded-xl shadow z-50 text-black">
      <li
        v-for="(product, index) in suggestions"
        :key="product.id"
        @click="openProduct(product.id)"
        :class="[
          'px-4 py-2 cursor-pointer transition-colors',
          selectedIndex === index ? 'bg-gray-300 font-semibold text-main' : 'hover:bg-gray-200',
        ]"
      >
        {{ product.description?.title }}
      </li>
    </ul>

    <div
      v-else-if="query.length >= 2 && !noMatchMessage && suggestions.length === 0"
      class="absolute left-0 right-0 top-full mt-2 bg-white rounded shadow z-50 text-gray-500 px-4 py-3"
    >
      Ничего не найдено
    </div>

    <div
      v-if="noMatchMessage"
      class="absolute left-0 right-0 top-full mt-2 bg-red-100 text-red-800 rounded shadow px-4 py-3 z-50"
    >
      Такой товар не найден
    </div>
  </div>
</template>
