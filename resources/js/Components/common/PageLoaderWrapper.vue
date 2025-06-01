<template>
  <div class="relative">
    <!-- Лоадер -->
    <transition name="fade">
      <div v-if="loading" class="fixed inset-0 z-40 bg-white flex items-center justify-center min-h-screen">
        <img src="/favicon-1.svg" alt="Loading logo" class="w-20 h-20 animate-logo-spin-bounce" />
      </div>
    </transition>

    <!-- Контент -->
    <transition name="fade" appear>
      <div
        :class="{
          'opacity-0 pointer-events-none': loading,
          'opacity-100': !loading,
          'transition-opacity duration-500': true,
        }"
      >
        <slot />
      </div>
    </transition>
  </div>
</template>

<script setup>
  defineProps({
    loading: {
      type: Boolean,
      required: true,
    },
  })
</script>
