<template>
    <Head title="О нас" />
    <h1 class="text-xl font-bold">О нас</h1>
    <p><h2>{{ translations.welcome || 'Загрузка...' }}</h2></p>
</template>
<script setup>
import { Head } from '@inertiajs/vue3';
import { useTranslations } from '@/composables/useTranslations';

const translations = useTranslations();
</script>
