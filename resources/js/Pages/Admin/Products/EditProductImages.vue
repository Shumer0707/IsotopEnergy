<script setup>
import { ref } from "vue";
import { router } from "@inertiajs/vue3";
import axios from "axios";

const props = defineProps({
    product: Object,
});

const emit = defineEmits(["cancel"]);

const uploading = ref(false);
const files = ref([]);
const images = ref([...props.product.images]);
const fileInputRef = ref(null);

const handleFileChange = (e) => {
    files.value = Array.from(e.target.files); // Превращаем FileList в массив
    console.log("files selected:", files.value);
};

const uploadImages = () => {
    console.log("start");

    if (!files.value || files.value.length === 0) {
        console.log("no files");
        return;
    }

    const formData = new FormData();
    files.value.forEach((file) => {
        formData.append("images[]", file);
    });

    router.post(`/admin/products/${props.product.id}/images`, formData, {
        forceFormData: true,
        onFinish: async () => {
            uploading.value = false;

            const res = await fetch(
                `/admin/products/${props.product.id}/images/list`
            );
            const data = await res.json();
            images.value = data.images;

            // Очистим выбранные файлы
            files.value = [];
            if (fileInputRef.value) {
                fileInputRef.value.value = ""; // сбрасываем input[type=file]
            }
        },
    });
};

const deleteImage = async (imageId) => {
    if (!confirm("Удалить это изображение?")) return;

    try {
        await router.delete(`/admin/products/images/${imageId}`, {
            preserveScroll: true,
            onSuccess: async () => {
                const res = await fetch(
                    `/admin/products/${props.product.id}/images/list`
                );
                const data = await res.json();
                images.value = data.images;
            },
        });
    } catch (error) {
        console.error("Ошибка при удалении изображения:", error);
    }
};

const makeMain = async (image) => {
    try {
        await axios.put(`/admin/products/${props.product.id}/main-image`, {
            image_id: image.id,
        });

        props.product.main_image = image.path;
        images.value = images.value.map((img) => ({
            ...img,
            is_main: img.id === image.id,
        }));
    } catch (e) {
        console.error("Ошибка при установке главного изображения:", e);
    }
};
const selectedImage = ref(null);
const showModal = ref(false);

const openImageModal = (image) => {
    selectedImage.value = image;
    showModal.value = true;
};

const closeModal = () => {
    showModal.value = false;
    selectedImage.value = null;
};
</script>

<template>
    <div class="p-6 bg-white rounded shadow">
        <h2 class="text-xl font-semibold mb-4">
            Фото товара: {{ product.id }}
        </h2>

        <!-- Просмотр текущих фото -->
        <div class="flex flex-wrap gap-4 mb-6">
            <div
                v-for="image in images"
                :key="image.id"
                class="relative w-32 h-32 border rounded overflow-hidden"
            >
                <img
                    :src="`/storage/${image.path}`"
                    alt=""
                    class="w-full h-full object-cover cursor-pointer"
                    @click="openImageModal(image)"
                />
                <button
                    @click="deleteImage(image.id)"
                    class="absolute top-1 right-1 bg-red-600 text-white text-xs px-1 rounded"
                >
                    ✖
                </button>
                <div
                    class="absolute bottom-1 left-1 text-xs bg-white rounded px-1"
                >
                    <span
                        v-if="product.main_image === image.path"
                        class="text-green-600 font-bold"
                        >Главное</span
                    >
                    <button
                        v-else
                        @click="makeMain(image)"
                        class="text-blue-600 hover:underline"
                    >
                        Сделать главным
                    </button>
                </div>
            </div>
        </div>

        <!-- Форма загрузки -->
        <div class="mb-4">
            <input
                type="file"
                multiple
                ref="fileInputRef"
                @change="handleFileChange"
            />
        </div>

        <!-- Предпросмотр выбранных файлов -->
        <div v-if="files.length" class="mb-4">
            <p class="text-gray-600">Выбранные файлы:</p>
            <ul class="list-disc pl-5">
                <li v-for="file in files" :key="file.name">
                    {{ file.name }} ({{ (file.size / 1024).toFixed(1) }} KB)
                </li>
            </ul>
        </div>
        <button
            @click="uploadImages"
            :disabled="uploading"
            class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700"
        >
            {{ uploading ? "Загрузка..." : "Загрузить" }}
        </button>

        <button
            @click="$emit('cancel')"
            class="ml-4 px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600"
        >
            Назад
        </button>
    </div>
    <div
        v-if="showModal"
        class="fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center z-50"
        @click.self="closeModal"
    >
        <div class="bg-white p-4 rounded max-w-3xl w-full">
            <img
                :src="`/storage/${selectedImage.path}`"
                alt="Изображение"
                class="w-full max-h-[80vh] object-contain"
            />
            <div class="text-right mt-2">
                <button
                    @click="closeModal"
                    class="text-sm text-gray-600 hover:underline"
                >
                    Закрыть
                </button>
            </div>
        </div>
    </div>
</template>
