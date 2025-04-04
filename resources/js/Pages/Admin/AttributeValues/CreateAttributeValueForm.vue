<script setup>
import { useForm } from '@inertiajs/vue3';
import { defineProps, defineEmits } from 'vue';

const props = defineProps({
    attributes: Array
});
const emit = defineEmits(['valueAdded', 'cancel']);

const form = useForm({
    attribute_id: '',
    translations: {
        ru: '',
        ro: '',
        en: ''
    }
});

const submit = () => {
    form.post('/admin/attribute-values/store', {
        onSuccess: () => {
            form.reset();
            emit('valueAdded');
        }
    });
};
</script>

<template>
    <div class="p-4 bg-gray-100 rounded">
        <h3 class="text-lg font-semibold mb-4">Добавить значение атрибута</h3>
        <form @submit.prevent="submit">
            <label class="block mb-1">Атрибут</label>
            <select v-model="form.attribute_id" required class="w-full p-2 border rounded mb-4">
                <option disabled value="">Выберите атрибут</option>
                <option v-for="attr in attributes" :key="attr.id" :value="attr.id">
                    {{ attr.translation?.name ?? '—' }}
                </option>
            </select>

            <label class="block">Значение (RU)</label>
            <input v-model="form.translations.ru" required class="w-full p-2 border rounded mb-2" />

            <label class="block">Значение (RO)</label>
            <input v-model="form.translations.ro" required class="w-full p-2 border rounded mb-2" />

            <label class="block">Значение (EN)</label>
            <input v-model="form.translations.en" required class="w-full p-2 border rounded mb-4" />

            <div class="flex space-x-2">
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">
                    Сохранить
                </button>
                <button type="button" @click="$emit('cancel')" class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600">
                    Отмена
                </button>
            </div>
        </form>
    </div>
</template>
