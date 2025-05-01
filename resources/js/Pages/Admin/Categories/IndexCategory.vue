<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, router, usePage } from "@inertiajs/vue3";
import { ref, watch, nextTick } from "vue";
import CreateCategoryForm from "./CreateCategoryForm.vue";
import EditCategoryForm from "./EditCategoryForm.vue";

// console.log("Flash из usePage():", usePage().props.flash);
const props = defineProps({
    categories: Array,
    parentCategories: Array,
});

const flashMessage = ref(null);

// Отслеживаем изменения flash-сообщения
watch(
    () => usePage().props.flash?.success,
    (newValue) => {
        if (newValue) {
            flashMessage.value = newValue;
            setTimeout(() => (flashMessage.value = null), 3000);
        }
    },
    { immediate: true }
);

const deleteCategory = (id) => {
    if (confirm("Вы уверены, что хотите удалить категорию?")) {
        router.delete(`/admin/categories/${id}`, {
            onSuccess: () => {
                // Сохраняем сообщение перед обновлением страницы
                flashMessage.value = usePage().props.flash?.success;

                // Перезагружаем только категории и flash-сообщения
                router.visit("/admin/categories", {
                    only: ["categories"],
                    preserveScroll: true,
                });
            },
        });
    }
};

const viewMode = ref("list");
const categoryToEdit = ref(null);

const editCategory = (category) => {
    categoryToEdit.value = { ...category };
    viewMode.value = "edit";
};

const refreshList = () => {
    viewMode.value = "list";
    router.reload({ only: ["categories"] });
};

const filters = ref({
    filter: usePage().props.filters?.filter || "",
    parent_id: usePage().props.filters?.parent_id || null,
});

const applyFilter = () => {
    router.get("/admin/categories", filters.value, {
        preserveState: true,
        preserveScroll: true,
    });
};

const resetFilters = () => {
    filters.value = { filter: "", parent_id: null };
    applyFilter();
};
</script>

<template>
    <Head title="Категории" />
    <div
        v-if="flashMessage"
        class="fixed top-5 right-5 bg-green-500 text-white px-4 py-2 rounded-lg shadow-lg z-50"
    >
        {{ flashMessage }}
    </div>
    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Управление категориями
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="bg-white p-6 shadow-sm sm:rounded-lg">
                    <!-- Список категорий -->
                    <div v-if="viewMode === 'list'">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-semibold">
                                Список категорий
                            </h3>
                            <div class="flex items-center space-x-4 mb-4">
                                <div>
                                    <label class="block text-sm font-medium"
                                        >Фильтр</label
                                    >
                                    <select
                                        v-model="filters.filter"
                                        @change="applyFilter"
                                        class="p-2 border rounded"
                                    >
                                        <option value="">Все</option>
                                        <option value="root">
                                            Только категории
                                        </option>
                                        <option value="children">
                                            Только подкатегории
                                        </option>
                                    </select>
                                </div>

                                <div v-if="filters.filter === 'children'">
                                    <label class="block text-sm font-medium"
                                        >Выбрать категорию</label
                                    >
                                    <select
                                        v-model="filters.parent_id"
                                        @change="applyFilter"
                                        class="p-2 border rounded"
                                    >
                                        <option :value="null">
                                            Выберите категорию
                                        </option>
                                        <option
                                            v-for="cat in parentCategories"
                                            :key="cat.id"
                                            :value="cat.id"
                                        >
                                            {{ cat.translation?.name ?? "—" }}
                                        </option>
                                    </select>
                                </div>

                                <button
                                    @click="resetFilters"
                                    class="self-end px-4 py-2 bg-gray-400 text-white rounded hover:bg-gray-500"
                                >
                                    Сбросить фильтр
                                </button>
                            </div>
                            <button
                                @click="viewMode = 'create'"
                                class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700"
                            >
                                Добавить категорию
                            </button>
                        </div>
                        <table
                            class="w-full border-collapse border border-gray-300"
                        >
                            <thead>
                                <tr class="bg-gray-200">
                                    <th
                                        class="border border-gray-300 px-4 py-2"
                                    >
                                        ID
                                    </th>
                                    <th
                                        class="border border-gray-300 px-4 py-2"
                                    >
                                        Название (RU)
                                    </th>
                                    <th
                                        class="border border-gray-300 px-4 py-2"
                                    >
                                        Название (RO)
                                    </th>
                                    <th
                                        class="border border-gray-300 px-4 py-2"
                                    >
                                        Название (EN)
                                    </th>
                                    <th
                                        class="border border-gray-300 px-4 py-2"
                                    >
                                        Действия
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr
                                    v-for="category in categories"
                                    :key="category.id"
                                >
                                    <td
                                        class="border border-gray-300 px-4 py-2"
                                    >
                                        {{ category.id }}
                                    </td>
                                    <td
                                        class="border border-gray-300 px-4 py-2"
                                    >
                                        {{
                                            category.translations.find(
                                                (t) => t.language === "ru"
                                            )?.name ?? "—"
                                        }}
                                    </td>
                                    <td
                                        class="border border-gray-300 px-4 py-2"
                                    >
                                        {{
                                            category.translations.find(
                                                (t) => t.language === "ro"
                                            )?.name ?? "—"
                                        }}
                                    </td>
                                    <td
                                        class="border border-gray-300 px-4 py-2"
                                    >
                                        {{
                                            category.translations.find(
                                                (t) => t.language === "en"
                                            )?.name ?? "—"
                                        }}
                                    </td>
                                    <td
                                        class="border border-gray-300 px-4 py-2 flex space-x-2"
                                    >
                                        <button
                                            @click="editCategory(category)"
                                            class="px-2 py-1 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600"
                                        >
                                            ✏ Редактировать
                                        </button>
                                        <button
                                            @click="deleteCategory(category.id)"
                                            class="px-2 py-1 bg-red-600 text-white rounded-lg hover:bg-red-700"
                                        >
                                            🗑 Удалить
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Форма добавления категории -->
                    <CreateCategoryForm
                        v-if="viewMode === 'create'"
                        @categoryAdded="refreshList"
                        @cancel="viewMode = 'list'"
                        :parentCategories="parentCategories"
                    />

                    <!-- Форма редактирования категории -->
                    <EditCategoryForm
                        v-if="viewMode === 'edit'"
                        :category="categoryToEdit"
                        :parentCategories="parentCategories"
                        @categoryUpdated="refreshList"
                        @cancel="viewMode = 'list'"
                    />
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
