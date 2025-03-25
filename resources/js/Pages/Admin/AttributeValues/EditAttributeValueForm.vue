<script setup>
import { useForm } from '@inertiajs/vue3';

const props = defineProps({
    valueItem: Object,
    attributes: Array
});

const emit = defineEmits(['valueUpdated', 'cancel']);

const form = useForm({ ...props.valueItem });

const submit = () => {
    form.post(`/admin/attribute-values/update/${form.id}`, {
        onSuccess: () => emit('valueUpdated')
    });
};
</script>

<template>
    <div class="p-4 bg-gray-100 rounded">
        <h3 class="text-lg font-semibold mb-4">Редактировать значение</h3>
        <form @submit.prevent="submit">
            <label class="block mb-1">Атрибут</label>
            <select v-model="form.attribute_id" required class="w-full p-2 border rounded mb-4">
                <option v-for="attr in attributes" :key="attr.id" :value="attr.id">
                    {{ attr.name_ru }}
                </option>
            </select>

            <label class="block">Значение (RU)</label>
            <input v-model="form.value_ru" required class="w-full p-2 border rounded mb-2" />

            <label class="block">Значение (RO)</label>
            <input v-model="form.value_ro" required class="w-full p-2 border rounded mb-2" />

            <label class="block">Значение (EN)</label>
            <input v-model="form.value_en" required class="w-full p-2 border rounded mb-4" />

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
