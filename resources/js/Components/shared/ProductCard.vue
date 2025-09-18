<script setup>
  import FavoriteButton from '../common/FavoriteButton.vue'
  import { useCartStore } from '@/Stores/cart'
  import { computed } from 'vue'
  import { useTranslations } from '@/composables/useTranslations'

  const t = useTranslations()
  const cart = useCartStore()

  const props = defineProps({
    product: Object,
    onClick: Function,
    inFavorites: Boolean,
  })

  // ✅ Получаем цену из вариантов
  const displayPrice = computed(() => {
    if (props.product.default_variant) {
      return parseFloat(props.product.default_variant.price)
    }
    if (props.product.cheapest_variant) {
      return parseFloat(props.product.cheapest_variant.price)
    }
    if (props.product.variants && props.product.variants.length > 0) {
      return parseFloat(props.product.variants[0].price)
    }
    return 0
  })

  // ✅ Вычисляем цену со скидкой
  const discountedPrice = computed(() => {
    if (props.product.promotion?.discount_group?.discount_percent) {
      const discount = props.product.promotion.discount_group.discount_percent
      return displayPrice.value * (1 - discount / 100)
    }
    return displayPrice.value
  })

  // ✅ ИСПРАВЛЕННЫЙ priceRange
  const priceRange = computed(() => {
    if (!props.product.variants || props.product.variants.length <= 1) {
      return null
    }

    const originalPrices = props.product.variants.map((v) => parseFloat(v.price))
    const minOriginal = Math.min(...originalPrices)
    const maxOriginal = Math.max(...originalPrices)

    if ((maxOriginal - minOriginal) / minOriginal > 0.1) {
      const discount = props.product.promotion?.discount_group?.discount_percent || 0
      const discountMultiplier = discount > 0 ? 1 - discount / 100 : 1

      return {
        originalMin: minOriginal,
        originalMax: maxOriginal,
        discountedMin: minOriginal * discountMultiplier,
        discountedMax: maxOriginal * discountMultiplier,
        hasDiscount: discount > 0,
      }
    }
    return null
  })

  const displaySku = computed(() => {
    if (props.product.default_variant?.sku) {
      return props.product.default_variant.sku
    }
    if (props.product.cheapest_variant?.sku) {
      return props.product.cheapest_variant.sku
    }
    if (props.product.variants?.[0]?.sku) {
      return props.product.variants[0].sku
    }
    return props.product.base_sku || props.product.id
  })

  const displayVariantInfo = computed(() => {
    let variant = null

    if (props.product.default_variant) {
      variant = props.product.default_variant
    } else if (props.product.cheapest_variant) {
      variant = props.product.cheapest_variant
    } else if (props.product.variants?.[0]) {
      variant = props.product.variants[0]
    }

    if (!variant) {
      return `${t['product_art']} ${displaySku.value}`
    }

    const hasAttributes =
      variant.variant_attributes &&
      variant.variant_attributes.length > 0 &&
      variant.variant_attributes.some((va) => va.attribute?.translated_name && va.attribute_value?.translated_value)

    if (!hasAttributes) {
      return `${t['product_art']} ${displaySku.value}`
    }

    const attributeStrings = variant.variant_attributes
      .filter((va) => va.attribute?.translated_name && va.attribute_value?.translated_value)
      .map((va) => {
        const attrName = va.attribute.translated_name
        const valueName = va.attribute_value.translated_value
        return `${attrName}: ${valueName}`
      })

    if (attributeStrings.length === 0) {
      return `${t['product_art']} ${displaySku.value}`
    }

    const attributesText = attributeStrings.join(', ')
    const price = parseFloat(variant.price).toFixed(2)
    const currency = props.product.currency || 'MDL'

    return `${attributesText} (${price} ${currency})`
  })

  const goToProduct = () => {
    if (props.onClick) {
      props.onClick(props.product.id)
    }
  }
</script>

<template>
  <div
    class="bg-white rounded-2xl shadow p-4 relative flex flex-col h-full cursor-pointer hover:shadow-lg transition-shadow duration-200"
    @click="goToProduct"
  >
    <!-- Скидка -->
    <div
      v-if="product.promotion?.discount_group"
      class="absolute top-2 left-2 bg-gray-300 text-xs font-semibold px-2 py-1 rounded z-10"
    >
      {{ t['product_discount'] }} -{{ product.promotion.discount_group.discount_percent }}%
    </div>

    <!-- Изображение -->
    <div class="h-20 lg:h-40 bg-gray-100 rounded flex items-center justify-center mb-3 overflow-hidden">
      <img
        :src="product.main_image ? `/storage/${product.main_image}` : '/images/placeholder.webp'"
        :alt="`${product.description?.title || 'Товар'}`"
        class="max-h-full max-w-full object-contain"
      />
    </div>

    <!-- Контентная часть -->
    <div class="flex flex-col flex-grow">
      <!-- Название и инфо -->
      <div class="mb-3 flex-grow">
        <h3 class="text-sm sm:text-base font-semibold leading-tight mb-2 line-clamp-2">
          {{ product.description?.title ?? 'Без названия' }}
        </h3>
        <p class="text-xs text-gray-500 mb-1">{{ displayVariantInfo }}</p>
        <p v-if="product.brand?.name" class="text-xs text-gray-600">{{ product.brand.name }}</p>
      </div>

      <!-- Цена и кнопки -->
      <div class="mt-auto">
        <!-- ИСПРАВЛЕННАЯ СЕКЦИЯ ЦЕН -->
        <div class="mb-3">
          <!-- Если есть диапазон цен -->
          <div v-if="priceRange">
            <!-- Зачеркнутые старые цены (если есть скидка) -->
            <div v-if="priceRange.hasDiscount" class="text-xs text-my_red line-through">
              от {{ priceRange.originalMin.toFixed(2) }} до {{ priceRange.originalMax.toFixed(2) }}
              {{ product.currency || 'mdl' }}
            </div>

            <!-- Цены со скидкой или без -->
            <div class="text-black font-bold text-sm sm:text-lg">
              от {{ priceRange.discountedMin.toFixed(2) }} до {{ priceRange.discountedMax.toFixed(2) }}
              {{ product.currency || 'mdl' }}
            </div>
          </div>

          <!-- Если нет диапазона - одна цена -->
          <template v-else>
            <!-- Зачеркнутая старая цена -->
            <div v-if="product.promotion?.discount_group" class="text-xs text-my_red line-through">
              {{ displayPrice.toFixed(2) }} {{ product.currency || 'mdl' }}
            </div>

            <!-- Итоговая цена -->
            <div class="text-black font-bold text-sm sm:text-lg">
              {{ discountedPrice.toFixed(2) }} {{ product.currency || 'mdl' }}
            </div>
          </template>

          <!-- Количество вариантов -->
          <div v-if="product.variants && product.variants.length > 1" class="text-xs text-gray-500 mt-1">
            {{ product.variants.length }} {{ t['product_variants'] }}
          </div>
        </div>

        <!-- Кнопки -->
        <div class="flex justify-between items-center">
          <button
            class="flex-1 bg-my_green hover:bg-my_green_op text-white text-sm py-2 px-4 rounded-lg transition-colors mr-2"
            @click.stop="goToProduct"
          >
            {{ t['product_more'] }}
          </button>

          <div @click.stop="">
            <FavoriteButton :product-id="product.id" :product="product" :in-favorites="inFavorites" size-class="text-xl" />
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
