import { defineStore } from 'pinia'
import { router } from '@inertiajs/vue3'

export const useProductFilterStore = defineStore('productFilter', {
  state: () => ({
    categoryId: null,
    filters: {
      brands: [],
      sort: '',
      price_from: 0,
      price_to: 10000,
      max_price: 10000,
    },
  }),

  actions: {
    init({ categoryId, sort, filters, max_price }) {
      this.categoryId = categoryId
      this.filters.sort = sort || ''
      this.filters.brands = filters?.brands || []
      this.filters.max_price = max_price
      this.filters.price_from = filters?.price_from ?? 0
      this.filters.price_to = filters?.price_to ?? max_price
    },

    setSort(value) {
      this.filters.sort = value
      this.apply()
    },

    toggleBrand(id) {
      const index = this.filters.brands.indexOf(id)
      if (index === -1) {
        this.filters.brands.push(id)
      } else {
        this.filters.brands.splice(index, 1)
      }
      this.apply()
    },

    apply() {
      router.get(
        `/category/${this.categoryId}`,
        {
          filters: JSON.stringify({
            brands: this.filters.brands,
            price_from: this.filters.price_from,
            price_to: this.filters.price_to,
          }),
          sort: this.filters.sort,
        },
        {
          preserveScroll: true,
          preserveState: true,
        }
      )
    },

    reset() {
      this.filters = {
        brands: [],
        sort: '',
        price_from: 0,
        price_to: this.filters.max_price,
        max_price: this.filters.max_price,
      }
      this.apply()
    },
  },
})
