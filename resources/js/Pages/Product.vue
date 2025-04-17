<script setup>
import QuantityControl from "@/Components/common/QuantityControl.vue";

const props = defineProps({
    product: Object,
});
</script>

<template>
    <div class="max-w-4xl mx-auto p-4">
        <h1 class="text-2xl font-bold mb-4">
            {{ product.description?.title ?? 'Без названия' }}
        </h1>

        <img
            :src="product.main_image ? `/storage/${product.main_image}` : '/images/placeholder.jpg'"
            alt=""
            class="w-full max-h-96 object-cover mb-4 rounded"
        />

        <p class="text-lg font-semibold">
            Цена: {{ product.price }} {{ product.currency }}
        </p>

        <p class="text-gray-600">
            Бренд: {{ product.brand?.name ?? 'Нет бренда' }}
        </p>

        <p class="mt-4">
            {{ product.description?.full_description ?? 'Нет описания.' }}
        </p>

        <!-- Кнопка добавить в корзину -->
        <button
            class="mt-6 bg-pink-500 hover:bg-pink-600 text-white py-2 px-4 rounded transition"
            @click="() => {}"
        >
            Добавить в корзину
        </button>
        <QuantityControl :product-id="product.id" />
    </div>
</template>
