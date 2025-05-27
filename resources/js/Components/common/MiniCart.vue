<script setup>
  import { ref, onMounted, watch, nextTick, computed } from 'vue'
  import { useCartMiniUiStore } from '@/Stores/cartMiniUi'
  import { useCartStore } from '@/Stores/cart'
  import { router } from '@inertiajs/vue3'
  import { useLayoutStore } from '@/Stores/layout'

  const layout = useLayoutStore()
  const cart = useCartStore()
  const cartMiniUi = useCartMiniUiStore()
  const cartButtonRef = ref(null)

  const updateOffset = () => {
    if (cartButtonRef.value) {
      const rect = cartButtonRef.value.getBoundingClientRect()
      layout.setCartButtonRightOffset(window.innerWidth - rect.right)
    }
  }

  onMounted(() => {
    nextTick(updateOffset)
  })

  watch(() => layout.headerBottom, updateOffset) // на всякий случай

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
    <div
      v-show="cartMiniUi.isMiniCartOpen"
      class="fixed inset-0 z-20 bg-black/10 backdrop-blur-sm transition-opacity duration-300"
      @click="cartMiniUi.closeMiniCart"
    ></div>

    <div
      v-show="cartMiniUi.isMiniCartOpen"
      class="fixed z-30 overflow-hidden"
      :style="{
        top: `${layout.headerBottom}px`,
        right: `${layout.cartButtonRightOffset}px`,
        width: '300px',
      }"
    >
      <!-- Анимация ТОЛЬКО внутренней панели -->
      <transition
        enter-active-class="transition duration-300 ease-out"
        enter-from-class="-translate-y-full opacity-0"
        enter-to-class="translate-y-0 opacity-100"
        leave-active-class="transition duration-200 ease-in"
        leave-from-class="translate-y-0 opacity-100"
        leave-to-class="-translate-y-full opacity-0"
      >
        <div
          v-if="cartMiniUi.isMiniCartOpen"
          ref="miniCartRef"
          class="overflow-y-auto scrollbar-thin bg-more rounded-md shadow-xl border border-gray-700 p-3 overflow-y-auto text-white text-sm w-full h-auto max-h-[50vh]"
        >
          <div class="flex justify-between items-center mb-2 relative">
            <h2 class="font-semibold text-center w-full">Корзина</h2>
            <button @click="cartMiniUi.closeMiniCart" class="text-lg absolute right-2 top-0 hover:text-gray-700 leading-none">
              &times;
            </button>
          </div>

          <div v-if="cart.products.length === 0" class="text-center">Пусто</div>

          <div v-else class="space-y-3">
            <div v-for="product in cart.products" :key="product.id" class="flex items-center gap-2 border-t pt-2">
              <img
                :src="product.main_image ? `/storage/${product.main_image}` : '/images/placeholder.jpg'"
                alt=""
                class="w-12 h-12 object-cover rounded bg-gray-100"
              />
              <div class="flex-1">
                <p class="text-xs">
                  {{ product.description?.title ?? '❌ нет title' }} x {{ cart.items[product.id] }} —
                  {{ (product.discounted_price ?? product.price) * cart.items[product.id] }} mdl
                </p>
              </div>
            </div>

            <hr />

            <div class="flex justify-between font-semibold">
              <span>Итого:</span>
              <span>{{ totalWithDiscount.toFixed(2) }} mdl</span>
            </div>

            <button
              class="w-full border border-gray-400 text-white py-1.5 rounded text-sm font-semibold hover:bg-gray-800"
              @click="goToCart"
            >
              Оформить
            </button>

            <button
              @click="cartMiniUi.closeMiniCart"
              class="w-full border border-gray-400 py-1.5 rounded text-sm font-semibold hover:bg-gray-800"
            >
              Проверить
            </button>
          </div>
        </div>
      </transition>
    </div>
  </div>
</template>
