<script setup>
import { useCartStore } from '@/Stores/cart';
import QuantityControl from "@/Components/common/QuantityControl.vue";

const cart = useCartStore();

</script>

<template>
    <div class="max-w-4xl mx-auto p-4">
        <h1 class="text-2xl font-bold mb-4">Корзина</h1>

        <div v-if="Object.keys(cart.items).length === 0" class="text-gray-600">
            Ваша корзина пуста.
        </div>

        <div v-else class="space-y-4">
            <div
                v-for="(quantity, productId) in cart.items"
                :key="productId"
                class="flex justify-between items-center border p-4 rounded"
            >
                <div>
                    <p class="font-medium">Товар ID: {{ productId }}</p>
                    <p class="text-sm text-gray-600">Количество: {{ quantity }}</p>
                </div>

                <QuantityControl :product-id="productId" />
            </div>

            <button
                @click="cart.clear"
                class="mt-6 text-sm text-red-600 hover:underline"
            >
                Очистить корзину
            </button>
        </div>
    </div>
</template>

