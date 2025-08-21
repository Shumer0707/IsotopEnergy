<script setup>
  import { onMounted, onBeforeUnmount, watch, computed, ref } from 'vue'

  const props = defineProps({
    graph: { type: Array, default: () => [] },
    id: { type: String, default: 'jsonld' }, // сделай уникальным на странице
  })

  const nodeRef = ref(null)
  const json = computed(() => JSON.stringify({ '@context': 'https://schema.org', '@graph': props.graph ?? [] }))

  onMounted(() => {
    const s = document.createElement('script')
    s.type = 'application/ld+json'
    s.setAttribute('data-key', props.id)
    s.text = json.value
    document.head.appendChild(s)
    nodeRef.value = s
  })

  watch(json, (val) => {
    if (nodeRef.value) nodeRef.value.text = val
  })

  onBeforeUnmount(() => {
    if (nodeRef.value?.parentNode) nodeRef.value.parentNode.removeChild(nodeRef.value)
  })
</script>

<template></template>
