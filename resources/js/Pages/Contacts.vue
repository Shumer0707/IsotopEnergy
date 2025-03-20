<template>
    <Head title="Контакты" />
    <div class="max-w-lg mx-auto mt-10 p-6 bg-white shadow-lg rounded-lg">
        <h1 class="text-xl font-bold mb-4">Контакты</h1>
        <p class="mb-4">Свяжитесь с нами.</p>

        <form @submit.prevent="submit">
            <div class="mb-4">
                <label class="block text-sm font-medium mb-1" for="name">Имя</label>
                <input
                    v-model="form.name"
                    type="text"
                    id="name"
                    class="w-full p-2 border rounded"
                    required
                />
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium mb-1" for="email">Email</label>
                <input
                    v-model="form.email"
                    type="email"
                    id="email"
                    class="w-full p-2 border rounded"
                    required
                />
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium mb-1" for="message">Текст</label>
                <textarea
                    v-model="form.message"
                    id="message"
                    class="w-full p-2 border rounded"
                    rows="4"
                    required
                ></textarea>
            </div>
            <p v-if="errorMessage" class="text-red-500 mt-2">{{ errorMessage }}</p>
            <button
                type="submit"
                class="bg-blue-600 text-white py-2 px-4 rounded hover:bg-blue-700"
            >
                Отправить
            </button>
        </form>
    </div>
</template>

<script setup>
import { Head, router } from '@inertiajs/vue3';
import { reactive, ref } from 'vue';

const form = reactive({
    name: '',
    email: '',
    message: '',
});

const errorMessage = ref('');

const submit = () => {
    errorMessage.value = ''; // Очищаем ошибку перед отправкой

    router.post('/contact', form, {
        onSuccess: () => {
            alert('Сообщение отправлено!');
        },
        onError: (errors) => {
            if (errors.status === 429) {
                errorMessage.value = 'Слишком много запросов. Попробуйте позже.';
            } else {
                console.error(errors);
            }
        }
    });
};
</script>
