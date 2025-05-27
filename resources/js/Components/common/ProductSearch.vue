<script setup>
  import axios from 'axios'
  import { usePage } from '@inertiajs/vue3'
  import { router } from '@inertiajs/vue3'
  import { ref, watch, onMounted, onBeforeUnmount } from 'vue'

  const query = ref('')
  const suggestions = ref([])
  const wrapperRef = ref(null)
  let debounceTimeout = null
  const selectedIndex = ref(-1)

  const openProduct = (id) => {
    router.visit(`/product/${id}`)
  }

  const onKeyDown = (e) => {
    if (!suggestions.value.length) return

    if (e.key === 'ArrowDown') {
      selectedIndex.value = (selectedIndex.value + 1) % suggestions.value.length
      e.preventDefault()
    } else if (e.key === 'ArrowUp') {
      selectedIndex.value = (selectedIndex.value - 1 + suggestions.value.length) % suggestions.value.length
      e.preventDefault()
    } else if (e.key === 'Enter' && selectedIndex.value >= 0) {
      openProduct(suggestions.value[selectedIndex.value].id)
    } else if (e.key === 'Escape') {
      suggestions.value = []
      selectedIndex.value = -1
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
        .then((res) => {
          suggestions.value = res.data
        })
        .catch(() => {
          suggestions.value = []
        })
    }, 300)
  })

  const handleClickOutside = (event) => {
    if (wrapperRef.value && !wrapperRef.value.contains(event.target)) {
      suggestions.value = []
    }
  }

  onMounted(() => {
    document.addEventListener('click', handleClickOutside)
  })

  onBeforeUnmount(() => {
    document.removeEventListener('click', handleClickOutside)
  })
</script>

<template>
  <div ref="wrapperRef" class="relative w-full max-w-sm sm:max-w-md md:max-w-lg">
    <input
      v-model="query"
      @keydown="onKeyDown"
      type="text"
      placeholder="Поиск по товарам..."
      class="w-full pl-4 pr-10 py-1.5 sm:py-3 rounded-xl text-black placeholder-gray-500 focus:outline-none"
    />
    <button class="absolute right-2 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-gray-700 pr-2">
      <font-awesome-icon icon="magnifying-glass" class="lg:text-xl text-lg" />
    </button>

    <ul v-if="suggestions.length" class="absolute left-0 right-0 top-full mt-2 bg-white rounded shadow z-50 text-black">
      <li
        v-for="(product, index) in suggestions"
        :key="product.id"
        @click="openProduct(product.id)"
        :class="[
          'px-4 py-2 cursor-pointer transition-colors',
          selectedIndex === index ? 'bg-gray-300 font-semibold text-main' : 'hover:bg-gray-100',
        ]"
      >
        {{ product.description?.title }}
      </li>
    </ul>

    <!-- 🔸 Ничего не найдено -->
    <div
      v-else-if="query.length >= 2"
      class="absolute left-0 right-0 top-full mt-2 bg-white rounded shadow z-50 text-gray-500 px-4 py-3"
    >
      Ничего не найдено
    </div>
  </div>
</template>
