<script setup>
import { useForm } from '@inertiajs/vue3';
import { computed, watch } from 'vue';

const props = defineProps({
    product: Object,
    categories: Array,
    attributes: Array,
    values: Array
});

const emit = defineEmits(['productUpdated', 'cancel']);

const form = useForm({
    id: props.product.id,
    category_id: props.product.category_id ?? '',
    manufacturer: props.product.manufacturer ?? '',
    price: props.product.price ?? '',
    discount_price: props.product.discount_price ?? '',
    currency: props.product.currency ?? 'MDL',
    descriptions: {
        ru: { title: '', short_description: '', full_description: '' },
        ro: { title: '', short_description: '', full_description: '' },
        en: { title: '', short_description: '', full_description: '' },
    },
    attributes: []
});

// Заполняем descriptions и attributes из пропсов
if (Array.isArray(props.product.descriptions)) {
    for (const desc of props.product.descriptions) {
        const lang = desc.language;
        form.descriptions[lang] = {
            title: desc.title,
            short_description: desc.short_description,
            full_description: desc.full_description ?? ''
        };
    }
}

form.attributes = props.product.attributeValues?.map(attr => ({
    attribute_id: attr.attribute_id,
    value_id: attr.attribute_value_id
})) ?? [];

const addAttribute = () => {
    form.attributes.push({ attribute_id: '', value_id: '' });
};

const removeAttribute = (index) => {
    form.attributes.splice(index, 1);
};

const filteredValues = (attrId) => {
    return props.values.filter(v => v.attribute_id === attrId);
};

const submit = () => {
    form.post(`/admin/products/update/${form.id}`, {
        onSuccess: () => emit('productUpdated')
    });
};
console.log('product.attributeValues', props.product.attributeValues);
</script>

<template>
    <div class="p-4 bg-yellow-50 rounded">
        <h3 class="text-lg font-semibold mb-4">Редактировать товар</h3>

        <!-- Категория -->
        <label class="block">Категория</label>
        <select v-model="form.category_id" required class="w-full p-2 border rounded mb-4">
            <option disabled value="">Выберите категорию</option>
            <option v-for="category in categories" :key="category.id" :value="category.id">
                {{ category.name_ru }}
            </option>
        </select>

        <!-- Производитель -->
        <label class="block">Производитель</label>
        <input v-model="form.manufacturer" class="w-full p-2 border rounded mb-4" />

        <!-- Цена и скидка -->
        <div class="flex space-x-4 mb-4">
            <div class="w-1/2">
                <label class="block">Цена</label>
                <input type="number" v-model="form.price" class="w-full p-2 border rounded" />
            </div>
            <div class="w-1/2">
                <label class="block">Цена со скидкой</label>
                <input type="number" v-model="form.discount_price" class="w-full p-2 border rounded" />
            </div>
        </div>

        <!-- Описания -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <div v-for="lang in ['ru', 'ro', 'en']" :key="lang" class="bg-gray-50 p-4 rounded shadow-sm">
                <h4 class="font-semibold mb-2 uppercase">Описание ({{ lang }})</h4>
                <label class="block">Название</label>
                <input v-model="form.descriptions[lang].title" class="w-full p-2 border rounded mb-2" />
                <label class="block">Краткое описание</label>
                <textarea v-model="form.descriptions[lang].short_description" class="w-full p-2 border rounded mb-2" />
                <label class="block">Полное описание</label>
                <textarea v-model="form.descriptions[lang].full_description" class="w-full p-2 border rounded" />
            </div>
        </div>

        <!-- Атрибуты -->
        <div class="mb-6">
            <h4 class="font-semibold mb-2">Атрибуты</h4>
            <div v-for="(attr, index) in form.attributes" :key="index" class="flex space-x-2 items-center mb-2">
                <select v-model="attr.attribute_id" class="w-1/3 p-2 border rounded">
                    <option disabled value="">Атрибут</option>
                    <option v-for="a in attributes" :key="a.id" :value="a.id">
                        {{ a.name_ru }}
                    </option>
                </select>

                <select v-model="attr.value_id" class="w-1/3 p-2 border rounded">
                    <option disabled value="">Значение</option>
                    <option
                        v-for="v in filteredValues(attr.attribute_id)"
                        :key="v.id"
                        :value="v.id"
                    >
                        {{ v.value_ru }}
                    </option>
                </select>

                <button @click="removeAttribute(index)" class="text-red-600 hover:text-red-800 text-xl">✖</button>
            </div>

            <button @click="addAttribute" class="mt-2 px-3 py-1 bg-gray-300 rounded hover:bg-gray-400 text-sm">
                ➕ Добавить атрибут
            </button>
        </div>

        <!-- Кнопки -->
        <div class="flex space-x-2">
            <button type="button" @click="submit" class="px-4 py-2 bg-yellow-600 text-white rounded hover:bg-yellow-700">
                Обновить
            </button>
            <button type="button" @click="$emit('cancel')" class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600">
                Отмена
            </button>
        </div>
    </div>
</template>
