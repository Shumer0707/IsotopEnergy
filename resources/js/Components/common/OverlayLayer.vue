<script setup>
  defineProps({
    show: {
      type: Boolean,
      required: true,
    },
  })

  const emit = defineEmits(['update:show'])

  const close = () => emit('update:show', false)
</script>

<template>
  <Teleport to="body">
    <div
      class="fixed inset-0 bg-black/30 transition-opacity duration-300"
      :class="show ? 'opacity-100 z-30' : 'opacity-0 pointer-events-none z-0'"
      @click="$emit('click')"
      @wheel.prevent
      @touchmove.prevent
      @scroll.prevent
    />
  </Teleport>
</template>
