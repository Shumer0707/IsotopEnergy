<template>
    <header
        class="bg-gray-800 text-white p-4 flex justify-between items-center"
    >
        <nav class="flex space-x-4">
            <CategoryNav />
            <Link href="/" class="px-2">Главная</Link>
            <Link href="/about" class="px-2">О нас</Link>
            <Link href="/contacts" class="px-2">Контакты</Link>
            <Link href="/cart" class="px-2">Корзина</Link>
        </nav>
        <div class="relative group">
            <button class="px-2">Язык</button>
            <div
                class="absolute right-0 mt-2 w-24 bg-white text-gray-800 shadow-md opacity-0 group-hover:opacity-100 transition-opacity z-50"
            >
                <button
                    @click="changeLocale('ru')"
                    class="block w-full px-4 py-2 hover:bg-gray-200"
                >
                    RU
                </button>
                <button
                    @click="changeLocale('ro')"
                    class="block w-full px-4 py-2 hover:bg-gray-200"
                >
                    RO
                </button>
            </div>
        </div>
    </header>
</template>

<script setup>
// import { ref, onMounted, computed } from 'vue'
import { Link,  router } from '@inertiajs/vue3'
import CategoryNav from '@/Components/shared/CategoryNav.vue'

function changeLocale(lang) {
    router.visit(route("set-locale", lang), {
        method: "get",
        preserveScroll: true,
        onSuccess: () => console.log("Язык изменён:", lang),
    });
}
</script>
