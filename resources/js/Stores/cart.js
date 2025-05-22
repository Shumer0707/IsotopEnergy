import { defineStore } from 'pinia'
import axios from 'axios'

export const useCartStore = defineStore('cart', {
  state: () => ({
    items: {}, // { [productId]: quantity }
    products: [], // массив полных данных о товарах
  }),

  actions: {
    set(cartFromServer) {
      this.items = cartFromServer
    },

    async init() {
      const res = await axios.get(route('cart.get'))
      this.items = res.data.items
      await this.loadProducts()
    },

    async loadProducts() {
      const productIds = Object.keys(this.items)

      if (productIds.length === 0) {
        this.products = []
        return
      }

      const res = await axios.post(route('cart.data'), {
        product_ids: productIds,
      })

      this.products = res.data.products
    },

    clear() {
      this.items = {}
      this.products = []
      axios.delete(route('cart.clear'))
    },

    add(productId, quantity = 1) {
      const existing = this.items[productId] ?? 0

      this.items = {
        ...this.items,
        [productId]: existing + quantity,
      }

      axios.post(route('cart.add'), {
        product_id: productId,
        quantity,
      })
    },

    update(productId, quantity) {
      if (quantity < 1) return

      this.items = {
        ...this.items,
        [productId]: quantity,
      }

      axios.post(route('cart.update'), {
        product_id: productId,
        quantity,
      })
    },

    increment(productId) {
      const qty = (this.items[productId] ?? 0) + 1
      this.update(productId, qty)
    },

    decrement(productId) {
      const qty = (this.items[productId] ?? 0) - 1

      if (qty < 1) {
        this.remove(productId)
      } else {
        this.update(productId, qty)
      }
    },

    remove(productId) {
      const { [productId]: _, ...rest } = this.items
      this.items = rest

      this.products = this.products.filter((p) => p.id !== productId)

      axios.delete(route('cart.remove', productId))
    },
  },
})
