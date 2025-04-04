<script setup>
import { usePage } from "@inertiajs/vue3";

const props = defineProps({
    category: Object,
    products: Array,
});

const locale = usePage().props.locale;
</script>

<template>
    <div>
        <h1 class="text-2xl font-bold mb-4">
            {{ category[`name_${locale}`] }}
        </h1>

        <div v-if="products.length === 0">
            <p>Нет товаров в этой категории.</p>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
            <div
                v-for="product in products"
                :key="product.id"
                class="border rounded p-4"
            >
                <img
                    :src="
                        product.main_image
                            ? `/storage/${product.main_image}`
                            : '/images/placeholder.jpg'
                    "
                    alt=""
                    class="w-full h-32 object-cover mb-2"
                />
                <h3 class="text-lg font-semibold">
                    {{ product.descriptions?.[0]?.title || "Без названия" }}
                </h3>
                <p class="text-sm">
                    {{ product.price }} {{ product.currency }}
                </p>
            </div>
        </div>
    </div>
</template>
