import { defineStore } from 'pinia'
import { ref } from 'vue'

export const useCartMiniUiStore = defineStore('cartMiniUi', () => {
  const isMiniCartOpen = ref(false)

  const toggleMiniCart = () => {
    isMiniCartOpen.value = !isMiniCartOpen.value
  }

  const closeMiniCart = () => {
    isMiniCartOpen.value = false
  }

  return { isMiniCartOpen, toggleMiniCart, closeMiniCart }
})
