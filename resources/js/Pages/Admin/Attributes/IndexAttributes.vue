<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, router, usePage } from "@inertiajs/vue3";
import CreateAttributeForm from "./CreateAttributeForm.vue";
import EditAttributeForm from "./EditAttributeForm.vue";
import { ref, watch } from "vue";

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
const props = defineProps({
    attributes: Array,
});

const viewMode = ref("list");
const attributeToEdit = ref(null);

const refreshList = () => {
    viewMode.value = "list";
    router.reload({ only: ["attributes"] });
};

const editAttribute = (attribute) => {
    attributeToEdit.value = { ...attribute };
    viewMode.value = "edit";
};
const deleteAttribute = (id) => {
    if (confirm("Удалить атрибут?")) {
        router.delete(`/admin/attributes/${id}`, {
            onSuccess: () => {
                flashMessage.value = usePage().props.flash?.success;
                router.visit("/admin/attributes", {
                    only: ["attributes"],
                    preserveScroll: true,
                });
            },
        });
    }
};
</script>

<template>
    <Head title="Атрибуты" />
    <div
        v-if="flashMessage"
        class="fixed top-5 right-5 bg-green-500 text-white px-4 py-2 rounded-lg shadow-lg z-50"
    >
        {{ flashMessage }}
    </div>
    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Управление атрибутами
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="bg-white p-6 shadow-sm sm:rounded-lg">
                    <div v-if="viewMode === 'list'">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-semibold">
                                Список атрибутов
                            </h3>
                            <button
                                @click="viewMode = 'create'"
                                class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700"
                            >
                                Добавить атрибут
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
                                        RU
                                    </th>
                                    <th
                                        class="border border-gray-300 px-4 py-2"
                                    >
                                        RO
                                    </th>
                                    <th
                                        class="border border-gray-300 px-4 py-2"
                                    >
                                        EN
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="attr in attributes" :key="attr.id">
                                    <td
                                        class="border border-gray-300 px-4 py-2"
                                    >
                                        {{ attr.id }}
                                    </td>
                                    <td class="border px-4 py-2">
                                        {{
                                            attr.translations.find(
                                                (t) => t.language === "ru"
                                            )?.name ?? "—"
                                        }}
                                    </td>
                                    <td class="border px-4 py-2">
                                        {{
                                            attr.translations.find(
                                                (t) => t.language === "ro"
                                            )?.name ?? "—"
                                        }}
                                    </td>
                                    <td class="border px-4 py-2">
                                        {{
                                            attr.translations.find(
                                                (t) => t.language === "en"
                                            )?.name ?? "—"
                                        }}
                                    </td>
                                    <td
                                        class="border border-gray-300 px-4 py-2 flex space-x-2"
                                    >
                                        <button
                                            @click="editAttribute(attr)"
                                            class="px-2 py-1 bg-yellow-500 text-white rounded hover:bg-yellow-600"
                                        >
                                            ✏ Редактировать
                                        </button>
                                        <button
                                            @click="deleteAttribute(attr.id)"
                                            class="px-2 py-1 bg-red-600 text-white rounded hover:bg-red-700"
                                        >
                                            🗑 Удалить
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <CreateAttributeForm
                        v-if="viewMode === 'create'"
                        @attributeAdded="refreshList"
                        @cancel="viewMode = 'list'"
                    />
                    <EditAttributeForm
                        v-if="viewMode === 'edit'"
                        :attribute="attributeToEdit"
                        @attributeUpdated="refreshList"
                        @cancel="viewMode = 'list'"
                    />
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
