import { defineStore } from 'pinia'
import { ref } from 'vue'
import axios from 'axios'

export const useCategoryStore = defineStore('category', () => {
  const activeCategory = ref(null)
  const navCategories = ref([])
  const isLoaded = ref(false)
  const isLoading = ref(false)

  async function loadCategories() {
    if (isLoaded.value || isLoading.value) return

    isLoading.value = true
    try {
      const res = await axios.get('/layout-data')
      navCategories.value = res.data.navCategories
      isLoaded.value = true
    } catch (e) {
      console.error('Ошибка при загрузке navCategories:', e)
    } finally {
      isLoading.value = false
    }
  }

  function openCategory(categoryId) {
    const parent = navCategories.value.find((c) => c.id === categoryId)

    if (!parent) return

    activeCategory.value = {
      id: parent.id,
      name: parent.translation?.name ?? 'Без названия',
      children: (parent.children || []).map((sub) => ({
        id: sub.id,
        name: sub.translation?.name ?? 'Без названия',
      })),
    }
  }

  function closeCategory() {
    activeCategory.value = null
  }

  function reset() {
    isLoaded.value = false
    navCategories.value = []
  }

  return {
    activeCategory,
    navCategories,
    isLoaded,
    loadCategories,
    openCategory,
    closeCategory,
    reset,
  }
})
