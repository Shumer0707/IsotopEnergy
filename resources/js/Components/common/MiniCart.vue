<script setup>
  import { ref, onMounted, watch, nextTick, computed } from 'vue'
  import { useCartMiniUiStore } from '@/Stores/cartMiniUi'
  import { useCartStore } from '@/Stores/cart'
  import { router, usePage } from '@inertiajs/vue3'
  import { useLayoutStore } from '@/Stores/layout'
  import { useKeyboardShortcuts } from '@/composables/useKeyboardShortcuts'
  import OverlayLayer from '@/Components/common/OverlayLayer.vue'
  import { useClickOutside } from '@/composables/useClickOutside'

  const layout = useLayoutStore()
  const cart = useCartStore()
  const cartMiniUi = useCartMiniUiStore()
  const cartButtonRef = ref(null)
  const miniCartRef = ref(null)
  const page = usePage()

  // Автозакрытие при клике вне
  useClickOutside(
    miniCartRef,
    () => {
      cartMiniUi.closeMiniCart()
    },
    cartButtonRef
  )

  useKeyboardShortcuts({
    Escape: () => {
      if (cartMiniUi.isMiniCartOpen) cartMiniUi.closeMiniCart()
    },
  })

  const updateOffset = () => {
    if (cartButtonRef.value) {
      const rect = cartButtonRef.value.getBoundingClientRect()
      layout.setCartButtonRightOffset(window.innerWidth - rect.right)
    }
  }

  onMounted(() => {
    nextTick(() => {
      setTimeout(updateOffset, 0)
    })
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
    router.visit(route('cart', { locale: page.props.locale }))
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
      <button @click="toggleMiniCart" class="gap-1 flex flex-raw items-center sm:p-1 text-3xl rounded-md hover:bg-my_green_op">
        <font-awesome-icon icon="shopping-cart" class="lg:text-3xl text-2xl" />
        <font-awesome-icon icon="chevron-down" class="text-xs" />
      </button>
      <span
        v-if="totalQuantity > 0"
        class="absolute -top-1 -left-1 bg-my_red text-white text-xs font-bold rounded-full w-5 h-5 flex items-center justify-center"
      >
        {{ totalQuantity }}
      </span>
    </div>

    <Teleport to="body">
      <!-- Затемнение -->
      <!-- <div
        class="fixed inset-0 bg-black/30 z-30 transition-opacity duration-300"
        :class="cartMiniUi.isMiniCartOpen ? 'opacity-100' : 'opacity-0 pointer-events-none'"
        @click="cartMiniUi.closeMiniCart"
      /> -->
      <!-- Затемнение -->
      <OverlayLayer v-model:show="cartMiniUi.isMiniCartOpen" />

      <!-- Модалка -->
      <div
        class="fixed z-40 transition-transform duration-300 ease-out"
        :class="cartMiniUi.isMiniCartOpen ? 'translate-y-0 ' : '-translate-y-[120%]'"
        :style="{
          top: `${layout.headerBottom}px`,
          right: `${layout.cartButtonRightOffset}px`,
          width: '300px',
        }"
      >
        <div
          ref="miniCartRef"
          class="bg-my_white rounded-b-md shadow-xl border-t-0 border border-gray-700 text-black text-sm w-full max-h-[70vh] flex flex-col"
        >
          <!-- Заголовок -->
          <div class="flex justify-between items-center p-3 border-b relative">
            <h2 class="font-semibold text-center w-full">Корзина</h2>
            <button @click="cartMiniUi.closeMiniCart" class="text-lg absolute right-2 top-0 hover:text-gray-700 leading-none">
              &times;
            </button>
          </div>

          <!-- Товары -->
          <div class="overflow-y-auto px-3 py-2 space-y-3 flex-1">
            <div v-if="cart.products.length === 0" class="text-center">Пусто</div>
            <div v-else>
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
            </div>
          </div>

          <!-- Итого -->
          <div class="p-3 border-t">
            <div class="flex justify-between font-semibold mb-2">
              <span>Итого:</span>
              <span>{{ totalWithDiscount.toFixed(2) }} mdl</span>
            </div>
            <button
              class="w-full bg-my_green border border-main text-black py-1.5 rounded text-sm font-semibold hover:bg-my_green_op"
              @click="goToCart"
            >
              Перейти в корзину
            </button>
          </div>
        </div>
      </div>
    </Teleport>
  </div>
</template>
