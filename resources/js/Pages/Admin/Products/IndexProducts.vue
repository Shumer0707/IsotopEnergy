<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, router, usePage } from "@inertiajs/vue3";
import { ref, watch } from "vue";
import CreateProductForm from "./CreateProductForm.vue";
import EditProductForm from "./EditProductForm.vue";
import EditProductImages from "./EditProductImages.vue";

const props = defineProps({
    products: Array,
    categories: Array,
    attributes: Array,
    brands: Array,
    values: Array,
});

const viewMode = ref("list");
const productToEdit = ref(null);
const flashMessage = ref(null);
const selectedCategory = ref("");

watch(selectedCategory, (newVal) => {
    router.get(
        "/admin/products",
        { category: newVal },
        {
            preserveScroll: true,
            preserveState: true,
            only: ["products"],
        }
    );
});

watch(
    () => usePage().props.flash?.success,
    (newVal) => {
        if (newVal) {
            flashMessage.value = newVal;
            setTimeout(() => (flashMessage.value = null), 3000);
        }
    },
    { immediate: true }
);

const refreshList = () => {
    viewMode.value = "list";
    router.reload({ only: ["products"] });
};

const editProduct = (product) => {
    productToEdit.value = JSON.parse(JSON.stringify(product)); // глубокая копия
    viewMode.value = "edit";
};

const deleteProduct = (id) => {
    if (confirm("Удалить товар?")) {
        router.delete(`/admin/products/${id}`, {
            onSuccess: () => router.reload({ only: ["products"] }),
        });
    }
};

const manageImages = (product) => {
    productToEdit.value = JSON.parse(JSON.stringify(product));
    viewMode.value = "images";
};
</script>

<template>
    <Head title="Товары" />
    <div
        v-if="flashMessage"
        class="fixed top-5 right-5 bg-green-500 text-white px-4 py-2 rounded shadow-lg z-50"
    >
        {{ flashMessage }}
    </div>
    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Товары
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div v-if="viewMode === 'list'">
                    <div class="mb-4 flex items-center gap-4">
                        <div>
                            <label class="block text-sm mb-1"
                                >Фильтр по категории</label
                            >
                            <select
                                v-model="selectedCategory"
                                class="p-2 border rounded w-64"
                            >
                                <option value="">— Все категории —</option>
                                <option
                                    v-for="cat in categories"
                                    :key="cat.id"
                                    :value="cat.id"
                                >
                                    {{ cat.name_ru }}
                                </option>
                            </select>
                        </div>

                        <button
                            v-if="selectedCategory"
                            @click="selectedCategory = ''"
                            class="mt-6 px-3 py-2 bg-gray-300 hover:bg-gray-400 rounded text-sm"
                        >
                            ❌ Сбросить фильтр
                        </button>
                    </div>
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold">Список товаров</h3>
                        <button
                            @click="viewMode = 'create'"
                            class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700"
                        >
                            ➕ Добавить товар
                        </button>
                    </div>

                    <table
                        class="w-full border-collapse border border-gray-300"
                    >
                        <thead>
                            <tr class="bg-gray-200">
                                <th class="border px-4 py-2">ID</th>
                                <th class="border px-4 py-2">Название (RU)</th>
                                <th class="border px-4 py-2">Категория</th>
                                <th class="border px-4 py-2">Цена</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="product in products" :key="product.id">
                                <td class="border px-4 py-2">
                                    {{ product.id }}
                                </td>
                                <td class="border px-4 py-2">
                                    {{ product.name_ru }}
                                </td>
                                <td class="border px-4 py-2">
                                    {{ product.category?.name_ru }}
                                </td>
                                <td class="border px-4 py-2">
                                    {{ product.price }}
                                </td>
                                <td class="border px-4 py-2">
                                    <button
                                        @click="editProduct(product)"
                                        class="px-2 py-1 bg-yellow-500 text-white rounded hover:bg-yellow-600"
                                    >
                                        ✏ Редактировать
                                    </button>
                                    <button
                                        @click="deleteProduct(product.id)"
                                        class="px-2 py-1 bg-red-600 text-white rounded hover:bg-red-700"
                                    >
                                        🗑 Удалить
                                    </button>
                                    <button
                                        @click="manageImages(product)"
                                        class="px-2 py-1 bg-indigo-600 text-white rounded hover:bg-indigo-700"
                                    >
                                        🖼 Фото
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <CreateProductForm
                    v-if="viewMode === 'create'"
                    :categories="categories"
                    :attributes="attributes"
                    :values="values"
                    :brands="brands"
                    @productAdded="refreshList"
                    @cancel="viewMode = 'list'"
                />
                <EditProductForm
                    v-if="viewMode === 'edit'"
                    :product="productToEdit"
                    :categories="categories"
                    :attributes="attributes"
                    :values="values"
                    :brands="brands"
                    @productUpdated="refreshList"
                    @cancel="viewMode = 'list'"
                />
                <EditProductImages
                    v-if="viewMode === 'images'"
                    :product="productToEdit"
                    @cancel="viewMode = 'list'"
                />
            </div>
        </div>
    </AuthenticatedLayout>
</template>
