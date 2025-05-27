<template>
  <button
    @click="toggleFavorite"
    :title="inFavorites ? 'Удалить из избранного' : 'Избранное'"
    :class="['sm:p-2',
      inFavorites
        ? ' rounded hover:bg-red-300'
        : sizeClass + 'hover:text-pink-600 transition',
      isFavorite && !inFavorites ? ' text-pink-500' : ''
    ]"
  >
    <template v-if="inFavorites">
      ✖
    </template>
    <template v-else>
      <font-awesome-icon class="text-xl sm:text-2xl" :icon="isFavorite ? ['fas', 'heart'] : ['far', 'heart']" />
    </template>
  </button>
</template>

<script setup>
import { computed } from 'vue'
import { useFavoritesStore } from '@/Stores/favorites'

const props = defineProps({
  productId: {
    type: Number,
    required: true,
  },
  product: Object,
  sizeClass: {
    type: String,
    default: 'text-base',
  },
  inFavorites: {
    type: Boolean,
    default: false,
  },
})

const favorites = useFavoritesStore()
favorites.load()
const isFavorite = computed(() => favorites.isFavorite(props.productId))

const toggleFavorite = () => {
  favorites.localToggle(props.product)
}
</script>
