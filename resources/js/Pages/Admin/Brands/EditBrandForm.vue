<script setup>
import { useForm } from '@inertiajs/vue3';

const props = defineProps({
    brand: Object
});

const emit = defineEmits(['brandUpdated', 'cancel']);

const form = useForm({ ...props.brand });

const updateBrand = () => {
    form.post(`/admin/brands/update/${form.id}`, {
        onSuccess: () => {
            emit('brandUpdated');
        }
    });
};
</script>

<template>
    <div class="p-4 bg-gray-100 rounded">
        <h3 class="text-lg font-semibold mb-4">Редактировать бренд</h3>
        <form @submit.prevent="updateBrand">
            <label class="block">Название бренда</label>
            <input v-model="form.name" class="w-full p-2 border rounded mb-4" />

            <div class="flex space-x-2">
                <button type="submit" class="px-4 py-2 bg-yellow-600 text-white rounded hover:bg-yellow-700">
                    Обновить
                </button>
                <button type="button" @click="$emit('cancel')" class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600">
                    Отмена
                </button>
            </div>
        </form>
    </div>
</template>
