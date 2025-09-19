<template>
  <div class="flex gap-4 text-xl text-gray-600">
    <a
      v-for="social in activeSocials"
      :key="social.key"
      :href="social.url"
      :title="social.name"
      target="_blank"
      rel="noopener noreferrer"
      class="hover:text-black transition-colors"
    >
      <font-awesome-icon :icon="social.icon" />
    </a>
  </div>
</template>

<script setup>
import { useSocialLinks } from '@/composables/useSocialLinks'

const { getActiveSocials } = useSocialLinks()
const activeSocials = getActiveSocials()
</script>
