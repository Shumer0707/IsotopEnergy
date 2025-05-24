import { defineStore } from 'pinia'
import { ref } from 'vue'

export const useLayoutStore = defineStore('layout', () => {
  const headerBottom = ref(0)

  const setHeaderBottom = (value) => {
    headerBottom.value = value
  }

  return { headerBottom, setHeaderBottom }
})
