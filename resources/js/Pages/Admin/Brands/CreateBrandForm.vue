<script setup>
import { useForm } from '@inertiajs/vue3';

const emit = defineEmits(['brandAdded', 'cancel']);

const form = useForm({
    name: ''
});

const submitBrand = () => {
    form.post('/admin/brands/store', {
        onSuccess: () => {
            form.reset();
            emit('brandAdded');
        }
    });
};
</script>

<template>
    <div class="p-4 bg-gray-100 rounded">
        <h3 class="text-lg font-semibold mb-4">Добавить бренд</h3>
        <form @submit.prevent="submitBrand">
            <label class="block">Название бренда</label>
            <input v-model="form.name" class="w-full p-2 border rounded mb-4" />

            <div class="flex space-x-2">
                <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">
                    Сохранить
                </button>
                <button type="button" @click="$emit('cancel')" class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600">
                    Отмена
                </button>
            </div>
        </form>
    </div>
</template>
