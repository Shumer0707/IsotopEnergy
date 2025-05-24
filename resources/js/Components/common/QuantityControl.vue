<script setup>
  import { useCartStore } from '@/Stores/cart'
  import { useCartInputHandlers } from '@/composables/useCartInputHandlers'

  const props = defineProps({
    productId: {
      type: [Number, String],
      required: true,
    },
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
  const { onQtyInput, onQtyBlur } = useCartInputHandlers()
</script>

<template>
  <div v-if="cart.items[productId]" class="flex items-center gap-2 flex-wrap lg:flex-nowrap">
    <!-- − -->
    <button
      :class="[small ? 'w-6 h-6 text-xs' : 'w-8 h-8'] + ' bg-gray-200 rounded flex items-center justify-center'"
      @click="cart.decrement(productId)"
    >
      −
    </button>

    <input
      type="text"
      min="1"
      max="100"
      :value="cart.items[productId]"
      @input="onQtyInput($event, productId)"
      @blur="onQtyBlur($event, productId)"
      :class="[small ? 'w-10 h-6 text-xs' : 'w-14 h-8'] + ' border rounded text-center'"
    />

    <button
      :class="[small ? 'w-6 h-6 text-xs' : 'w-8 h-8'] + ' bg-gray-200 rounded flex items-center justify-center'"
      @click="cart.increment(productId)"
    >
      +
    </button>

    <button
      v-if="cross"
      :class="[small ? 'text-base' : 'text-xl'] + ' text-red-500 leading-none hover:text-red-700'"
      @click="cart.remove(productId)"
      title="Удалить"
    >
      ✖
    </button>
  </div>
</template>
