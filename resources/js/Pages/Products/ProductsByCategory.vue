<script setup>
import { router } from "@inertiajs/vue3";
import FilterPanel from "@/Components/shared/FilterPanel.vue";
import QuantityControl from "@/Components/common/QuantityControl.vue";
import { useCartStore } from "@/Stores/cart";

const props = defineProps({
    category: Object,
    products: Array,
    brands: Array,
});

const cart = useCartStore();

const openProduct = (id) => {
    router.visit(`/product/${id}`);
};

</script>

<template>
    <div class="flex flex-col lg:flex-row gap-6">
        <FilterPanel :brands="brands" :category-id="category.id" />

        <div class="flex-1">
            <h1 class="text-2xl font-bold mb-4">
                {{ category.translation.name }}
            </h1>

            <div v-if="products.length === 0">
                <p>Нет товаров в этой категории.</p>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
                <div
                    v-for="product in products"
                    :key="product.id"
                    class="border rounded p-4 flex flex-col"
                >
                    <img
                        :src="
                            product.main_image
                                ? `/storage/${product.main_image}`
                                : '/images/placeholder.jpg'
                        "
                        alt=""
                        class="w-full h-32 object-cover mb-2 cursor-pointer"
                        @click="openProduct(product.id)"
                    />
                    <h3 class="text-lg font-semibold">
                        {{ product.description?.title ?? "Без названия" }}
                    </h3>
                    <p class="text-sm">
                        {{ product.price }} {{ product.currency }}
                    </p>
                    <p class="text-sm mb-2">{{ product.brand.name }}</p>

                    <!-- Кнопка корзины -->
                    <button
                        class="mt-auto bg-pink-500 hover:bg-pink-600 text-white py-1 px-3 rounded text-sm transition"
                        @click="cart.add(product.id)"
                    >
                        В корзину
                    </button>
                    <QuantityControl :product-id="product.id" />
                </div>
            </div>
        </div>
    </div>
</template>
