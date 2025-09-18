<script setup>
  import { computed } from 'vue'
  import { useCartStore } from '@/Stores/cart'
  import { useTranslations } from '@/composables/useTranslations'

  const t = useTranslations()

  const props = defineProps({
    // Теперь работаем с variant-id вместо product-id
    variantId: {
      type: [Number, String],
      default: null,
    },
    // Опциональные пропсы для стилизации
    small: {
      type: Boolean,
      default: false,
    },
    cross: {
      type: Boolean,
      default: true,
    },
  })

  const cart = useCartStore()

  // Получаем количество варианта в корзине
  const quantity = computed(() => {
    if (!props.variantId) return 0
    return cart.getVariantQuantity(props.variantId)
  })

  // Проверяем есть ли вариант в корзине
  const inCart = computed(() => {
    if (!props.variantId) return false
    return cart.hasVariant(props.variantId)
  })

  // Увеличить количество
  const increment = () => {
    if (!props.variantId) return
    cart.increment(props.variantId)
  }

  // Уменьшить количество
  const decrement = () => {
    if (!props.variantId) return
    cart.decrement(props.variantId)
  }

  // Убрать из корзины полностью
  const removeFromCart = () => {
    if (!props.variantId) return
    cart.remove(props.variantId)
  }
</script>

<template>
  <!-- Если вариант не выбран - показываем заглушку -->
  <div v-if="!variantId" class="text-sm text-gray-400">{{ t['product_select'] }}</div>

  <!-- Если вариант не в корзине - показываем только количество -->
  <div v-else-if="!inCart" :class="['flex items-center', small ? 'text-sm' : '']">
    <span class="text-gray-600 mr-2">{{ t['product_quantity'] }}</span>
    <span class="bg-gray-100 px-3 py-1 rounded">1</span>
  </div>

  <!-- Если вариант в корзине - показываем полный контроль -->
  <div v-else :class="['flex items-center gap-2', small ? 'text-sm' : '']">
    <!-- Кнопка уменьшения -->
    <button
      @click="decrement"
      :class="[
        'flex items-center justify-center transition-colors',
        small ? 'w-8 h-8 text-sm' : 'w-10 h-10',
        'bg-gray-200 hover:bg-gray-300 rounded-lg',
      ]"
    >
      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
      </svg>
    </button>

    <!-- Отображение количества -->
    <span :class="['px-3 py-2 bg-gray-100 rounded text-center font-medium', small ? 'text-sm min-w-[2rem]' : 'min-w-[3rem]']">
      {{ quantity }}
    </span>

    <!-- Кнопка увеличения -->
    <button
      @click="increment"
      :class="[
        'flex items-center justify-center transition-colors',
        small ? 'w-8 h-8 text-sm' : 'w-10 h-10',
        'bg-gray-200 hover:bg-gray-300 rounded-lg',
      ]"
    >
      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v12m6-6H6" />
      </svg>
    </button>

    <!-- Кнопка полного удаления (если включена) -->
    <button
      v-if="cross"
      @click="removeFromCart"
      :class="[
        'flex items-center justify-center transition-colors ml-2',
        small ? 'w-8 h-8 text-sm' : 'w-10 h-10',
        'text-red-500 hover:text-red-700 hover:bg-red-50 rounded-lg',
      ]"
      title="Убрать из корзины"
    >
      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
      </svg>
    </button>
  </div>
</template>
