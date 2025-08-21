<script setup>
  import { useSiteSchema } from '@/composables/useSiteSchema'
  import SeoJSONLD from '@/Components/seo/SeoJSONLD.vue'
  const { graph } = useSiteSchema()
</script>

<template>
  <SeoJSONLD id="global-schema" :graph="graph" />
</template>
