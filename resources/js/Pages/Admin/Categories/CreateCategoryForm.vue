<script setup>
import { useForm } from '@inertiajs/vue3';

const props = defineProps({
    parentCategories: Array
});

const emit = defineEmits(['categoryAdded', 'cancel']);

const form = useForm({
    name_ru: '',
    name_ro: '',
    name_en: '',
    parent_id: null
});

const submitCategory = () => {
    form.post('/admin/categories/store', {
        onSuccess: () => {
            form.reset();
            emit('categoryAdded');
        }
    });
};
</script>

<template>
    <div class="p-4 bg-gray-100 rounded">
        <h3 class="text-lg font-semibold mb-4">Добавить категорию</h3>
        <form @submit.prevent="submitCategory">
            <label class="block">Название (RU)</label>
            <input v-model="form.name_ru" class="w-full p-2 border rounded mb-2" />

            <label class="block">Название (RO)</label>
            <input v-model="form.name_ro" class="w-full p-2 border rounded mb-2" />

            <label class="block">Название (EN)</label>
            <input v-model="form.name_en" class="w-full p-2 border rounded mb-2" />

            <label class="block mt-2">Родительская категория (необязательно)</label>
            <select v-model="form.parent_id" class="w-full p-2 border rounded mb-2">
                <option :value="null">Без родителя</option>
                <option v-for="cat in parentCategories" :key="cat.id" :value="cat.id">
                    {{ cat.name_ru }}
                </option>
            </select>

            <div class="flex space-x-2 mt-4">
                <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                    Сохранить
                </button>
                <button @click="$emit('cancel')" type="button"
                    class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600">
                    Отмена
                </button>
            </div>
        </form>
    </div>
</template>
