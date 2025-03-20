<template>
    <div class="min-h-screen flex flex-col">
        <header class="bg-gray-800 text-white p-4 flex justify-between items-center">
            <nav class="flex space-x-4">
                <div class="relative">
                    <button @click="toggleCategories" class="px-2">Товары</button>
                    <div v-if="showCategories" class="absolute left-0 mt-2 w-48 bg-white text-gray-800 shadow-md">
                        <div v-for="(products, category) in categories" :key="category" class="relative group">
                            <button class="block w-full px-4 py-2 text-left hover:bg-gray-200">{{ category }}</button>
                            <div class="absolute left-full top-0 mt-0 w-48 bg-white shadow-md opacity-0 group-hover:opacity-100 transition-opacity hidden group-hover:block">
                                <Link v-for="product in products" :key="product.id" :href="`/product/${product.id}`" class="block px-4 py-2 hover:bg-gray-200">{{ product.name }}</Link>
                            </div>
                        </div>
                    </div>
                </div>
                <Link href="/" class="px-2">Главная</Link>
                <Link href="/about" class="px-2">О нас</Link>
                <Link href="/contacts" class="px-2">Контакты</Link>
                <Link href="/cart" class="px-2">Корзина</Link>
            </nav>
            <div class="relative group">
                <button class="px-2">Язык</button>
                <div class="absolute right-0 mt-2 w-24 bg-white text-gray-800 shadow-md opacity-0 group-hover:opacity-100 transition-opacity">
                    <button @click="changeLocale('ru')" class="block w-full px-4 py-2 hover:bg-gray-200">RU</button>
                    <button @click="changeLocale('ro')" class="block w-full px-4 py-2 hover:bg-gray-200">RO</button>
                </div>
            </div>
        </header>

        <main class="flex-1 p-4">
            <slot />
        </main>

        <footer class="bg-gray-900 text-white p-4 text-center">
            <nav class="flex justify-center space-x-4">
                <Link href="/" class="px-2">Главная</Link>
                <Link href="/about" class="px-2">О нас</Link>
                <Link href="/contacts" class="px-2">Контакты</Link>
                <Link href="/cart" class="px-2">Корзина</Link>
            </nav>
            &copy; 2025 My Website
        </footer>
    </div>
</template>

<script setup>
import { ref } from 'vue';
import { Link } from '@inertiajs/vue3';
import { router } from '@inertiajs/vue3';

const showCategories = ref(false);
const categories = ref({
    'Дерево': [
        { id: 1, name: 'Брус' },
        { id: 2, name: 'Фанера' }
    ],
    'Металл': [
        { id: 3, name: 'Арматура' },
        { id: 4, name: 'Листовой металл' }
    ],
    'Бетон': [
        { id: 5, name: 'Блоки' },
        { id: 6, name: 'Растворы' }
    ]
});

function toggleCategories() {
    showCategories.value = !showCategories.value;
}

function changeLocale(lang) {
    router.visit(route('set-locale', lang), {
        method: 'get',
        preserveScroll: true,
        onSuccess: () => console.log('Язык изменён:', lang)
    });
}
</script>
