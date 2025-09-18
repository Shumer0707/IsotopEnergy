<script setup>
  import { ref, computed } from 'vue'
  import { useCartStore } from '@/Stores/cart'
  import axios from 'axios'
  import { useModalScrollLock } from '@/composables/useModalScrollLock'
  import { useTranslations } from '@/composables/useTranslations'

  const t = useTranslations()
  useModalScrollLock()
  const emit = defineEmits(['close'])
  const cart = useCartStore()
  const errors = ref({})
  const moldovaRegions = [
    'Chișinău',
    'Bălți',
    'Orhei',
    'Anenii Noi',
    'Cahul',
    'Comrat',
    'Edineț',
    'Hîncești',
    'Soroca',
    'Strășeni',
    'Ungheni',
    'Căușeni',
    'Călărași',
    'Dubăsari',
    'Florești',
    'Glodeni',
    'Ialoveni',
    'Leova',
    'Nisporeni',
    'Rezina',
    'Rîșcani',
    'Sîngerei',
    'Ștefan Vodă',
    'Taraclia',
    'Telenești',
    'Șoldănești',
  ]

  // 🧍‍ Контакты
  const firstName = ref('')
  const lastName = ref('')
  const phone = ref('')
  const email = ref('')

  // 📦 Адрес
  const deliveryMethod = ref('pickup') // 'pickup' | 'delivery'
  const country = ref('Moldova, Republic of')
  const region = ref('')
  const city = ref('')
  const zip = ref('')
  const address = ref('')

  // 💳 Оплата и коммент
  const paymentMethod = ref('cash')
  const comment = ref('')

  // 🛠 Состояния
  const loading = ref(false)
  const error = ref(null)

  // ✅ ИСПРАВЛЯЕМ: Используем геттеры из cart store
  const totalQuantity = computed(() => cart.totalQuantity)
  const totalWithoutDiscount = computed(() => cart.totalWithoutDiscount)
  const totalWithDiscount = computed(() => cart.totalWithDiscount)
  const totalDiscount = computed(() => cart.totalDiscount)

  // ✅ НОВАЯ ФУНКЦИЯ: Форматирование атрибутов варианта
  const formatVariantAttributes = (variant) => {
    if (!variant.attributes || variant.attributes.length === 0) {
      return ''
    }

    return variant.attributes.map((attr) => `${attr.name}: ${attr.value}`).join(', ')
  }

  // 🚀 Отправка
  const submitOrder = async () => {
    if (!validateForm()) {
      error.value = 'Пожалуйста, заполните обязательные поля'
      return
    }

    loading.value = true
    error.value = null

    try {
      // ✅ ИЗМЕНЕНИЕ: Отправляем cart.items (variant_id: quantity) вместо старой структуры
      await axios.post('/order', {
        first_name: firstName.value,
        last_name: lastName.value,
        phone: phone.value,
        email: email.value,
        comment: comment.value,
        delivery_method: deliveryMethod.value,
        address:
          deliveryMethod.value === 'delivery'
            ? {
                country: country.value,
                region: region.value,
                city: city.value,
                zip: zip.value,
                street: address.value,
              }
            : null,
        payment_method: paymentMethod.value,
        cart: cart.items, // Теперь это { [variantId]: quantity }
      })

      cart.clear()
      emit('close')
      alert('✅ Заказ успешно отправлен!')
    } catch (err) {
      error.value = 'Ошибка при отправке заказа'
      console.error('Order submission error:', err)
    } finally {
      loading.value = false
    }
  }

  const isFormValid = computed(() => {
    const requiredFilled = firstName.value.trim() && lastName.value.trim() && phone.value.trim()

    const addressFilled =
      deliveryMethod.value === 'pickup' ||
      (country.value.trim() && region.value.trim() && city.value.trim() && address.value.trim())

    return requiredFilled && addressFilled && paymentMethod.value
  })

  const validateForm = () => {
    errors.value = {}

    if (!lastName.value.trim()) errors.value.lastName = 'Укажите фамилию'
    if (!firstName.value.trim()) errors.value.firstName = 'Укажите имя'
    if (!phone.value.trim()) errors.value.phone = 'Укажите телефон'

    if (deliveryMethod.value === 'delivery') {
      if (!country.value.trim()) errors.value.country = 'Укажите страну'
      if (!region.value.trim()) errors.value.region = 'Укажите регион'
      if (!city.value.trim()) errors.value.city = 'Укажите город'
      if (!address.value.trim()) errors.value.address = 'Укажите адрес'
    }

    return Object.keys(errors.value).length === 0
  }
</script>

