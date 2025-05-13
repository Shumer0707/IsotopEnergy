import { defineStore } from 'pinia'
import { router } from '@inertiajs/vue3'

export const useProductFilterStore = defineStore('productFilter', {
  state: () => ({
    categoryId: null,
    filters: {
      brands: [],
      sort: '',
    },
  }),

  actions: {
    setCategoryId(id) {
      this.categoryId = id
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
          filters: JSON.stringify({ brands: this.filters.brands }),
          sort: this.filters.sort,
        },
        {
          preserveScroll: true,
          preserveState: true,
        }
      )
    },

    init({ categoryId, sort, filters }) {
      this.categoryId = categoryId
      this.filters.sort = sort || ''
      this.filters.brands = filters?.brands || []
    },
    reset() {
      this.filters.sort = ''
      this.filters.brands = []
      this.apply()
    },
  },
})
