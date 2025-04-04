<template>
    <div class="min-h-screen flex flex-col">
      <Header />

      <main class="flex-1 p-4 w-full">
        <slot />
      </main>

      <Footer />
      <WhatsAppButton />
    </div>
  </template>

  <script setup>
  import Header from '@/Layouts/Partials/Header.vue'
  import Footer from '@/Layouts/Partials/Footer.vue'
  import WhatsAppButton from '@/Components/common/WhatsAppButton.vue';
  </script>
