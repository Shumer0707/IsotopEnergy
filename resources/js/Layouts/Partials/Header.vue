<template>
    <header class="text-white sticky top-0 left-0 w-full z-50">
        <!-- Верхний бар -->
        <div
            class="bg-gray-700 text-base lg:text-md py-3 px-12 flex justify-between items-center"
        >
            <nav class="flex space-x-12 lg:space-x-20">
                <Link href="/" class="hover:underline">Главная</Link>
                <Link href="/about" class="hover:underline">О нас</Link>
                <Link href="/contacts" class="hover:underline">Контакты</Link>
                <Link href="/cart" class="hover:underline">Корзина</Link>
            </nav>
            <div class="flex items-center space-x-2">
                <span class="text-lg">📞</span>
                <span class="text-base lg:text-lg">+373 699 77 771</span>
            </div>
        </div>

        <!-- Основной хедер -->
        <div
            class="bg-gray-800 py-6 px-12 flex flex-col lg:flex-row lg:justify-between lg:items-center space-y-4 lg:space-y-0"
        >
            <!-- Левая часть: Лого и Каталог -->
            <div class="flex items-center space-x-6">
                <span class="text-5xl font-bold">LOGO</span>
                <CategoryNav />
            </div>

            <!-- Центр: Поиск + языки -->
            <div
                class="flex-1 flex flex-col lg:flex-row items-center justify-center space-y-3 lg:space-y-0 lg:space-x-4"
            >
                <div class="relative w-full 2xl:w-[600px] xl:w-[500px] lg:w-[300px]">
                    <input
                        type="text"
                        placeholder="Поиск по товарам..."
                        class="w-full pl-4 pr-12 py-3 rounded-xl text-black placeholder-gray-500 focus:outline-none"
                    />
                    <button
                        class="absolute right-2 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-gray-700"
                    >
                        🔍
                    </button>
                </div>
                <div class="flex items-center space-x-2">
                    <span class="text-2xl">🌐</span>
                    <button
                        @click="changeLocale('ro')"
                        class="hover:underline text-sm"
                    >
                        RO
                    </button>
                    <button
                        @click="changeLocale('ru')"
                        class="hover:underline text-sm"
                    >
                        RU
                    </button>
                    <button
                        @click="changeLocale('en')"
                        class="hover:underline text-sm"
                    >
                        EN
                    </button>
                </div>
            </div>

            <!-- Справа: Избранное и Корзина -->
            <div class="flex items-center space-x-6">
                <Link href="/favorites" class="text-3xl hover:text-gray-300"
                    >❤️</Link
                >
                <Link href="/cart" class="text-3xl hover:text-gray-300">🛒</Link>
            </div>
        </div>
    </header>
</template>

<script setup>
import { Link, router } from "@inertiajs/vue3";
import CategoryNav from '@/Components/shared/CategoryNav.vue'

function changeLocale(lang) {
    router.visit(route("set-locale", lang), {
        method: "get",
        preserveScroll: true,
        onSuccess: () => console.log("Язык изменён:", lang),
    });
}
</script>
