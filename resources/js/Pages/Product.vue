<script setup>
  import { ref, computed, onMounted, onBeforeUnmount } from 'vue'
  import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
  import QuantityControl from '@/Components/common/QuantityControl.vue'
  import FavoriteButton from '@/Components/common/FavoriteButton.vue'
  import { useCartStore } from '@/Stores/cart'
  import { Swiper, SwiperSlide } from 'swiper/vue'
  import { Navigation, Mousewheel } from 'swiper'
  import 'swiper/css'
  import 'swiper/css/navigation'
  import { useTranslations } from '@/composables/useTranslations'

  const t = useTranslations()
  const props = defineProps({
    product: Object,
  })

  const cart = useCartStore()
  const isMobile = ref(false)
  const swiper = ref(null)
  const activeImage = ref(
    props.product.main_image
      ? `/storage/${props.product.main_image}`
      : props.product.images.length
      ? `/storage/${props.product.images[0].path}`
      : '/images/placeholder.jpg'
  )

  const setImage = (imgPath) => {
    activeImage.value = `/storage/${imgPath}`
  }

  const attributes = computed(() => {
    return (
      props.product.attribute_values?.map((pivot) => {
        return {
          name: pivot.attribute?.translated_name ?? '—',
          value: pivot.value?.translated_value ?? '—',
        }
      }) ?? []
    )
  })

  const checkMobile = () => {
    isMobile.value = window.matchMedia('(max-width: 768px)').matches
  }

  onMounted(() => {
    checkMobile()
    window.addEventListener('resize', checkMobile)
  })

  onBeforeUnmount(() => {
    window.removeEventListener('resize', checkMobile)
  })
</script>

