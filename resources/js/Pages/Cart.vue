<script setup>
  import { useCartStore } from '@/Stores/cart'
  import QuantityControl from '@/Components/common/QuantityControl.vue'
  import OrderModal from '@/Components/common/OrderModal.vue'
  import { onMounted, ref } from 'vue'
  import { router, usePage } from '@inertiajs/vue3'
  import { useTranslations } from '@/composables/useTranslations'

  const page = usePage()
  const openProduct = (productId) => {
    router.visit(route('product.show', { locale: page.props.locale, product: productId }))
  }
  const t = useTranslations()
  const isModalOpen = ref(false)
  const cart = useCartStore()

  // Форматируем атрибуты варианта для отображения
  const formatVariantAttributes = (variant) => {
    if (!variant.attributes || variant.attributes.length === 0) {
      return ''
    }

    return variant.attributes.map((attr) => `${attr.name}: ${attr.value}`).join(', ')
  }

  onMounted(() => {
    cart.init()
  })
</script>

<template>
  <div class="max-w-7xl mx-auto px-4 py-8 grid grid-cols-1 lg:grid-cols-3 gap-8">
    <!-- 🛒 Таблица товаров -->
    <div class="lg:col-span-2">
      <h1 class="text-2xl font-bold mb-6 text-center">{{ t['cart_title'] }}</h1>

      <div v-if="Object.keys(cart.items).length === 0" class="text-gray-600 text-center">{{ t['cart_empty'] }}</div>

      <div v-else>
        <!-- Заголовки -->
        <div class="hidden lg:grid grid-cols-14 font-semibold border-b pb-2 mb-4 text-sm text-gray-700">
          <div class="col-span-6">{{ t['cart_item'] }}</div>
          <div class="col-span-2 text-center">{{ t['cart_quantity'] }}</div>
          <div class="col-span-2 text-right">{{ t['cart_price'] }}</div>
          <div class="col-span-2 text-right">{{ t['cart_all'] }}</div>
          <div class="col-span-2 text-right">{{ t['cart_delete'] }}</div>
        </div>

        <!-- Список вариантов -->
        <div
          v-for="variant in cart.variants"
          :key="variant.id"
          class="flex flex-col lg:grid lg:grid-cols-14 gap-4 border-t py-4 items-center lg:min-h-[88px]"
        >
          <!-- 📦 Фото + Название -->
          <div class="flex gap-4 items-center lg:col-span-6">
            <img
              :src="variant.product.main_image ? `/storage/${variant.product.main_image}` : '/images/placeholder.jpg'"
              :alt="variant.product.title"
              @click="openProduct(variant.product.id)"
              class="w-20 h-20 object-cover rounded shrink-0 cursor-pointer"
            />
            <div class="flex flex-col justify-center">
              <p class="font-medium text-sm sm:text-base">{{ variant.product.title }}</p>

              <!-- ✅ НОВОЕ: Показываем артикул варианта -->
              <p class="text-xs text-gray-400 mt-1">{{ t['cart_article'] }} {{ variant.sku }}</p>

              <!-- ✅ НОВОЕ: Показываем атрибуты варианта -->
              <p v-if="formatVariantAttributes(variant)" class="text-xs text-gray-500 mt-1">
                {{ formatVariantAttributes(variant) }}
              </p>

              <!-- ✅ НОВОЕ: Бренд если есть -->
              <p v-if="variant.product.brand_name" class="text-xs text-gray-500 mt-1">
                {{ variant.product.brand_name }}
              </p>

              <!-- 🔹 Инфо для мобилки -->
              <div class="mt-2 space-y-1 text-sm text-gray-700 lg:hidden">
                <div>
                  {{ t['cart_price'] }}
                  <p v-if="variant.price !== variant.discounted_price" class="line-through text-gray-400">
                    {{ variant.price }} {{ variant.product.currency }}
                  </p>
                  <p class="ml-1 font-semibold">{{ variant.discounted_price || variant.price }} {{ variant.product.currency }}</p>
                </div>
                <p>{{ t['cart_quantity'] }}: {{ cart.getVariantQuantity(variant.id) }}</p>
                <div class="flex justify-center items-center">
                  <QuantityControl :variant-id="variant.id" small />
                </div>
                <p class="font-semibold">
                  {{ t['cart_all'] }}
                  {{ ((variant.discounted_price || variant.price) * cart.getVariantQuantity(variant.id)).toFixed(2) }}
                  {{ variant.product.currency }}
                </p>
              </div>
            </div>
          </div>

          <!-- ➖➕ Кол-во -->
          <div class="hidden lg:flex lg:col-span-2 justify-center items-center">
            <QuantityControl :variant-id="variant.id" :cross="false" />
          </div>

          <!-- 💰 Цена -->
          <div class="hidden lg:flex items-center justify-end lg:col-span-2">
            <div class="text-sm text-right leading-snug">
              <p v-if="variant.price !== variant.discounted_price" class="line-through text-gray-400">
                {{ variant.price }} {{ variant.product.currency }}
              </p>
              <p class="font-semibold">{{ variant.discounted_price || variant.price }} {{ variant.product.currency }}</p>
            </div>
          </div>

          <!-- 🧮 Всего -->
          <div class="hidden lg:flex items-center justify-end lg:col-span-2">
            <p class="text-sm font-semibold">
              {{ ((variant.discounted_price || variant.price) * cart.getVariantQuantity(variant.id)).toFixed(2) }}
              {{ variant.product.currency }}
            </p>
          </div>

          <!-- ❌ Удалить -->
          <div class="hidden lg:flex items-center justify-end lg:col-span-2">
            <button class="text-xl text-red-500 hover:text-red-700" @click="cart.remove(variant.id)" title="Удалить товар">
              ✖
            </button>
          </div>
        </div>

        <!-- Очистить корзину -->
        <button
          @click="cart.clear"
          class="flex justify-end w-full mt-2 text-sm text-red-600 hover:underline"
          v-if="cart.variants.length"
        >
          {{ t['cart_delete_all'] }}
        </button>
      </div>
    </div>

    <!-- 📦 Боковая колонка с итогами -->
    <div class="bg-gray-100 rounded-xl p-6 h-fit lg:col-span-1 w-full lg:w-auto mt-6 lg:mt-0">
      <h2 class="font-semibold text-lg mb-4 border-b pb-2">{{ t['cart_details'] }}</h2>

      <div class="flex justify-between text-sm mb-2">
        <span>{{ t['cart_all_items'] }}</span>
        <span>{{ cart.totalQuantity }}</span>
      </div>

      <div class="flex justify-between text-sm mb-2">
        <span>{{ t['cart_not_discount'] }}</span>
        <span>{{ cart.totalWithoutDiscount.toFixed(2) }} MDL</span>
      </div>

      <div class="flex justify-between text-sm mb-2">
        <span>{{ t['cart_discount'] }}</span>
        <span class="text-my_red">-{{ cart.totalDiscount.toFixed(2) }} MDL</span>
      </div>

      <div class="flex justify-between font-bold text-lg mb-4">
        <span>{{ t['cart_all_price'] }}</span>
        <span>{{ cart.totalWithDiscount.toFixed(2) }} MDL</span>
      </div>

      <button @click="isModalOpen = true" class="w-full bg-my_green text-white py-2 rounded hover:bg-my_green_op">
        {{ t['cart_send'] }}
      </button>
    </div>
  </div>
  <OrderModal v-if="isModalOpen" @close="isModalOpen = false" />
</template>
