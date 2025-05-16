<template>
  <button
    @click="toggleFavorite"
    :title="inFavorites ? 'Удалить из избранного' : 'Избранное'"
    :class="[
      inFavorites
        ? 'bg-gray-300 text-gray-700 p-2 rounded hover:bg-red-300'
        : sizeClass + ' text-gray-500 hover:text-pink-600 transition',
      isFavorite && !inFavorites ? 'text-pink-500' : ''
    ]"
  >
    <template v-if="inFavorites">
      ✖
    </template>
    <template v-else>
      <font-awesome-icon :icon="isFavorite ? ['fas', 'heart'] : ['far', 'heart']" />
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
  favorites.localToggle({ id: props.productId })
}
</script>
