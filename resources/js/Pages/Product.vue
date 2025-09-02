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
  import ProductHeadSeo from '@/Components/seo/pages/ProductHeadSeo.vue'

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

  // Вычисляем количество слайдов для показа
  const slidesPerView = computed(() => {
    const imageCount = props.product.images?.length || 0
    return Math.min(imageCount, 4) // Максимум 4, но не больше чем есть изображений
  })

  // Показывать ли навигацию (только если изображений больше чем помещается)
  const showNavigation = computed(() => {
    const imageCount = props.product.images?.length || 0
    return imageCount > (isMobile.value ? 4 : 4)
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
  <ProductHeadSeo :product="product" />
  <div class="max-w-7xl mx-auto px-4">
    <div class="flex flex-col md:flex-row gap-8 bg-white rounded-xl shadow p-6">
      <!-- 🔹 Галерея -->
      <div class="flex flex-col sm:flex-row gap-4 md:w-1/2">
        <div v-if="product.images && product.images.length > 0" :class="['relative', isMobile ? 'w-full h-24' : 'w-20']">
          <!-- Кнопка вверх (показываем только если есть навигация) -->
          <button
            v-if="!isMobile && showNavigation"
            @click="swiper?.slidePrev()"
            class="absolute top-0 left-0 right-0 h-6 z-10 text-sm text-gray-400 hover:text-pink-600"
          >
            <font-awesome-icon icon="chevron-up" />
          </button>

          <!-- Swiper -->
          <Swiper
            :modules="[Mousewheel]"
            :direction="isMobile ? 'horizontal' : 'vertical'"
            :slides-per-view="slidesPerView"
            :space-between="8"
            :mousewheel="!isMobile && showNavigation"
            :allow-touch-move="showNavigation"
            @swiper="(s) => (swiper = s)"
            :class="[
              'py-6',
              isMobile ? 'h-24 w-full px-6' : 'max-h-[400px] mt-6 mb-6',
              !showNavigation ? 'swiper-no-swiping' : '',
            ]"
          >
            <SwiperSlide v-for="(img, index) in product.images" :key="index" @click="setImage(img.path)">
              <div :class="['w-20 h-20 rounded overflow-hidden cursor-pointer shadow-sm transition p-1']">
                <img
                  :src="`/storage/${img.path}`"
                  :alt="`${product.description?.title} — foto ${index + 1}`"
                  :class="[
                    'w-full h-full object-contain',
                    activeImage.includes(img.path) ? 'ring-2 ring-pink-500 shadow-md ring-offset-2 ring-offset-white' : 'border',
                  ]"
                />
              </div>
            </SwiperSlide>
          </Swiper>

          <!-- Кнопка вниз (показываем только если есть навигация) -->
          <button
            v-if="!isMobile && showNavigation"
            @click="swiper?.slideNext()"
            class="absolute bottom-0 left-0 right-0 h-6 z-10 bg-white text-gray-500 hover:text-pink-600"
          >
            <font-awesome-icon icon="chevron-down" />
          </button>
        </div>

        <!-- Fallback если нет изображений -->
        <div v-else :class="['relative', isMobile ? 'w-full h-24' : 'w-20']">
          <div class="w-20 h-20 rounded overflow-hidden shadow-sm p-1 border">
            <img src="/images/placeholder.jpg" alt="No image" class="w-full h-full object-contain" />
          </div>
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

          <img
            :src="activeImage"
            :alt="product.description?.title || 'product image'"
            class="max-w-full max-h-[400px] object-contain"
          />
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
      <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition-shadow duration-200">
        <div class="flex items-center gap-3 mb-6">
          <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
            <span class="text-blue-600 text-sm">📋</span>
          </div>
          <h2 class="text-xl font-semibold text-gray-800">{{ t['product_characteristics'] }}</h2>
        </div>

        <div v-if="attributes.length > 0" class="space-y-3">
          <div
            v-for="(attribute, index) in attributes"
            :key="index"
            class="flex justify-between items-center py-3 px-4 rounded-lg hover:bg-gray-50 transition-colors duration-150"
            :class="index % 2 === 0 ? 'bg-gray-25' : 'bg-white'"
          >
            <span class="font-medium text-gray-700 text-sm">{{ attribute.name }}</span>
            <span class="text-gray-600 text-sm font-semibold ml-4 text-right">{{ attribute.value }}</span>
          </div>
        </div>

        <div v-else class="text-center py-8">
          <div class="text-gray-400 mb-2">📝</div>
          <p class="text-gray-500 text-sm">Характеристики не указаны</p>
        </div>
      </div>

      <!-- Описание -->
      <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition-shadow duration-200">
        <div class="flex items-center gap-3 mb-6">
          <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
            <span class="text-green-600 text-sm">📖</span>
          </div>
          <h2 class="text-xl font-semibold text-gray-800">{{ t['product_description'] }}</h2>
        </div>

        <div v-if="product.description?.short_description" class="prose prose-sm max-w-none">
          <p class="text-gray-700 leading-relaxed whitespace-pre-line text-sm">
            {{ product.description.short_description }}
          </p>
        </div>

        <div v-else class="text-center py-8">
          <div class="text-gray-400 mb-2">📄</div>
          <p class="text-gray-500 text-sm">Описание отсутствует</p>
        </div>
      </div>
    </div>
  </div>
</template>
