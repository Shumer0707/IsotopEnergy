<template>
    <div class="p-4">
        <Head :title="product.name" />
        <h1 class="text-2xl font-bold">{{ product.name }}</h1>
        <p class="mt-2">{{ product.description }}</p>
        <p class="mt-2 text-lg font-semibold">Цена: {{ product.price }} {{ product.currency }}</p>
    </div>
</template>

<script setup>
import { Head } from '@inertiajs/vue3';
import { defineProps } from 'vue';

const props = defineProps({
    product: Object,
});
</script>
