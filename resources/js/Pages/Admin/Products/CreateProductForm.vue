<script setup>
import { useForm } from "@inertiajs/vue3";

const emit = defineEmits(["productAdded", "cancel"]);

const props = defineProps({
    categories: Array,
    attributes: Array,
    values: Array,
    brands: Array,
});

const form = useForm({
    category_id: "",
    brand_id: "",
    price: "",
    discount_price: "",
    currency: "MDL",
    main_image: "", // строка пути (в будущем может быть upload)
    descriptions: {
        ru: { title: "", short_description: "", full_description: "" },
        ro: { title: "", short_description: "", full_description: "" },
        en: { title: "", short_description: "", full_description: "" },
    },
    attributes: [],
});

const submit = () => {
    form.post("/admin/products/store", {
        onSuccess: () => {
            form.reset();
            emit("productAdded");
        },
    });
};

const addAttribute = () => {
    form.attributes.push({ attribute_id: "", value_id: "" });
};

const removeAttribute = (index) => {
    form.attributes.splice(index, 1);
};

const filteredValues = (attrId) => {
    return props.values.filter((v) => v.attribute_id === attrId);
};
</script>

<template>
    <div class="p-4 bg-gray-100 rounded">
        <h3 class="text-lg font-semibold mb-4">Добавить товар</h3>
        <form @submit.prevent="submit">
            <label class="block mb-1">Категория</label>
            <select
                v-model="form.category_id"
                required
                class="w-full p-2 border rounded mb-4"
            >
                <option disabled value="">Выберите категорию</option>
                <option
                    v-for="category in categories"
                    :key="category.id"
                    :value="category.id"
                >
                    {{ category.name_ru }}
                </option>
            </select>
            <label class="block">Бренд</label>
            <select
                v-model="form.brand_id"
                class="w-full p-2 border rounded mb-4"
            >
                <option disabled value="">Выберите бренд</option>
                <option
                    v-for="brand in brands"
                    :key="brand.id"
                    :value="brand.id"
                >
                    {{ brand.name }}
                </option>
            </select>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div
                    v-for="lang in ['ru', 'ro', 'en']"
                    :key="lang"
                    class="bg-gray-50 p-4 rounded shadow-sm"
                >
                    <h4 class="font-semibold mb-2 uppercase">
                        Описание ({{ lang }})
                    </h4>

                    <label class="block">Название</label>
                    <input
                        v-model="form.descriptions[lang].title"
                        class="w-full p-2 border rounded mb-2"
                    />

                    <label class="block">Краткое описание</label>
                    <textarea
                        v-model="form.descriptions[lang].short_description"
                        class="w-full p-2 border rounded mb-2"
                    />
<!--
                    <label class="block">Полное описание</label>
                    <textarea
                        v-model="form.descriptions[lang].full_description"
                        class="w-full p-2 border rounded"
                    /> -->
                </div>
            </div>

            <label class="block">Цена</label>
            <input
                type="number"
                v-model="form.price"
                class="w-full p-2 border rounded mb-4"
            />

            <div class="mt-6">
                <h4 class="font-semibold mb-2">Атрибуты товара</h4>

                <div
                    v-for="(attr, index) in form.attributes"
                    :key="index"
                    class="flex space-x-2 items-center mb-2"
                >
                    <select
                        v-model="attr.attribute_id"
                        class="w-1/3 p-2 border rounded"
                    >
                        <option disabled value="">Атрибут</option>
                        <option
                            v-for="a in attributes"
                            :key="a.id"
                            :value="a.id"
                        >
                            {{ a.name_ru }}
                        </option>
                    </select>

                    <select
                        v-model="attr.value_id"
                        class="w-1/3 p-2 border rounded"
                    >
                        <option disabled value="">Значение</option>
                        <option
                            v-for="v in filteredValues(attr.attribute_id)"
                            :key="v.id"
                            :value="v.id"
                        >
                            {{ v.value_ru }}
                        </option>
                    </select>

                    <button
                        @click="removeAttribute(index)"
                        class="text-red-600 hover:text-red-800 text-xl"
                    >
                        ✖
                    </button>
                </div>

                <button
                    type="button"
                    @click="addAttribute"
                    class="mt-2 px-3 py-1 bg-gray-300 rounded hover:bg-gray-400 text-sm"
                >
                    ➕ Добавить атрибут
                </button>
            </div>
            <div class="flex space-x-2">
                <button
                    type="submit"
                    class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700"
                >
                    Сохранить
                </button>
                <button
                    type="button"
                    @click="$emit('cancel')"
                    class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600"
                >
                    Отмена
                </button>
            </div>
        </form>
    </div>
</template>