<template>
  <div class="max-w-7xl mx-auto px-4">
    <div class="flex flex-col md:flex-row gap-8 bg-white rounded-xl shadow p-6">
      <!-- 🔹 Галерея -->
      <div class="flex flex-col sm:flex-row gap-4 md:w-1/2">
        <div :class="['relative', isMobile ? 'w-full h-24' : 'w-20']">
          <!-- Кнопка вверх -->
          <button
            v-if="!isMobile"
            @click="swiper.slidePrev()"
            class="absolute top-0 left-0 right-0 h-6 z-10 text-sm text-gray-400 hover:text-pink-600"
          >
            <font-awesome-icon icon="chevron-up" />
          </button>

          <!-- Swiper -->
          <Swiper
            :modules="[Mousewheel]"
            :direction="isMobile ? 'horizontal' : 'vertical'"
            :slides-per-view="4"
            :space-between="8"
            :mousewheel="!isMobile"
            @swiper="(s) => (swiper = s)"
            :class="['py-6', isMobile ? 'h-24 w-full px-6' : 'max-h-[400px] mt-6 mb-6']"
          >
            <SwiperSlide v-for="(img, index) in product.images" :key="index" @click="setImage(img.path)">
              <div :class="['w-20 h-20 rounded overflow-hidden cursor-pointer shadow-sm transition p-1']">
                <img
                  :src="`/storage/${img.path}`"
                  :class="[
                    'w-full h-full object-contain',
                    activeImage.includes(img.path) ? 'ring-2 ring-pink-500 shadow-md ring-offset-2 ring-offset-white' : 'border',
                  ]"
                />
              </div>
            </SwiperSlide>
          </Swiper>

          <!-- Кнопка вниз -->
          <button
            v-if="!isMobile"
            @click="swiper.slideNext()"
            class="absolute bottom-0 left-0 right-0 h-6 z-10 bg-white text-gray-500 hover:text-pink-600"
          >
            <font-awesome-icon icon="chevron-down" />
          </button>
        </div>

        <!-- Основное изображение -->
        <div class="relative flex-1 flex justify-center items-center bg-gray-50 rounded p-4">
          <!-- 🔻 Скидка -->
          <div
            v-if="product.promotion?.discount_group"
            class="absolute top-2 left-2 bg-gray-200 text-xs font-bold px-3 py-1 rounded z-10"
          >
            {{ t['product_discount'] }} -{{ product.promotion.discount_group.discount_percent }}%
          </div>

          <img :src="activeImage" alt="product image" class="max-w-full max-h-[400px] object-contain" />
        </div>
      </div>

      <!-- 🔸 Информация о товаре -->
      <div class="flex flex-col gap-4 md:w-1/2 justify-center md:justify-start self-center">
        <div class="flex flex-col gap-4">
          <!-- Название и бренд -->
          <div>
            <h1 class="text-2xl font-bold leading-snug">
              {{ product.description?.title ?? 'Без названия' }}
            </h1>

            <div class="text-sm text-gray-500 mt-1">
              {{ t['product_art'] }} {{ product.id }}
              <br />
              {{ t['product_availability'] }}
              <span class="text-my_grin font-semibold">{{ t['product_stock'] }}</span>
            </div>
          </div>

          <!-- Цена и скидка -->
          <div class="space-y-1">
            <div
              v-if="product.promotion?.discount_group"
              class="inline-block bg-my_grin_op text-my_red text-xs font-bold px-2 py-1 rounded"
            >
              {{ t['product_discount'] }} -{{ product.promotion.discount_group.discount_percent }}%
            </div>
            <div v-if="product.promotion?.discount_group" class="text-sm text-my_red line-through">
              {{ product.price }} {{ product.currency }}
            </div>
            <div class="text-2xl font-bold text-black">{{ product.discounted_price }} {{ product.currency }}</div>
          </div>

          <!-- Кнопки действия + QuantityControl -->
          <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4 mt-2">
            <!-- Счётчик -->
            <QuantityControl :product-id="product.id" />
            <!-- Кнопки -->
            <div class="flex gap-4">
              <button
                v-if="!cart.items[product.id]"
                @click="cart.toggle(product.id)"
                class="bg-my_green hover:bg-my_green_op text-white text-sm px-6 py-2 rounded-xl"
              >
                {{ t['product_add'] }}
              </button>
              <FavoriteButton :product-id="product.id" :product="product" size-class="text-2xl" />
            </div>
          </div>
          <!-- Иконки преимуществ -->
          <div class="flex gap-4 text-sm text-gray-600 pt-4 border-t mt-4">
            <div class="flex flex-col items-center gap-1 text-center">
              <span class="text-2xl">🔒</span>
              <span class="text-xs leading-tight">
                {{ t['product_text_1'] }}
                <br />
                {{ t['product_text_1_1'] }}
              </span>
            </div>

            <div class="flex flex-col items-center gap-1 text-center">
              <span class="text-2xl">🚚</span>
              <span class="text-xs leading-tight">
                {{ t['product_text_2'] }}
                <br />
                {{ t['product_text_2_2'] }}
              </span>
            </div>
            <div class="flex flex-col items-center gap-1 text-center">
              <span class="text-2xl">✅</span>
              <span class="text-xs leading-tight">
                {{ t['product_text_3'] }}
                <br />
                {{ t['product_text_3_3'] }}
              </span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="grid md:grid-cols-2 gap-6 mt-10">
      <!-- Характеристики -->
      <div>
        <h2 class="text-lg font-semibold mb-4">{{ t['product_characteristics'] }}</h2>
        <table class="w-full text-sm text-gray-700">
          <tbody>
            <tr v-for="(attribute, index) in attributes" :key="index" class="border-b last:border-0">
              <td class="py-2 font-medium">{{ attribute.name }}</td>
              <td class="py-2 text-right">{{ attribute.value }}</td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Описание -->
      <div>
        <h2 class="text-lg font-semibold mb-4">{{ t['product_description'] }}</h2>
        <p class="text-sm text-gray-800 leading-relaxed whitespace-pre-line">
          {{ product.description?.full_description ?? 'Описание отсутствует.' }}
        </p>
      </div>
    </div>
  </div>
</template>
