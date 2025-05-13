<script setup>
defineProps({
  product: Object,
  onClick: Function,
  onAddToCart: Function,
  onToggleFavorite: Function,
  isFavorite: Boolean,
})
</script>

<template>
  <div class="bg-white rounded-2xl shadow p-4 relative flex flex-col h-full justify-between">
    <!-- 🔹 Скидка -->
    <div
      v-if="product.promotion?.discount_group"
      class="absolute top-2 left-2 bg-gray-300 text-xs font-bold px-2 py-1 rounded"
    >
      СКИДКА -{{ product.promotion.discount_group.discount_percent }}%
    </div>

    <!-- 🔹 Изображение -->
    <div class="h-40 bg-gray-100 rounded flex items-center justify-center mb-4 overflow-hidden">
      <img
        :src="product.main_image ? `/storage/${product.main_image}` : '/images/placeholder.jpg'"
        alt="product image"
        class="max-h-full max-w-full object-contain cursor-pointer"
        @click="onClick?.(product.id)"
      />
    </div>

    <!-- 🔹 Название и инфо -->
    <div class="mb-4">
      <h3 class="text-base font-semibold leading-tight mb-1">
        {{ product.description?.title ?? 'Без названия' }}
      </h3>
      <p class="text-xs text-gray-500">Артикул: {{ product.id }}</p>
      <p class="text-sm text-gray-600">{{ product.brand.name }}</p>
    </div>

    <!-- 🔹 Цена + кнопки -->
    <div class="flex justify-between items-center mt-auto">
      <div>
        <div v-if="product.promotion?.discount_group" class="text-xs text-gray-400 line-through">
          {{ product.price }} mdl
        </div>
        <div class="text-pink-600 font-bold text-lg">{{ product.discounted_price }} mdl</div>
      </div>

      <div class="flex gap-2 items-center">
        <button @click="onAddToCart(product.id)" class="p-2 text-white bg-gray-700 hover:bg-gray-800 rounded">🛒</button>
        <button @click="onToggleFavorite(product)" title="Избранное">
          <font-awesome-icon
            :icon="isFavorite ? ['fas', 'heart'] : ['far', 'heart']"
            class="text-xl text-gray-500 hover:text-pink-600"
          />
        </button>
      </div>
    </div>
  </div>
</template>
