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

  watch(() => layout.headerBottom, updateOffset)

  // ✅ ИСПРАВЛЯЕМ: Теперь используем геттеры из cart store
  const totalWithDiscount = computed(() => cart.totalWithDiscount)
  const totalQuantity = computed(() => cart.totalQuantity)

  // ✅ НОВАЯ ФУНКЦИЯ: Форматирование атрибутов для краткого отображения
  const formatVariantAttributesShort = (variant) => {
    if (!variant.attributes || variant.attributes.length === 0) {
      return ''
    }

    // Берем только первые 2 атрибута для экономии места
    const shortAttributes = variant.attributes
      .slice(0, 2)
      .map((attr) => `${attr.value}`)
      .join(', ')

    const moreCount = variant.attributes.length - 2
    return shortAttributes + (moreCount > 0 ? `... +${moreCount}` : '')
  }

  const goToCart = () => {
    cartMiniUi.closeMiniCart()
    router.visit(route('cart', { locale: page.props.locale }))
  }

  const toggleMiniCart = () => {
    cartMiniUi.toggleMiniCart()
  }
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
      <OverlayLayer v-model:show="cartMiniUi.isMiniCartOpen" />

      <!-- Модалка -->
      <div
        class="fixed z-40 transition-transform duration-300 ease-out"
        :class="cartMiniUi.isMiniCartOpen ? 'translate-y-0 ' : '-translate-y-[120%]'"
        :style="{
          top: `${layout.headerBottom}px`,
          right: `${layout.cartButtonRightOffset}px`,
          width: '320px',
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

          <!-- ✅ ОБНОВЛЕННЫЙ БЛОК: Варианты товаров -->
          <div class="overflow-y-auto px-3 py-2 space-y-3 flex-1">
            <div v-if="cart.variants.length === 0" class="text-center text-gray-500 py-4">Корзина пуста</div>
            <div v-else>
              <div
                v-for="variant in cart.variants"
                :key="variant.id"
                class="flex items-start gap-2 border-t pt-2 first:border-t-0 first:pt-0"
              >
                <!-- Изображение товара -->
                <img
                  :src="variant.product.main_image ? `/storage/${variant.product.main_image}` : '/images/placeholder.jpg'"
                  :alt="variant.product.title"
                  class="w-12 h-12 object-cover rounded bg-gray-100 shrink-0"
                />

                <!-- Информация о варианте -->
                <div class="flex-1 min-w-0">
                  <!-- Название товара -->
                  <p class="text-xs font-medium leading-tight mb-1 truncate" :title="variant.product.title">
                    {{ variant.product.title || 'Без названия' }}
                  </p>

                  <!-- Атрибуты варианта (сокращенно) -->
                  <p v-if="formatVariantAttributesShort(variant)" class="text-xs text-gray-500 mb-1 truncate">
                    {{ formatVariantAttributesShort(variant) }}
                  </p>

                  <!-- Количество и цена -->
                  <div class="flex justify-between items-center text-xs">
                    <span class="text-gray-600">{{ cart.getVariantQuantity(variant.id) }} шт</span>
                    <div class="text-right">
                      <!-- Цена со скидкой если есть -->
                      <div
                        v-if="variant.discounted_price && variant.discounted_price < variant.price"
                        class="line-through text-gray-400 text-xs"
                      >
                        {{ variant.price }} {{ variant.product.currency }}
                      </div>
                      <div class="font-medium">
                        {{ ((variant.discounted_price || variant.price) * cart.getVariantQuantity(variant.id)).toFixed(2) }}
                        {{ variant.product.currency }}
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Итого -->
          <div class="p-3 border-t bg-gray-50">
            <div class="flex justify-between font-semibold mb-3 text-sm">
              <span>Итого ({{ totalQuantity }} шт):</span>
              <span>{{ totalWithDiscount.toFixed(2) }} MDL</span>
            </div>
            <button
              :disabled="cart.variants.length === 0"
              class="w-full bg-my_green border border-main text-black py-2 rounded text-sm font-semibold hover:bg-my_green_op disabled:bg-gray-300 disabled:cursor-not-allowed transition-colors"
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
