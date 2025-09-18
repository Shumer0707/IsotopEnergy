import { defineStore } from 'pinia'
import axios from 'axios'

export const useCartStore = defineStore('cart', {
  state: () => ({
    items: {}, // { [variantId]: quantity } - теперь работаем с ID вариантов
    variants: [], // массив полных данных о вариантах из API
  }),

  getters: {
    // Общее количество товаров в корзине
    totalQuantity: (state) => {
      return Object.values(state.items).reduce((sum, qty) => sum + qty, 0)
    },

    // Общая сумма без скидки
    totalWithoutDiscount: (state) => {
      return state.variants.reduce((sum, variant) => {
        const quantity = state.items[variant.id] || 0
        return sum + variant.price * quantity
      }, 0)
    },

    // Общая сумма со скидкой
    totalWithDiscount: (state) => {
      return state.variants.reduce((sum, variant) => {
        const quantity = state.items[variant.id] || 0
        const price = variant.discounted_price || variant.price
        return sum + price * quantity
      }, 0)
    },

    // Размер скидки
    totalDiscount: (state) => {
      return state.totalWithoutDiscount - state.totalWithDiscount
    },

    // Проверка есть ли вариант в корзине
    hasVariant: (state) => {
      return (variantId) => !!state.items[variantId]
    },

    // Получить количество конкретного варианта
    getVariantQuantity: (state) => {
      return (variantId) => state.items[variantId] || 0
    },
  },

  actions: {
    // Установка корзины с сервера (при инициализации)
    set(cartFromServer) {
      this.items = cartFromServer
    },

    // Инициализация - загружаем данные корзины из сессии
    async init() {
      try {
        const res = await axios.get(route('cart.get'))
        this.items = res.data.items || {}
        await this.loadVariants()
      } catch (error) {
        console.error('Ошибка инициализации корзины:', error)
        this.items = {}
        this.variants = []
      }
    },

    // Загрузка полных данных о вариантах
    async loadVariants() {
      const variantIds = Object.keys(this.items).map((id) => parseInt(id))

      if (variantIds.length === 0) {
        this.variants = []
        return
      }

      try {
        const res = await axios.post(route('cart.data'), {
          variant_ids: variantIds,
        })

        this.variants = res.data.variants || []
      } catch (error) {
        console.error('Ошибка загрузки данных корзины:', error)
        this.variants = []
      }
    },

    // Переключение варианта в корзине (добавить/убрать)
    toggle(variantId) {
      if (this.items[variantId]) {
        this.remove(variantId)
      } else {
        this.add(variantId)
      }
    },

    // Полная очистка корзины
    async clear() {
      try {
        this.items = {}
        this.variants = []
        await axios.delete(route('cart.clear'))
      } catch (error) {
        console.error('Ошибка очистки корзины:', error)
      }
    },

    // Добавление варианта в корзину
    async add(variantId, quantity = 1) {
      // Оптимистичное обновление UI
      const existing = this.items[variantId] || 0
      this.items = {
        ...this.items,
        [variantId]: existing + quantity,
      }

      try {
        const response = await axios.post(route('cart.add'), {
          variant_id: variantId,
          quantity,
        })

        // Перезагружаем данные о вариантах
        await this.loadVariants()

        return response.data
      } catch (error) {
        // Откатываем изменения при ошибке
        if (existing === 0) {
          delete this.items[variantId]
        } else {
          this.items[variantId] = existing
        }
        console.error('Ошибка добавления в корзину:', error)
        throw error
      }
    },

    // Обновление количества варианта
    async update(variantId, quantity) {
      if (quantity < 1) {
        return this.remove(variantId)
      }

      // Оптимистичное обновление
      const oldQuantity = this.items[variantId] || 0
      this.items = {
        ...this.items,
        [variantId]: quantity,
      }

      try {
        await axios.post(route('cart.update'), {
          variant_id: variantId,
          quantity,
        })
      } catch (error) {
        // Откатываем изменения при ошибке
        this.items[variantId] = oldQuantity
        console.error('Ошибка обновления корзины:', error)
        throw error
      }
    },

    // Увеличение количества на 1
    increment(variantId) {
      const qty = (this.items[variantId] || 0) + 1
      return this.update(variantId, qty)
    },

    // Уменьшение количества на 1
    decrement(variantId) {
      const qty = (this.items[variantId] || 0) - 1

      if (qty < 1) {
        return this.remove(variantId)
      } else {
        return this.update(variantId, qty)
      }
    },

    // Удаление варианта из корзины
    async remove(variantId) {
      // Оптимистичное обновление UI
      const { [variantId]: removed, ...rest } = this.items
      this.items = rest

      // Удаляем из списка загруженных вариантов
      this.variants = this.variants.filter((variant) => variant.id != variantId)

      try {
        await axios.delete(route('cart.remove', variantId))
      } catch (error) {
        // Откатываем изменения при ошибке
        this.items[variantId] = removed || 1
        await this.loadVariants()
        console.error('Ошибка удаления из корзины:', error)
        throw error
      }
    },

    // Проверка что вариант добавлен в корзину (для кнопок)
    isVariantInCart(variantId) {
      return !!this.items[variantId]
    },

    // Получить данные конкретного варианта из загруженных
    getVariantData(variantId) {
      return this.variants.find((variant) => variant.id == variantId)
    },
  },
})
