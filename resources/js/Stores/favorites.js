// Stores/favorites.js
import { defineStore } from 'pinia'
import axios from 'axios'

export const useFavoritesStore = defineStore('favorites', {
  state: () => ({
    items: [], // теперь это массив товаров, а не id
  }),

  actions: {
    async load() {
      const res = await axios.get(route('favorites.data'))
      this.items = res.data.products
    },

    isFavorite(productId) {
      return this.items.some((p) => p.id === productId)
    },

    localToggle(product) {
      const index = this.items.findIndex((p) => p.id === product.id)

      if (index !== -1) {
        // Удаляем локально
        this.items.splice(index, 1)
      } else {
        // Добавляем локально
        this.items.push(product)
      }

      // Отправляем на бэк
      axios.post(route('favorites.toggle'), { product_id: product.id }).catch(() => {
        // В случае ошибки — откатим
        if (index === -1) {
          this.items = this.items.filter((p) => p.id !== product.id)
        } else {
          this.items.push(product)
        }
      })
    },

    async remove(productId) {
      const res = await axios.delete(route('favorites.remove', productId))
      this.items = this.items.filter(p => p.id !== productId)
    },

    async clear() {
      await axios.delete(route('favorites.clear'))
      this.items = []
    }
  },
})
