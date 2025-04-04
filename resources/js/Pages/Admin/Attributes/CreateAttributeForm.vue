<script setup>
import { useForm } from '@inertiajs/vue3';

const emit = defineEmits(['attributeAdded', 'cancel']);

const form = useForm({
    translations: {
        ru: '',
        ro: '',
        en: ''
    }
});

const submit = () => {
    form.post('/admin/attributes/store', {
        onSuccess: () => {
            form.reset();
            emit('attributeAdded');
        }
    });
};
</script>

<template>
    <div class="p-4 bg-gray-100 rounded">
        <h3 class="text-lg font-semibold mb-4">Добавить атрибут</h3>
        <form @submit.prevent="submit">
            <label class="block">Название (RU)</label>
            <input v-model="form.translations.ru" class="w-full p-2 border rounded mb-2" />

            <label class="block">Название (RO)</label>
            <input v-model="form.translations.ro" class="w-full p-2 border rounded mb-2" />

            <label class="block">Название (EN)</label>
            <input v-model="form.translations.en" class="w-full p-2 border rounded mb-4" />

            <div class="flex space-x-2">
                <button type="submit" class="px-4 py-2 bg-purple-600 text-white rounded hover:bg-purple-700">
                    Сохранить
                </button>
                <button type="button" @click="$emit('cancel')" class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600">
                    Отмена
                </button>
            </div>
        </form>
    </div>
</template>
