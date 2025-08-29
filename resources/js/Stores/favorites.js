import { defineStore } from 'pinia'
import axios from 'axios'
import { useAppStateStore } from './appState'

export const useFavoritesStore = defineStore('favorites', {
  state: () => ({
    items: [], // Массив товаров
  }),

  getters: {
    // 🔹 Количество избранных
    count: (state) => state.items.length,

    // 🔹 Проверка, есть ли товар в избранном
    isFavorite: (state) => (id) => state.items.some((p) => p.id === id),
  },

  actions: {
    // 🔹 Загрузка избранного с сервера
    async load() {
      const res = await axios.get(route('favorites.data'))
      this.items = res.data.products

      const app = useAppStateStore()
      app.markLoaded('favorites')
    },

    // 🔹 Переключение товара в избранном (локально + на сервере)
    localToggle(product) {
      const index = this.items.findIndex((p) => p.id === product.id)

      if (index !== -1) {
        // Удаляем
        this.items.splice(index, 1)
      } else {
        // Добавляем
        this.items.push(product)
      }

      // 🔁 Отправка запроса и откат в случае ошибки
      axios.post(route('favorites.toggle'), { product_id: product.id }).catch(() => {
        if (index === -1) {
          this.items = this.items.filter((p) => p.id !== product.id)
        } else {
          this.items.push(product)
        }
      })
    },

    // 🔹 Удалить товар
    async remove(productId) {
      await axios.delete(route('favorites.remove', productId))
      this.items = this.items.filter((p) => p.id !== productId)
    },

    // 🔹 Очистить все
    async clear() {
      await axios.delete(route('favorites.clear'))
      this.items = []
    },
  },
})
