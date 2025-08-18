// Stores/category.js
import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
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

  // 🔹 Все подкатегории всех родителей (плоский список)
  const allSubcategories = computed(() =>
    navCategories.value.flatMap((parent) =>
      (parent.children || []).map((sub) => ({
        ...sub,
        parent_id: parent.id,
        parent_name: parent.translation?.name ?? 'Без названия',
        // fallback на логотип родителя, если у сабкатегории нет своего
        logo: sub.logo ?? parent.logo ?? null,
      }))
    )
  )

  // 🔹 Ищем либо родителя, либо подкатегорию — и активируем её детей
  function openCategory(categoryId) {
    // 1) Родитель?
    const parent = navCategories.value.find((c) => c.id === categoryId)
    if (parent) {
      activeCategory.value = {
        id: parent.id,
        name: parent.translation?.name ?? 'Без названия',
        children: (parent.children || []).map((sub) => ({
          id: sub.id,
          name: sub.translation?.name ?? 'Без названия',
        })),
      }
      return
    }

    // 2) Подкатегория?
    for (const p of navCategories.value) {
      const sub = (p.children || []).find((c) => c.id === categoryId)
      if (sub) {
        activeCategory.value = {
          id: sub.id,
          name: sub.translation?.name ?? 'Без названия',
          children: (sub.children || []).map((s) => ({
            id: s.id,
            name: s.translation?.name ?? 'Без названия',
          })),
        }
        return
      }
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
    isLoading,
    loadCategories,
    allSubcategories,
    openCategory,
    closeCategory,
    reset,
  }
})
