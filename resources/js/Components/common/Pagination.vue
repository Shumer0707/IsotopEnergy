<script setup>
defineProps({
  links: Array,
})
</script>

<template>
  <div class="mt-6 flex justify-center gap-1 flex-wrap">
    <template v-for="link in links" :key="link.label">
      <component
        :is="link.url ? 'Link' : 'span'"
        :href="link.url"
        class="px-3 py-1 text-sm rounded border"
        :class="{
          'bg-pink-600 text-white border-pink-600': link.active,
          'text-gray-500 cursor-not-allowed bg-gray-100': !link.url,
          'hover:bg-gray-200': link.url && !link.active,
        }"
        v-html="link.label"
      />
    </template>
  </div>
</template>

<script>
import { Link } from '@inertiajs/vue3'
export default { components: { Link } }
</script>
