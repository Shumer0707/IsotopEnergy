<script setup>
import { useCartStore } from "@/Stores/cart";
import { useCartInputHandlers } from "@/composables/useCartInputHandlers";

const props = defineProps({
    productId: {
        type: [Number, String],
        required: true,
    },
});

const cart = useCartStore();
const { onQtyInput, onQtyBlur } = useCartInputHandlers();
</script>

<template>
    <div v-if="cart.items[productId]" class="flex items-center gap-2">
    <!-- <div class="flex items-center gap-2"> -->
        <!-- Кнопка - -->
        <button
            class="bg-gray-200 px-2 rounded"
            @click="cart.decrement(productId)"
        >
            −
        </button>

        <!-- Инпут -->
        <input
            type="number"
            min="1"
            max="100"
            :value="cart.items[productId]"
            @input="onQtyInput($event, productId)"
            @blur="onQtyBlur($event, productId)"
            class="w-16 border rounded px-2 text-center text-sm"
        />

        <!-- Кнопка + -->
        <button
            class="bg-gray-200 px-2 rounded"
            @click="cart.increment(productId)"
        >
            +
        </button>
        <button
            class="text-xs text-red-500 hover:underline mt-1 self-start"
            @click="cart.remove(productId)"
        >
            Удалить
        </button>
    </div>
</template>