<template>
  <div class="fixed inset-0 bg-black/40 z-50 flex items-center justify-center animate-fade-in" @click.self="emit('close')">
    <div class="bg-white rounded-xl p-6 w-full max-w-5xl shadow-lg relative overflow-y-auto max-h-[90vh] animate-fade-in">
      <button class="absolute top-2 right-3 text-gray-400 hover:text-black text-2xl" @click="emit('close')">×</button>

      <h2 class="text-2xl font-bold mb-6 text-center">{{ t['order_title'] }}</h2>
      <div class="text-sm">
        <p>(* - {{ t['order_mandatory'] }})</p>
      </div>
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Левая часть - форма заказа (без изменений) -->
        <div class="lg:col-span-2 space-y-4">
          <!-- Контактные данные -->
          <div class="bg-gray-100 border border-gray-200 rounded-lg overflow-hidden">
            <div class="border-b border-gray-200 px-4 py-2">
              <h3 class="text-sm font-semibold text-gray-700">{{ t['order_data_title'] }}</h3>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 px-4 py-3">
              <div>
                <input v-model="lastName" type="text" :placeholder="t.order_surname" class="w-full border rounded p-2 text-sm" />
                <p v-if="errors.lastName" class="text-xs text-red-500 mt-1">{{ errors.lastName }}</p>
              </div>

              <div>
                <input v-model="firstName" type="text" :placeholder="t.order_name" class="w-full border rounded p-2 text-sm" />
                <p v-if="errors.firstName" class="text-xs text-red-500 mt-1">{{ errors.firstName }}</p>
              </div>

              <div>
                <input v-model="phone" type="text" :placeholder="t.order_phone" class="w-full border rounded p-2 text-sm" />
                <p v-if="errors.phone" class="text-xs text-red-500 mt-1">{{ errors.phone }}</p>
              </div>

              <div>
                <input v-model="email" type="email" :placeholder="t.order_mail" class="w-full border rounded p-2 text-sm" />
              </div>
            </div>
          </div>

          <!-- Способ получения -->
          <div class="bg-gray-100 border border-gray-200 rounded-lg overflow-hidden">
            <div class="border-b border-gray-200 px-4 py-2">
              <h3 class="text-sm font-semibold text-gray-700">{{ t['order_delivery_title'] }}</h3>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 px-4 py-3">
              <label class="flex items-center gap-2 border rounded px-3 py-2 cursor-pointer text-sm">
                <input type="radio" v-model="deliveryMethod" value="pickup" />
                <span>{{ t['order_pickup'] }}</span>
              </label>
              <label class="flex items-center gap-2 border rounded px-3 py-2 cursor-pointer text-sm">
                <input type="radio" v-model="deliveryMethod" value="delivery" />
                <span>{{ t['order_delivery'] }}</span>
              </label>
            </div>
          </div>

          <!-- Адрес доставки -->
          <div v-if="deliveryMethod === 'delivery'" class="bg-gray-100 border border-gray-200 rounded-lg overflow-hidden">
            <div class="border-b border-gray-200 px-4 py-2">
              <h3 class="text-sm font-semibold text-gray-700">{{ t['order_delivery_address'] }}</h3>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 px-4 py-3">
              <div>
                <input v-model="country" type="text" :placeholder="t.order_country" class="w-full border rounded p-2 text-sm" />
                <p v-if="errors.country" class="text-xs text-red-500 mt-1">{{ errors.country }}</p>
              </div>

              <div>
                <select v-model="region" class="w-full border rounded p-2 text-sm bg-white">
                  <option disabled value="">-- {{ t['order_region'] }} --</option>
                  <option v-for="r in moldovaRegions" :key="r" :value="r">{{ r }}</option>
                </select>
                <p v-if="errors.region" class="text-xs text-red-500 mt-1">{{ errors.region }}</p>
              </div>

              <div>
                <input v-model="city" type="text" :placeholder="t.order_settlement" class="w-full border rounded p-2 text-sm" />
                <p v-if="errors.city" class="text-xs text-red-500 mt-1">{{ errors.city }}</p>
              </div>

              <div class="sm:col-span-2">
                <input v-model="address" type="text" :placeholder="t.order_address" class="w-full border rounded p-2 text-sm" />
                <p v-if="errors.address" class="text-xs text-red-500 mt-1">{{ errors.address }}</p>
              </div>
            </div>
          </div>

          <!-- Способы оплаты -->
          <div class="bg-gray-100 border border-gray-200 rounded-lg overflow-hidden">
            <div class="border-b border-gray-200 px-4 py-2">
              <h3 class="text-sm font-semibold text-gray-700">{{ t['order_payment_title'] }}</h3>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 px-4 py-3">
              <label class="flex items-center gap-2 border rounded px-3 py-2 cursor-pointer text-sm">
                <input type="radio" v-model="paymentMethod" value="cash" />
                <span>{{ t['order_cash'] }}</span>
              </label>
              <label class="flex items-center gap-2 border rounded px-3 py-2 cursor-pointer text-sm">
                <input type="radio" v-model="paymentMethod" value="card" />
                <span>{{ t['order_card'] }}</span>
              </label>
              <label class="flex items-center gap-2 border rounded px-3 py-2 cursor-pointer text-sm">
                <input type="radio" v-model="paymentMethod" value="invoice" />
                <span>{{ t['order_check'] }}</span>
              </label>
            </div>
          </div>

          <!-- Комментарий -->
          <div class="bg-gray-100 border border-gray-200 rounded-lg overflow-hidden">
            <div class="border-b border-gray-200 px-4 py-2">
              <h3 class="text-sm font-semibold text-gray-700">{{ t['order_information'] }}</h3>
            </div>
            <div class="px-4 py-3">
              <textarea v-model="comment" rows="3" :placeholder="t.order_comment" class="w-full border rounded p-2 text-sm" />
            </div>
          </div>
        </div>

        <!-- ✅ ОБНОВЛЕННАЯ правая часть - список вариантов -->
        <div class="bg-gray-100 p-4 rounded-lg space-y-4">
          <h3 class="text-lg font-semibold border-b pb-2">{{ t['cart_details'] }}</h3>

          <!-- Список вариантов вместо товаров -->
          <div
            v-for="variant in cart.variants"
            :key="variant.id"
            class="flex items-start gap-3 text-sm border-b border-gray-200 pb-3 last:border-b-0"
          >
            <img
              :src="variant.product.main_image ? `/storage/${variant.product.main_image}` : '/images/placeholder.jpg'"
              class="w-12 h-12 object-cover rounded bg-white border shrink-0"
            />
            <div class="flex-1 min-w-0">
              <p class="font-medium truncate" :title="variant.product.title">
                {{ variant.product.title || 'Без названия' }}
              </p>

              <!-- Показываем атрибуты варианта -->
              <p v-if="formatVariantAttributes(variant)" class="text-xs text-gray-600 mt-1 truncate">
                {{ formatVariantAttributes(variant) }}
              </p>

              <!-- Артикул варианта -->
              <p class="text-xs text-gray-500 mt-1">{{ variant.sku }}</p>

              <!-- Количество и цена -->
              <div class="flex justify-between items-center mt-2">
                <span class="text-xs text-gray-600">{{ cart.getVariantQuantity(variant.id) }} шт</span>
                <div class="text-right">
                  <div
                    v-if="variant.discounted_price && variant.discounted_price !== variant.price"
                    class="text-xs line-through text-gray-400"
                  >
                    {{ variant.price }} {{ variant.product.currency }}
                  </div>
                  <div class="text-sm font-medium">
                    {{ ((variant.discounted_price || variant.price) * cart.getVariantQuantity(variant.id)).toFixed(2) }}
                    {{ variant.product.currency }}
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Итоги заказа -->
          <div class="border-t pt-4 space-y-2 text-sm">
            <div class="flex justify-between">
              <span>{{ t['cart_all_items'] }}</span>
              <span>{{ totalQuantity }}</span>
            </div>
            <div class="flex justify-between">
              <span>{{ t['cart_not_discount'] }}</span>
              <span>{{ totalWithoutDiscount.toFixed(2) }} MDL</span>
            </div>
            <div class="flex justify-between text-my_red">
              <span>{{ t['cart_discount'] }}</span>
              <span>-{{ totalDiscount.toFixed(2) }} MDL</span>
            </div>
            <div class="flex justify-between font-bold text-base pt-1 border-t">
              <span>{{ t['cart_all_price'] }}</span>
              <span>{{ totalWithDiscount.toFixed(2) }} MDL</span>
            </div>
          </div>

          <button
            @click="submitOrder"
            :disabled="loading || cart.variants.length === 0"
            class="w-full bg-my_green hover:bg-my_green_op text-white py-2 rounded mt-4 transition-colors disabled:bg-gray-400 disabled:cursor-not-allowed"
          >
            {{ loading ? 'Отправка...' : t.order_send }}
          </button>

          <p v-if="error" class="text-red-500 text-sm mt-2">{{ error }}</p>
        </div>
      </div>
    </div>
  </div>
</template>
