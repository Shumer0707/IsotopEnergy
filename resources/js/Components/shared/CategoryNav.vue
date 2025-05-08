<script setup>
  import { ref, computed, onMounted, onBeforeUnmount } from 'vue'
  import { Link, usePage } from '@inertiajs/vue3'
  import axios from 'axios'
  import SubcategoryModal from '@/Components/shared/SubcategoryModal.vue'

  const showCategories = ref(false)
  const navCategories = ref([])
  const locale = computed(() => usePage().props.locale)
  const navRef = ref(null)
  const navButtonRef = ref(null)
  const menuTop = ref(0)

  const toggleCategories = () => {
    showCategories.value = !showCategories.value

    if (navCategories.value.length === 0 && showCategories.value) {
      axios.get('/layout-data').then((res) => {
        navCategories.value = res.data.navCategories
      })
    }
  }

  const categories = computed(() => {
    return navCategories.value.map((category) => ({
      id: category.id,
      name: category.translation?.name ?? 'Без названия',
      children: (category.children || []).map((sub) => ({
        id: sub.id,
        name: sub.translation?.name ?? 'Без названия',
      })),
    }))
  })

  const handleClickOutside = (event) => {
    if (navRef.value && !navRef.value.contains(event.target)) {
      showCategories.value = false
    }
  }

  const activeCategory = ref(null)

  function openSubcategories(category) {
    activeCategory.value = category
    // Здесь дальше будет вызываться модалка
    console.log('Открываем модалку для:', category.name)
  }

  onMounted(() => {
    document.addEventListener('click', handleClickOutside)
  })

  onBeforeUnmount(() => {
    document.removeEventListener('click', handleClickOutside)
  })

  onMounted(() => {
    if (navButtonRef.value) {
      const rect = navButtonRef.value.getBoundingClientRect()
      menuTop.value = rect.bottom + 24
    }
  })
</script>

<template>
  <div ref="navRef" class="relative">
    <button
      ref="navButtonRef"
      @click="toggleCategories"
      class="bg-pink-600 hover:bg-pink-500 text-white px-12 py-3 rounded-xl text-sm lg:text-base"
    >
      Каталог
    </button>
    <div
      v-if="showCategories"
      :style="{ top: `${menuTop}px` }"
      class="fixed left-0 bg-white text-gray-800 shadow-xl z-50 transition-all duration-300 px-4 py-2"
    >
      <div v-for="category in categories" :key="category.name" class="relative group">
        <button class="block w-full px-4 py-2 text-left hover:bg-gray-200" @click.prevent="openSubcategories(category)">
          {{ category.name }}
        </button>
        <div
          class="absolute left-full top-0 mt-0 max-h-0 w-0 bg-white shadow-md opacity-0 group-hover:opacity-100 transition-all duration-300 overflow-hidden group-hover:max-h-[400px] group-hover:w-64"
        >
          <Link
            v-for="sub in category.children"
            :key="sub.id"
            :href="`/category/${sub.id}`"
            class="block px-4 py-2 hover:bg-gray-200"
          >
            {{ sub.name }}
          </Link>
        </div>
      </div>
    </div>
  </div>
  <SubcategoryModal :category="activeCategory" @close="activeCategory = null" />
</template>
