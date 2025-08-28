// resources/js/Stores/productFilter.js
import { defineStore } from 'pinia'
import { router } from '@inertiajs/vue3'

export const useProductFilterStore = defineStore('productFilter', {
  state: () => ({
    locale: 'ru',
    categoryId: null,
    availableFilters: [],
    filters: {
      brands: [],
      attrs: {},
      sort: '',
      price_from: 0,
      price_to: 10000,
      max_price: 10000,
    },
  }),

  actions: {
    init({ locale, categoryId, sort, filters, max_price, available_filters }) {
      const clamp = (v, lo, hi) => Math.min(Math.max(v, lo), hi)

      this.locale = locale || 'ru'
      this.categoryId = Number(categoryId)
      this.filters.sort = sort || ''
      this.filters.brands = (filters?.brands || []).map(Number).filter(Number.isFinite)

      if (typeof max_price === 'number') this.filters.max_price = max_price
      const mp = this.filters.max_price

      const from = clamp(Number(filters?.price_from ?? 0), 0, mp)
      const toRaw = Number(filters?.price_to ?? mp)
      const to = clamp(Number.isFinite(toRaw) ? toRaw : mp, 0, mp)
      this.filters.price_from = Math.min(from, to)
      this.filters.price_to = Math.max(from, to)

      const srcAttrs = filters?.attrs || {}
      const normAttrs = {}
      Object.keys(srcAttrs).forEach((k) => {
        const aId = Number(k)
        if (!Number.isFinite(aId)) return
        const vals = (srcAttrs[k] || []).map(Number).filter(Number.isFinite)
        if (vals.length) normAttrs[aId] = Array.from(new Set(vals))
      })
      this.filters.attrs = normAttrs

      this.availableFilters = Array.isArray(available_filters) ? available_filters : []
    },

    setAvailableFilters(list) {
      this.availableFilters = Array.isArray(list) ? list : []
    },

    setSort(value) {
      this.filters.sort = value
      this.apply()
    },

    toggleBrand(id) {
      const val = Number(id)
      const i = this.filters.brands.indexOf(val)
      if (i === -1) this.filters.brands.push(val)
      else this.filters.brands.splice(i, 1)
      this.apply()
    },

    toggleAttr(attrId, valueId) {
      const aId = Number(attrId),
        vId = Number(valueId)
      if (!Number.isFinite(aId) || !Number.isFinite(vId)) return
      const list = this.filters.attrs[aId] ? [...this.filters.attrs[aId]] : []
      const idx = list.indexOf(vId)
      if (idx === -1) list.push(vId)
      else list.splice(idx, 1)
      if (list.length) this.filters.attrs[aId] = list
      else delete this.filters.attrs[aId]
      this.apply()
    },

    clearAttrs() {
      this.filters.attrs = {}
    },

    apply() {
      router.get(
        route('category.show', { locale: this.locale, category: this.categoryId }),
        {
          filters: JSON.stringify({
            brands: this.filters.brands,
            attrs: this.filters.attrs,
            price_from: this.filters.price_from,
            price_to: this.filters.price_to,
          }),
          sort: this.filters.sort,
        },
        {
          preserveScroll: true,
          preserveState: true,
          replace: true,
        }
      )
    },

    reset() {
      this.filters.brands = []
      this.filters.attrs = {}
      this.filters.sort = ''
      this.filters.price_from = 0
      this.filters.price_to = this.filters.max_price
      this.apply()
    },
  },
})
