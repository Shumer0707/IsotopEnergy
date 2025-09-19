<script setup>
  import { ref, computed, onMounted, onBeforeUnmount, watch, nextTick } from 'vue'
  import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
  import QuantityControl from '@/Components/common/QuantityControl.vue'
  import FavoriteButton from '@/Components/common/FavoriteButton.vue'
  import ImageModal from '@/Components/common/ImageModal.vue'
  import { useCartStore } from '@/Stores/cart'
  import { Swiper, SwiperSlide } from 'swiper/vue'
  import { Navigation, Mousewheel } from 'swiper'
  import 'swiper/css'
  import 'swiper/css/navigation'
  import ProductHeadSeo from '@/Components/seo/pages/ProductHeadSeo.vue'
  import { useTranslations } from '@/composables/useTranslations'

  const t = useTranslations()
  const props = defineProps({
    product: Object,
  })

  const cart = useCartStore()
  const isMobile = ref(false)
  const swiper = ref(null)

  // Выбранный вариант товара
  const selectedVariant = ref(null)

  // Модальное окно для изображений
  const isModalOpen = ref(false)
  const currentModalImageIndex = ref(0)

  // Инициализация выбранного варианта
  const initSelectedVariant = () => {
    if (!props.product) return

    let defaultVariant = null

    if (props.product.default_variant) {
      defaultVariant = props.product.default_variant
    } else if (props.product.cheapest_variant) {
      defaultVariant = props.product.cheapest_variant
    } else if (props.product.variants && props.product.variants.length > 0) {
      defaultVariant = props.product.variants.find((v) => v.is_default) || props.product.variants[0]
    }

    if (defaultVariant) {
      selectedVariant.value = defaultVariant
    }
  }

  // Наблюдатель за изменением product
  watch(
    () => props.product,
    (newProduct) => {
      if (newProduct) {
        nextTick(() => {
          initSelectedVariant()
        })
      }
    },
    { immediate: true, deep: true }
  )

  const activeImage = ref(
    props.product?.main_image
      ? `/storage/${props.product.main_image}`
      : props.product?.images?.length
      ? `/storage/${props.product.images[0].path}`
      : '/images/placeholder.jpg'
  )

  // Простая смена картинки (только для миниатюр)
  const setActiveImage = (imgPath) => {
    activeImage.value = `/storage/${imgPath}`
  }

  // Открытие модалки с конкретным индексом
  const openModal = (imageIndex) => {
    console.log('openModal вызван с индексом:', imageIndex)
    currentModalImageIndex.value = imageIndex
    isModalOpen.value = true
  }

  // Открытие модалки для текущей активной картинки
  const openModalForCurrentImage = () => {
    const currentImagePath = activeImage.value.replace('/storage/', '')
    const currentIndex = props.product.images?.findIndex((img) => img.path === currentImagePath) || 0
    console.log('Открываем модалку для главной картинки, индекс:', currentIndex)
    openModal(currentIndex)
  }

  const closeModal = () => {
    isModalOpen.value = false
  }

  const onModalImageChange = (index) => {
    if (props.product.images && props.product.images[index]) {
      // Обновляем активное изображение без открытия модалки
      activeImage.value = `/storage/${props.product.images[index].path}`
    }
  }

  // Группировка вариантов по атрибутам
  const variantOptions = computed(() => {
    if (!props.product?.variants || props.product.variants.length === 0) {
      return {}
    }

    const options = {}

    props.product.variants.forEach((variant) => {
      if (!variant.variant_attributes || variant.variant_attributes.length === 0) {
        return
      }

      variant.variant_attributes.forEach((va) => {
        const attributeId = va.attribute_id
        const attributeName = va.attribute?.translated_name || `Атрибут ${attributeId}`
        const valueId = va.attribute_value_id
        const valueName = va.attribute_value?.translated_value || `Значение ${valueId}`

        if (!options[attributeId]) {
          options[attributeId] = {
            name: attributeName,
            values: [],
          }
        }

        const existingValue = options[attributeId].values.find((v) => v.id === valueId)

        if (!existingValue) {
          options[attributeId].values.push({
            id: valueId,
            label: valueName,
            variants: [variant],
          })
        } else {
          if (!existingValue.variants.find((v) => v.id === variant.id)) {
            existingValue.variants.push(variant)
          }
        }
      })
    })

    return options
  })

  // Выбор варианта по атрибуту
  const selectVariantByAttribute = (attributeId, valueId) => {
    if (!selectedVariant.value) {
      const matchingVariant = props.product.variants.find((variant) =>
        variant.variant_attributes?.some((va) => va.attribute_id === attributeId && va.attribute_value_id === valueId)
      )

      if (matchingVariant) {
        selectedVariant.value = matchingVariant
      }
      return
    }

    const currentAttributes = {}
    selectedVariant.value.variant_attributes?.forEach((va) => {
      currentAttributes[va.attribute_id] = va.attribute_value_id
    })

    currentAttributes[attributeId] = valueId

    let bestMatch = props.product.variants.find((variant) => {
      const variantAttrs = {}
      variant.variant_attributes?.forEach((va) => {
        variantAttrs[va.attribute_id] = va.attribute_value_id
      })

      return Object.keys(currentAttributes).every((attrId) => variantAttrs[attrId] === currentAttributes[attrId])
    })

    if (!bestMatch) {
      bestMatch = props.product.variants.find((variant) =>
        variant.variant_attributes?.some((va) => va.attribute_id === attributeId && va.attribute_value_id === valueId)
      )
    }

    if (bestMatch) {
      selectedVariant.value = bestMatch
    }
  }

  // Проверка выбранного значения
  const isAttributeValueSelected = (attributeId, valueId) => {
    if (!selectedVariant.value) return false

    return selectedVariant.value.variant_attributes?.some(
      (va) => va.attribute_id === attributeId && va.attribute_value_id === valueId
    )
  }

  // Цена выбранного варианта
  const currentPrice = computed(() => {
    if (!selectedVariant.value) return 0
    return parseFloat(selectedVariant.value.price)
  })

  // Цена со скидкой
  const discountedPrice = computed(() => {
    if (props.product?.promotion?.discount_group?.discount_percent) {
      const discount = props.product.promotion.discount_group.discount_percent
      return currentPrice.value * (1 - discount / 100)
    }
    return currentPrice.value
  })

  // Формирование красивой строки атрибутов выбранного варианта
  const selectedVariantDisplayText = computed(() => {
    if (!selectedVariant.value?.variant_attributes || selectedVariant.value.variant_attributes.length === 0) {
      return ''
    }

    const attributeStrings = selectedVariant.value.variant_attributes.map((va) => {
      const attrName = va.attribute?.translated_name || 'Атрибут'
      const valueName = va.attribute_value?.translated_value || 'Значение'
      return `${attrName}: ${valueName}`
    })

    return attributeStrings.join(', ')
  })

  // Атрибуты выбранного варианта для отображения в характеристиках
  const selectedVariantAttributes = computed(() => {
    if (!selectedVariant.value?.variant_attributes) return []

    return selectedVariant.value.variant_attributes.map((va) => ({
      name: va.attribute?.translated_name || '—',
      value: va.attribute_value?.translated_value || '—',
    }))
  })

  // Остальная логика для слайдера
  const slidesPerView = computed(() => {
    const imageCount = props.product?.images?.length || 0
    return Math.min(imageCount, 4)
  })

  const showNavigation = computed(() => {
    const imageCount = props.product?.images?.length || 0
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
      <!-- Галерея -->
      <div class="flex flex-col sm:flex-row gap-4 md:w-1/2">
        <div v-if="product?.images && product.images.length > 0" :class="['relative', isMobile ? 'w-full h-24' : 'w-20']">
          <button
            v-if="!isMobile && showNavigation"
            @click="swiper?.slidePrev()"
            class="absolute top-0 left-0 right-0 h-6 z-10 text-sm text-gray-400 hover:text-pink-600"
          >
            <font-awesome-icon icon="chevron-up" />
          </button>

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
            <!-- ИСПРАВЛЕНО: только один обработчик на SwiperSlide -->
            <SwiperSlide v-for="(img, index) in product.images" :key="index">
              <div
                :class="['w-20 h-20 rounded overflow-hidden cursor-pointer shadow-sm transition p-1']"
                @click="setActiveImage(img.path)"
                @dblclick="openModal(index)"
              >
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

          <button
            v-if="!isMobile && showNavigation"
            @click="swiper?.slideNext()"
            class="absolute bottom-0 left-0 right-0 h-6 z-10 bg-white text-gray-500 hover:text-pink-600"
          >
            <font-awesome-icon icon="chevron-down" />
          </button>
        </div>

        <div v-else :class="['relative', isMobile ? 'w-full h-24' : 'w-20']">
          <div class="w-20 h-20 rounded overflow-hidden shadow-sm p-1 border">
            <img src="/images/placeholder.jpg" alt="No image" class="w-full h-full object-contain" />
          </div>
        </div>

        <div class="relative flex-1 flex justify-center items-center bg-gray-50 rounded p-4">
          <div
            v-if="product?.promotion?.discount_group"
            class="absolute top-2 left-2 bg-gray-200 text-xs font-bold px-3 py-1 rounded z-10"
          >
            {{ t['product_discount'] }} -{{ product.promotion.discount_group.discount_percent }}%
          </div>

          <!-- ИСПРАВЛЕНО: используем новый метод для главной картинки -->
          <img
            :src="activeImage"
            :alt="product?.description?.title || 'product image'"
            class="w-full h-full object-cover rounded cursor-pointer hover:opacity-90 transition-opacity"
            @click="openModalForCurrentImage"
          />

          <!-- Подсказка для пользователя -->
          <div class="absolute bottom-2 right-2 bg-black bg-opacity-50 text-white text-xs px-2 py-1 rounded opacity-75">
            {{ t['product_img_click'] }}
          </div>
        </div>
      </div>

      <!-- Информация о товаре -->
      <div class="flex flex-col gap-4 md:w-1/2 justify-center md:justify-start self-center">
        <div class="flex flex-col gap-4">
          <!-- Название и бренд -->
          <div>
            <h1 class="text-2xl font-bold leading-snug">
              {{ product?.description?.title ?? 'Без названия' }}
            </h1>

            <div class="text-sm text-gray-500 mt-1">
              {{ t['product_art'] }} {{ selectedVariant?.sku || product?.base_sku || product?.id }}
            </div>
          </div>

          <!-- БЛОК ВЫБОРА ВАРИАНТОВ -->
          <div v-if="Object.keys(variantOptions).length > 0" class="space-y-4 border rounded-lg p-4 bg-gray-50">
            <h3 class="text-sm font-semibold text-gray-700">{{ t['product_choose'] }}</h3>

            <div v-for="(option, attributeId) in variantOptions" :key="attributeId" class="space-y-2">
              <label class="text-sm text-gray-600 font-medium">{{ option.name }}:</label>

              <div class="flex flex-wrap gap-2">
                <button
                  v-for="value in option.values"
                  :key="value.id"
                  @click="selectVariantByAttribute(Number(attributeId), value.id)"
                  :class="[
                    'px-3 py-2 text-sm border rounded-lg transition-colors',
                    isAttributeValueSelected(Number(attributeId), value.id)
                      ? 'bg-my_green text-white border-my_green shadow-md'
                      : 'bg-white text-gray-700 border-gray-300 hover:border-my_green hover:bg-blue-50',
                  ]"
                >
                  {{ value.label }}
                </button>
              </div>
            </div>

            <!-- Информация о выбранном варианте -->
            <div v-if="selectedVariant && selectedVariantDisplayText" class="text-sm text-gray-600 pt-2 border-t border-gray-200">
              <span class="font-medium">{{ t['product_selected'] }}</span>
              {{ selectedVariantDisplayText }} ({{ selectedVariant.price }} {{ product?.currency || 'MDL' }})
            </div>
          </div>

          <!-- ЦЕНА из выбранного варианта -->
          <div class="space-y-1">
            <div
              v-if="product?.promotion?.discount_group"
              class="inline-block bg-my_grin_op text-my_red text-xs font-bold px-2 py-1 rounded"
            >
              {{ t['product_discount'] }} -{{ product.promotion.discount_group.discount_percent }}%
            </div>
            <div v-if="product?.promotion?.discount_group" class="text-sm text-my_red line-through">
              {{ currentPrice.toFixed(2) }} {{ product?.currency || 'mdl' }}
            </div>
            <div class="text-2xl font-bold text-black">
              {{ discountedPrice.toFixed(2) }} {{ product?.currency || 'mdl' }} {{ product?.measurement || '' }}
            </div>
          </div>

          <!-- КНОПКИ с проверкой варианта -->
          <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4 mt-2">
            <QuantityControl :variant-id="selectedVariant?.id" />

            <div class="flex gap-4">
              <button
                v-if="selectedVariant && !cart.hasVariant(selectedVariant.id)"
                @click="cart.add(selectedVariant.id)"
                class="bg-my_green hover:bg-my_green_op text-white text-sm px-6 py-2 rounded-xl"
              >
                {{ t['product_add'] }}
              </button>

              <button
                v-else-if="selectedVariant && cart.hasVariant(selectedVariant.id)"
                @click="cart.remove(selectedVariant.id)"
                class="bg-red-500 hover:bg-red-600 text-white text-sm px-6 py-2 rounded-xl"
              >
                {{ t['product_remove'] }}
              </button>

              <button v-else disabled class="bg-gray-300 text-gray-500 text-sm px-6 py-2 rounded-xl cursor-not-allowed">
                {{ t['product_select'] }}
              </button>

              <FavoriteButton :product-id="product?.id" :product="product" size-class="text-2xl" />
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
      <!-- ХАРАКТЕРИСТИКИ выбранного варианта -->
      <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition-shadow duration-200">
        <div class="flex items-center gap-3 mb-6">
          <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
            <span class="text-blue-600 text-sm">📋</span>
          </div>
          <h2 class="text-xl font-semibold text-gray-800">{{ t['product_characteristics'] }}</h2>
        </div>

        <div v-if="selectedVariantAttributes.length > 0" class="space-y-3">
          <div
            v-for="(attribute, index) in selectedVariantAttributes"
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
          <p class="text-gray-500 text-sm">
            {{ selectedVariant ? 'Характеристики не указаны' : 'Выберите вариант товара' }}
          </p>
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

        <div v-if="product?.description?.short_description" class="prose prose-sm max-w-none">
          <p class="text-gray-700 leading-relaxed whitespace-pre-line text-sm">
            {{ product.description.short_description }}
          </p>
        </div>

        <div v-else class="text-center py-8">
          <div class="text-gray-400 mb-2">📄</div>
          <p class="text-gray-500 text-sm">{{ t['product_available'] }}</p>
        </div>
      </div>
    </div>
  </div>

  <!-- Модальное окно для изображений -->
  <ImageModal
    :is-open="isModalOpen"
    :images="product?.images || []"
    :initial-image-index="currentModalImageIndex"
    :product-title="product?.description?.title || 'Товар'"
    @close="closeModal"
    @image-change="onModalImageChange"
  />
</template>
