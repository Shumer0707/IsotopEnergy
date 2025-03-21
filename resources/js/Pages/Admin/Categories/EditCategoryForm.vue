<script setup>
import { useForm } from '@inertiajs/vue3';

const props = defineProps({
    category: Object
});
const emit = defineEmits(['categoryUpdated', 'cancel']);

const form = useForm({ ...props.category });

const updateCategory = () => {
    form.post(`/admin/categories/update/${form.id}`, {
        onSuccess: () => {
            emit('categoryUpdated');
        }
    });
};
</script>

<template>
    <div class="p-4 bg-gray-100 rounded">
        <h3 class="text-lg font-semibold mb-4">Редактировать категорию</h3>
        <form @submit.prevent="updateCategory">
            <label class="block">Название (RU)</label>
            <input v-model="form.name_ru" class="w-full p-2 border rounded mb-2" />

            <label class="block">Название (RO)</label>
            <input v-model="form.name_ro" class="w-full p-2 border rounded mb-2" />

            <label class="block">Название (EN)</label>
            <input v-model="form.name_en" class="w-full p-2 border rounded mb-2" />

            <div class="flex space-x-2 mt-4">
                <button type="submit" class="px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700">
                    Обновить
                </button>
                <button @click="$emit('cancel')" type="button"
                    class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600">
                    Отмена
                </button>
            </div>
        </form>
    </div>
</template>
