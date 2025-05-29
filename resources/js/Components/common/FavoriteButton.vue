<template>
  <button
    @click="toggleFavorite"
    :title="inFavorites ? 'Удалить из избранного' : 'В избранное'"
    :class="[
      'transition rounded sm:p-2',
      inFavorites
        ? 'text-my_red hover:text-red-300'
        : [sizeClass, isFavorite ? 'text-my_red hover:text-my_red' : 'text-gray-400 hover:text-my_red_op'],
    ]"
  >
    <template v-if="inFavorites">✖</template>
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
  const isFavorite = computed(() => favorites.isFavorite(props.productId))

  const toggleFavorite = () => {
    favorites.localToggle(props.product)
  }
</script>
