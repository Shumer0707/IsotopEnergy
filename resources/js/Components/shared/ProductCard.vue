<script setup>
  import FavoriteButton from '../common/FavoriteButton.vue'
  import { useCartStore } from '@/Stores/cart'
  import { computed } from 'vue'

  const cart = useCartStore()

  const props = defineProps({
    product: Object,
    onClick: Function,
    // onAddToCart: Function,
    inFavorites: Boolean,
  })

  const inCart = computed(() => !!cart.items[props.product.id])
</script>

<template>
  <div class="bg-white rounded-2xl shadow p-4 relative flex flex-col h-full justify-between">
    <!-- 🔹 Скидка -->
    <div v-if="product.promotion?.discount_group" class="absolute top-2 left-2 bg-gray-300 text-xs font-semibold px-2 sm:py-1 rounded">
      СКИДКА -{{ product.promotion.discount_group.discount_percent }}%
    </div>

    <!-- 🔹 Изображение -->
    <div class="h-20 lg:h-40 bg-gray-100 rounded flex items-center justify-center mb-2 lg:mb-4 overflow-hidden">
      <img
        :src="product.main_image ? `/storage/${product.main_image}` : '/images/placeholder.webp'"
        alt="product image"
        class="max-h-full max-w-full object-contain cursor-pointer"
        @click="onClick?.(product.id)"
      />
    </div>

    <!-- 🔹 Название и инфо -->
    <div class="mb-2 lg:mb-4">
      <h3 class="text-sm sm:text-base font-semibold leading-tight mb-1">
        {{ product.description?.title ?? 'Без названия' }}
      </h3>
      <p class="text-xs text-gray-500">Артикул: {{ product.id }}</p>
      <p class="text-sm text-gray-600">{{ product.brand.name }}</p>
    </div>

    <!-- 🔹 Цена + кнопки -->
    <div class="flex justify-between items-center mt-auto">
      <div>
        <div v-if="product.promotion?.discount_group" class="text-xs text-my_red line-through">{{ product.price }} mdl</div>
        <div class="text-black font-bold text-sm sm:text-lg">{{ product.discounted_price }} mdl</div>
      </div>

      <div class="flex gap-2 items-center">
        <button
          @click="cart.toggle(product.id)"
          :class="['p-1 sm:p-2 text-white rounded', inCart ? 'bg-my_green' : 'bg-more_op hover:bg-my_green_op']"
        >
          <font-awesome-icon icon="shopping-cart" class="text-lg" />
        </button>
        <FavoriteButton :product-id="product.id" :product="product" :in-favorites="inFavorites" size-class="text-xl" />
      </div>
    </div>
  </div>
</template>
