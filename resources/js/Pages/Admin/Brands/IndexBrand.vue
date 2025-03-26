<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, router, usePage } from "@inertiajs/vue3";
import { ref, watch } from "vue";
import CreateBrandForm from "./CreateBrandForm.vue";
import EditBrandForm from "./EditBrandForm.vue";

const props = defineProps({
    brands: Array,
});

const flashMessage = ref(null);

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

const viewMode = ref("list");
const brandToEdit = ref(null);

const editBrand = (brand) => {
    brandToEdit.value = { ...brand };
    viewMode.value = "edit";
};

const deleteBrand = (id) => {
    if (confirm("Удалить бренд?")) {
        router.delete(`/admin/brands/${id}`, {
            onSuccess: () => {
                flashMessage.value = usePage().props.flash?.success;
                router.visit("/admin/brands", {
                    only: ["brands"],
                    preserveScroll: true,
                });
            },
        });
    }
};

const refreshList = () => {
    viewMode.value = "list";
    router.reload({ only: ["brands"] });
};
</script>

<template>
    <Head title="Бренды" />
    <div
        v-if="flashMessage"
        class="fixed top-5 right-5 bg-green-500 text-white px-4 py-2 rounded z-50"
    >
        {{ flashMessage }}
    </div>

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold text-gray-800">
                Управление брендами
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white p-6 shadow-sm sm:rounded-lg">
                    <div v-if="viewMode === 'list'">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-semibold">
                                Список брендов
                            </h3>
                            <button
                                @click="viewMode = 'create'"
                                class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700"
                            >
                                Добавить бренд
                            </button>
                        </div>

                        <table class="w-full border border-gray-300">
                            <thead class="bg-gray-200">
                                <tr>
                                    <th class="border px-4 py-2">ID</th>
                                    <th class="border px-4 py-2">Название</th>
                                    <th class="border px-4 py-2">Действия</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="brand in brands" :key="brand.id">
                                    <td class="border px-4 py-2">
                                        {{ brand.id }}
                                    </td>
                                    <td class="border px-4 py-2">
                                        {{ brand.name }}
                                    </td>
                                    <td class="border px-4 py-2 flex space-x-2">
                                        <button
                                            @click="editBrand(brand)"
                                            class="px-2 py-1 bg-yellow-500 text-white rounded hover:bg-yellow-600"
                                        >
                                            ✏ Редактировать
                                        </button>
                                        <button
                                            @click="deleteBrand(brand.id)"
                                            class="px-2 py-1 bg-red-600 text-white rounded hover:bg-red-700"
                                        >
                                            🗑 Удалить
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <CreateBrandForm
                        v-if="viewMode === 'create'"
                        @brandAdded="refreshList"
                        @cancel="viewMode = 'list'"
                    />
                    <EditBrandForm
                        v-if="viewMode === 'edit'"
                        :brand="brandToEdit"
                        @brandUpdated="refreshList"
                        @cancel="viewMode = 'list'"
                    />
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
