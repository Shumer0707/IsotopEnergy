import { defineStore } from 'pinia'

export const useAppStateStore = defineStore('appState', {
  state: () => ({
    loaded: {
      favorites: false,
      cart: false,
      // потом добавим user, settings и т.д.
    },
  }),

  actions: {
    markLoaded(key) {
      this.loaded[key] = true
    },
    isLoaded(key) {
      return this.loaded[key]
    },
  },
})
