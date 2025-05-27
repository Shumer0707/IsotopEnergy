import { defineStore } from 'pinia'
import { ref } from 'vue'

export const useLayoutStore = defineStore('layout', () => {
  const headerBottom = ref(0)
  const cartButtonRightOffset = ref(12)
  const categoryButtonLeftOffset = ref(0)

  const setHeaderBottom = (value) => {
    headerBottom.value = value
  }

  const setCartButtonRightOffset = (value) => {
    cartButtonRightOffset.value = value
  }

  const setCategoryButtonLeftOffset = (value) => {
    categoryButtonLeftOffset.value = value
  }

  return {
    headerBottom,
    setHeaderBottom,
    cartButtonRightOffset,
    setCartButtonRightOffset,
    categoryButtonLeftOffset,
    setCategoryButtonLeftOffset,
  }
})
