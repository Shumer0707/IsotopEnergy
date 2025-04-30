<script setup>
import { ref } from 'vue';
import { useCartStore } from '@/Stores/cart';

const emit = defineEmits(['close']);
const cart = useCartStore();

const name = ref('');
const phone = ref('');
const comment = ref('');
const loading = ref(false);
const error = ref(null);

const submitOrder = async () => {
    loading.value = true;
    error.value = null;

    try {
        await axios.post('/order', {
            name: name.value,
            phone: phone.value,
            comment: comment.value,
            cart: cart.items,
        });

        cart.clear();
        emit('close');
        alert('✅ Заказ отправлен!');
    } catch (err) {
        error.value = 'Ошибка отправки заказа';
    } finally {
        loading.value = false;
    }
};
</script>

<template>
    <div class="fixed inset-0 bg-black/40 z-50 flex items-center justify-center">
        <div class="bg-white rounded-xl p-6 w-full max-w-md shadow-lg relative">
            <button class="absolute top-2 right-3 text-gray-400 hover:text-black" @click="emit('close')">×</button>

            <h2 class="text-xl font-bold mb-4">Оформить заказ</h2>

            <div class="space-y-4">
                <input v-model="name" type="text" placeholder="Имя" class="w-full border rounded p-2" />
                <input v-model="phone" type="text" placeholder="Телефон" class="w-full border rounded p-2" />
                <textarea v-model="comment" placeholder="Комментарий" class="w-full border rounded p-2"></textarea>

                <p v-if="error" class="text-red-500 text-sm">{{ error }}</p>

                <button
                    @click="submitOrder"
                    :disabled="loading"
                    class="bg-pink-500 hover:bg-pink-600 text-white px-4 py-2 rounded w-full"
                >
                    {{ loading ? 'Отправка...' : 'Отправить заказ' }}
                </button>
            </div>
        </div>
    </div>
</template>
