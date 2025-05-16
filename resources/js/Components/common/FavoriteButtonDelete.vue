<template>
  <button

    @click="favorites.localToggle(product)"
    class="bg-gray-300 text-gray-700 p-2 rounded hover:bg-red-300"
    title="Удалить из избранного"
  >
    ✖
  </button>
</template>

<script setup>
  const favorites = useFavoritesStore()
  import { useFavoritesStore } from '@/Stores/favorites'
</script>
