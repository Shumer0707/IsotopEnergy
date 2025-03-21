<script setup>
import { useForm } from '@inertiajs/vue3';

const props = defineProps({
    attribute: Object
});

const emit = defineEmits(['attributeUpdated', 'cancel']);

const form = useForm({ ...props.attribute });

const submit = () => {
    form.post(`/admin/attributes/update/${form.id}`, {
        onSuccess: () => emit('attributeUpdated')
    });
};
</script>

<template>
    <div class="p-4 bg-gray-100 rounded">
        <h3 class="text-lg font-semibold mb-4">Редактировать атрибут</h3>
        <form @submit.prevent="submit">
            <label class="block">Название (RU)</label>
            <input v-model="form.name_ru" class="w-full p-2 border rounded mb-2" />

            <label class="block">Название (RO)</label>
            <input v-model="form.name_ro" class="w-full p-2 border rounded mb-2" />

            <label class="block">Название (EN)</label>
            <input v-model="form.name_en" class="w-full p-2 border rounded mb-4" />

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
