<script setup>
  import { computed } from 'vue'
  import { useCartMiniUiStore } from '@/Stores/cartMiniUi'
  import { useCartStore } from '@/Stores/cart'
  import { router } from '@inertiajs/vue3'
  import { useLayoutStore } from '@/Stores/layout'

  const layout = useLayoutStore()
  const cart = useCartStore()
  const cartMiniUi = useCartMiniUiStore()

  const totalWithDiscount = computed(() =>
    cart.products.reduce((sum, p) => {
      const price = parseFloat(p.discounted_price ?? p.price)
      return sum + price * cart.items[p.id]
    }, 0)
  )

  const goToCart = () => {
    cartMiniUi.closeMiniCart()
    router.visit('/cart')
  }

  const toggleMiniCart = () => {
    cartMiniUi.toggleMiniCart()
  }

  const totalQuantity = computed(() => Object.values(cart.items).reduce((sum, qty) => sum + qty, 0))
</script>

<template>
  <div class="relative">
    <!-- Кнопка -->
    <div ref="cartButtonRef">
      <button @click="toggleMiniCart" class="gap-1 flex flex-raw items-center sm:p-1 text-3xl rounded-md hover:bg-gray-700">
        <font-awesome-icon icon="shopping-cart" class="lg:text-3xl text-2xl" />
        <font-awesome-icon icon="chevron-down" class="text-xs" />
      </button>
      <!-- <button @click="toggleMiniCart" class="sm:p-1 text-3xl rounded-md hover:bg-gray-700">🛒</button>
      <font-awesome-icon icon="chevron-down" class="text-xs" /> -->
      <span
        v-if="totalQuantity > 0"
        class="absolute -top-1 -left-1 bg-pink-600 text-white text-xs font-bold rounded-full w-5 h-5 flex items-center justify-center"
      >
        {{ totalQuantity }}
      </span>
    </div>

    <!-- Модалка -->
    <transition
      enter-active-class="transition duration-300 ease-out"
      enter-from-class="opacity-0 -translate-y-4"
      enter-to-class="opacity-100 translate-y-0"
      leave-active-class="transition duration-200 ease-in"
      leave-from-class="opacity-100 translate-y-0"
      leave-to-class="opacity-0 -translate-y-4"
    >
      <div v-if="cartMiniUi.isMiniCartOpen" class="fixed inset-0 z-30 bg-black/50" @click.self="cartMiniUi.closeMiniCart">
        <!-- Панель -->
        <div
          class="absolute right-3 z-40 transform transition-all duration-300 ease-out"
          :style="{ top: `${layout.headerBottom}px` }"
        >
          <div
            class="w-[300px] max-h-[50vh] h-auto bg-white rounded-md shadow-md border border-gray-200 p-3 overflow-y-auto text-sm"
          >
            <div class="flex justify-between items-center mb-2 relative">
              <h2 class="font-semibold text-center w-full">Корзина</h2>
              <button
                @click="cartMiniUi.closeMiniCart"
                class="text-lg absolute right-2 top-0 text-gray-500 hover:text-gray-700 leading-none"
              >
                &times;
              </button>
            </div>

            <div v-if="cart.products.length === 0" class="text-gray-500 text-center">Пусто</div>

            <div v-else class="space-y-3">
              <div v-for="product in cart.products" :key="product.id" class="flex items-center gap-2 border-t pt-2">
                <img
                  :src="product.main_image ? `/storage/${product.main_image}` : '/images/placeholder.jpg'"
                  alt=""
                  class="w-12 h-12 object-cover rounded bg-gray-100"
                />
                <div class="flex-1">
                  <p class="text-gray-500 text-xs">
                    {{ product.description?.title ?? '❌ нет title' }} x {{ cart.items[product.id] }} —
                    {{ (product.discounted_price ?? product.price) * cart.items[product.id] }} mdl
                  </p>
                </div>
              </div>

              <hr />

              <div class="flex justify-between font-semibold">
                <span>Итого:</span>
                <span class="text-pink-600">{{ totalWithDiscount.toFixed(2) }} mdl</span>
              </div>

              <button
                class="w-full bg-gray-700 text-white py-1.5 rounded text-sm font-semibold hover:bg-gray-800"
                @click="goToCart"
              >
                Оформить
              </button>

              <button
                @click="cartMiniUi.closeMiniCart"
                class="w-full border border-gray-400 text-gray-700 py-1.5 rounded text-sm font-semibold hover:bg-gray-100"
              >
                Проверить
              </button>
            </div>
          </div>
        </div>
      </div>
    </transition>
  </div>
</template>
