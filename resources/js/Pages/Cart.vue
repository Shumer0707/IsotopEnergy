<script setup>
  import { useCartStore } from '@/Stores/cart'
  import QuantityControl from '@/Components/common/QuantityControl.vue'
  import OrderModal from '@/Components/common/OrderModal.vue'
  import { onMounted, ref, computed } from 'vue'
  import { router } from '@inertiajs/vue3'

  const isModalOpen = ref(false)
  const cart = useCartStore()
  const totalQuantity = computed(() => Object.values(cart.items).reduce((sum, qty) => sum + qty, 0))

  const totalWithoutDiscount = computed(() => cart.products.reduce((sum, p) => sum + p.price * cart.items[p.id], 0))

  const totalDiscount = computed(() => totalWithoutDiscount.value - totalWithDiscount.value)

  const totalWithDiscount = computed(() =>
    cart.products.reduce((sum, p) => {
      const price = parseFloat(p.discounted_price ?? p.price)
      return sum + price * cart.items[p.id]
    }, 0)
  )

  const openProduct = (id) => {
    router.visit(`/product/${id}`)
  }

  onMounted(() => {
    cart.init()
  })
</script>

<template>
  <div class="max-w-7xl mx-auto px-4 py-8 grid grid-cols-1 lg:grid-cols-3 gap-8">
    <!-- 🛒 Таблица товаров -->
    <div class="lg:col-span-2">
      <h1 class="text-2xl font-bold mb-6 text-center">Корзина покупок</h1>

      <div v-if="Object.keys(cart.items).length === 0" class="text-gray-600 text-center">Ваша корзина пуста.</div>

      <div v-else>
        <!-- Заголовки -->
        <div class="hidden lg:grid grid-cols-14 font-semibold border-b pb-2 mb-4 text-sm text-gray-700">
          <div class="col-span-6">Товар</div>
          <div class="col-span-2 text-center">Кол-во</div>
          <div class="col-span-2 text-right">Цена за шт.</div>
          <div class="col-span-2 text-right">Всего</div>
          <div class="col-span-2 text-right">Жопа</div>
        </div>

        <!-- Список товаров -->
        <div
          v-for="product in cart.products"
          :key="product.id"
          class="flex flex-col lg:grid lg:grid-cols-14 gap-4 border-t py-4 items-center lg:min-h-[88px]"
        >
          <!-- 📦 Фото + Название -->
          <div class="flex gap-4 items-center lg:col-span-6">
            <img
              :src="product.main_image ? `/storage/${product.main_image}` : '/images/placeholder.jpg'"
              alt=""
              @click="openProduct(product.id)"
              class="w-20 h-20 object-cover rounded shrink-0 cursor-pointer"
            />
            <div class="flex flex-col justify-center">
              <p class="font-medium text-sm sm:text-base">{{ product.description?.title ?? 'Без названия' }}</p>
              <p class="text-xs text-gray-400 mt-1">Арт: {{ product.code ?? product.id }}</p>

              <!-- 🔹 Инфо для мобилки -->
              <div class="mt-2 space-y-1 text-sm text-gray-700 lg:hidden">
                <div>
                  Цена:
                  <p v-if="product.price !== product.discounted_price" class="line-through text-gray-400">
                    {{ product.price }} mdl
                  </p>
                  <p class="ml-1 font-semibold">{{ product.discounted_price ?? product.price }} mdl</p>
                </div>
                <p>Кол-во: {{ cart.items[product.id] }}</p>
                <div class="flex justify-center items-center">
                  <QuantityControl :product-id="product.id" small />
                </div>
                <p class="font-semibold">Всего: {{ (product.discounted_price ?? product.price) * cart.items[product.id] }} mdl</p>
              </div>
            </div>
          </div>

          <!-- ➖➕ Кол-во -->
          <div class="hidden lg:flex lg:col-span-2 justify-center items-center">
            <QuantityControl :product-id="product.id" :cross="false" />
          </div>

          <!-- 💰 Цена -->
          <div class="hidden lg:flex items-center justify-end lg:col-span-2">
            <div class="text-sm text-right leading-snug">
              <p v-if="product.price !== product.discounted_price" class="line-through text-gray-400">{{ product.price }} mdl</p>
              <p class="font-semibold">{{ product.discounted_price ?? product.price }} mdl</p>
            </div>
          </div>

          <!-- 🧮 Всего -->
          <div class="hidden lg:flex items-center justify-end lg:col-span-2">
            <p class="text-sm font-semibold">{{ (product.discounted_price ?? product.price) * cart.items[product.id] }} mdl</p>
          </div>

          <!-- ❌ Удалить -->
          <div class="hidden lg:flex items-center justify-end lg:col-span-2">
            <button class="text-xl text-red-500 hover:text-red-700" @click="cart.remove(product.id)" title="Удалить товар">
              ✖
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- 📦 Боковая колонка с итогами -->
    <div class="bg-gray-100 rounded-xl p-6 h-fit lg:col-span-1 w-full lg:w-auto mt-6 lg:mt-0">
      <h2 class="font-semibold text-lg mb-4 border-b pb-2">Детали заказа</h2>

      <div class="flex justify-between text-sm mb-2">
        <span>Всего товаров:</span>
        <span>{{ totalQuantity }}</span>
      </div>

      <div class="flex justify-between text-sm mb-2">
        <span>Цена без скидки:</span>
        <span>{{ totalWithoutDiscount.toFixed(2) }} mdl</span>
      </div>

      <div class="flex justify-between text-sm mb-2">
        <span>Скидка:</span>
        <span class="text-green-600">-{{ totalDiscount.toFixed(2) }} mdl</span>
      </div>

      <div class="flex justify-between font-bold text-lg mb-4">
        <span>Итоговая цена:</span>
        <span class="text-pink-600">{{ totalWithDiscount.toFixed(2) }} mdl</span>
      </div>

      <button @click="isModalOpen = true" class="w-full bg-gray-600 text-white py-2 rounded hover:bg-gray-700">
        Оформить заказ
      </button>
    </div>
  </div>
  <OrderModal v-if="isModalOpen" @close="isModalOpen = false" />
</template>
