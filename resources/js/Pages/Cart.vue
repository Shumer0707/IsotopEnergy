<script setup>
import { useCartStore } from "@/Stores/cart"
import QuantityControl from "@/Components/common/QuantityControl.vue"
import OrderModal from '@/Components/common/OrderModal.vue'
import { onMounted, ref } from 'vue'

const isModalOpen = ref(false)
const cart = useCartStore()

onMounted(() => {
  cart.loadProducts()
})
</script>

<template>
  <div class="max-w-4xl mx-auto p-4">
    <h1 class="text-2xl font-bold mb-4">Корзина</h1>

    <div v-if="Object.keys(cart.items).length === 0" class="text-gray-600">
      Ваша корзина пуста.
    </div>

    <div v-else class="space-y-4">
      <div
        v-for="product in cart.products"
        :key="product.id"
        class="flex justify-between items-center border p-4 rounded"
      >
        <div class="flex items-center gap-4">
          <img
            :src="product.main_image ? `/storage/${product.main_image}` : '/images/placeholder.jpg'"
            alt=""
            class="w-20 h-20 object-cover rounded"
          />
          <div>
            <p class="font-medium text-lg">{{ product.description?.title ?? 'Без названия' }}</p>
            <p class="text-sm text-gray-500">
              {{ product.price }} {{ product.currency }}
            </p>
            <p class="text-sm text-gray-400">
              Кол-во: {{ cart.items[product.id] }}
            </p>
          </div>
        </div>

        <QuantityControl :product-id="product.id" />
      </div>

      <div class="flex justify-between mt-6">
        <button
          @click="cart.clear"
          class="text-sm text-red-600 hover:underline"
        >
          Очистить корзину
        </button>

        <button
          @click="isModalOpen = true"
          class="bg-pink-500 hover:bg-pink-600 text-white px-4 py-2 rounded"
        >
          Оформить заказ
        </button>
      </div>

      <OrderModal v-if="isModalOpen" @close="isModalOpen = false" />
    </div>
  </div>
</template>
