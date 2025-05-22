<script setup>
  import { ref, computed } from 'vue'
  import { useCartStore } from '@/Stores/cart'
  import axios from 'axios'
  import { useModalScrollLock } from '@/composables/useModalScrollLock'

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

  // 💰 Итоги
  const totalQuantity = computed(() => Object.values(cart.items).reduce((sum, qty) => sum + qty, 0))

  const totalWithoutDiscount = computed(() => cart.products.reduce((sum, p) => sum + p.price * cart.items[p.id], 0))

  const totalWithDiscount = computed(() =>
    cart.products.reduce((sum, p) => {
      const price = parseFloat(p.discounted_price ?? p.price)
      return sum + price * cart.items[p.id]
    }, 0)
  )

  const totalDiscount = computed(() => totalWithoutDiscount.value - totalWithDiscount.value)

  // 🚀 Отправка
  const submitOrder = async () => {
    if (!validateForm()) {
      error.value = 'Пожалуйста, заполните обязательные поля'
      return
    }

    loading.value = true
    error.value = null

    try {
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
        cart: cart.items,
      })

      cart.clear()
      emit('close')
      alert('✅ Заказ успешно отправлен!')
    } catch (err) {
      error.value = 'Ошибка при отправке заказа'
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

      <h2 class="text-2xl font-bold mb-6 text-center">Оформление заказа</h2>
      <div class="text-sm"><p>(* - обязательные поля)</p></div>
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Левая часть -->
        <div class="lg:col-span-2 space-y-4">
          <!-- Контактные данные -->
          <div class="bg-gray-100 border border-gray-200 rounded-lg overflow-hidden">
            <div class="border-b border-gray-200 px-4 py-2">
              <h3 class="text-sm font-semibold text-gray-700">Контактные данные</h3>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 px-4 py-3">
              <div>
                <input v-model="lastName" type="text" placeholder="Фамилия*" class="w-full border rounded p-2 text-sm" />
                <p v-if="errors.lastName" class="text-xs text-red-500 mt-1">{{ errors.lastName }}</p>
              </div>

              <div>
                <input v-model="firstName" type="text" placeholder="Имя*" class="w-full border rounded p-2 text-sm" />
                <p v-if="errors.firstName" class="text-xs text-red-500 mt-1">{{ errors.firstName }}</p>
              </div>

              <div>
                <input v-model="phone" type="text" placeholder="Телефон*" class="w-full border rounded p-2 text-sm" />
                <p v-if="errors.phone" class="text-xs text-red-500 mt-1">{{ errors.phone }}</p>
              </div>

              <div>
                <input v-model="email" type="email" placeholder="Email" class="w-full border rounded p-2 text-sm" />
              </div>
            </div>
          </div>

          <!-- Способ получения -->
          <div class="bg-gray-100 border border-gray-200 rounded-lg overflow-hidden">
            <div class="border-b border-gray-200 px-4 py-2">
              <h3 class="text-sm font-semibold text-gray-700">Способ получения</h3>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 px-4 py-3">
              <label class="flex items-center gap-2 border rounded px-3 py-2 cursor-pointer text-sm">
                <input type="radio" v-model="deliveryMethod" value="pickup" />
                <span>Самовывоз</span>
              </label>
              <label class="flex items-center gap-2 border rounded px-3 py-2 cursor-pointer text-sm">
                <input type="radio" v-model="deliveryMethod" value="delivery" />
                <span>Доставка</span>
              </label>
            </div>
          </div>

          <!-- Адрес доставки -->
          <div v-if="deliveryMethod === 'delivery'" class="bg-gray-100 border border-gray-200 rounded-lg overflow-hidden">
            <div class="border-b border-gray-200 px-4 py-2">
              <h3 class="text-sm font-semibold text-gray-700">Адрес доставки</h3>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 px-4 py-3">
              <div>
                <input v-model="country" type="text" placeholder="Страна*" class="w-full border rounded p-2 text-sm" />
                <p v-if="errors.country" class="text-xs text-red-500 mt-1">{{ errors.country }}</p>
              </div>

              <div>
                <select v-model="region" class="w-full border rounded p-2 text-sm bg-white">
                  <option disabled value="">-- Выберите регион --</option>
                  <option v-for="r in moldovaRegions" :key="r" :value="r">{{ r }}</option>
                </select>
                <p v-if="errors.region" class="text-xs text-red-500 mt-1">{{ errors.region }}</p>
              </div>

              <div>
                <input v-model="city" type="text" placeholder="Населённый пункт*" class="w-full border rounded p-2 text-sm" />
                <p v-if="errors.city" class="text-xs text-red-500 mt-1">{{ errors.city }}</p>
              </div>

              <div class="sm:col-span-2">
                <input
                  v-model="address"
                  type="text"
                  placeholder="Адрес (улица, дом, участок...)*"
                  class="w-full border rounded p-2 text-sm"
                />
                <p v-if="errors.address" class="text-xs text-red-500 mt-1">{{ errors.address }}</p>
              </div>
            </div>
          </div>

          <!-- Способы оплаты -->
          <div class="bg-gray-100 border border-gray-200 rounded-lg overflow-hidden">
            <div class="border-b border-gray-200 px-4 py-2">
              <h3 class="text-sm font-semibold text-gray-700">Способы оплаты</h3>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 px-4 py-3">
              <label class="flex items-center gap-2 border rounded px-3 py-2 cursor-pointer text-sm">
                <input type="radio" v-model="paymentMethod" value="cash" />
                <span>Наличными</span>
              </label>
              <label class="flex items-center gap-2 border rounded px-3 py-2 cursor-pointer text-sm">
                <input type="radio" v-model="paymentMethod" value="card" />
                <span>Картой курьеру</span>
              </label>
              <label class="flex items-center gap-2 border rounded px-3 py-2 cursor-pointer text-sm">
                <input type="radio" v-model="paymentMethod" value="invoice" />
                <span>Банковский чек</span>
              </label>
            </div>
          </div>

          <!-- Комментарий -->
          <div class="bg-gray-100 border border-gray-200 rounded-lg overflow-hidden">
            <div class="border-b border-gray-200 px-4 py-2">
              <h3 class="text-sm font-semibold text-gray-700">Дополнительная информация</h3>
            </div>
            <div class="px-4 py-3">
              <textarea
                v-model="comment"
                rows="3"
                placeholder="Комментарий к заказу..."
                class="w-full border rounded p-2 text-sm"
              />
            </div>
          </div>
        </div>

        <!-- 🔸 Правая часть -->
        <div class="bg-gray-100 p-4 rounded-lg space-y-4">
          <h3 class="text-lg font-semibold border-b pb-2">Ваш заказ</h3>

          <div v-for="product in cart.products" :key="product.id" class="flex items-start gap-4 text-sm">
            <img
              :src="product.main_image ? `/storage/${product.main_image}` : '/images/placeholder.jpg'"
              class="w-14 h-14 object-cover rounded bg-white border"
            />
            <div class="flex-1">
              <p class="font-medium truncate">{{ product.description?.title ?? 'Без названия' }}</p>
              <p class="text-gray-500 text-xs">
                {{ cart.items[product.id] }} × {{ product.discounted_price ?? product.price }} {{ product.currency ?? 'mdl' }}
              </p>
            </div>
          </div>

          <div class="border-t pt-4 space-y-2 text-sm">
            <div class="flex justify-between">
              <span>Товаров:</span>
              <span>{{ totalQuantity }}</span>
            </div>
            <div class="flex justify-between">
              <span>Сумма без скидки:</span>
              <span>{{ totalWithoutDiscount.toFixed(2) }} mdl</span>
            </div>
            <div class="flex justify-between text-green-600">
              <span>Скидка:</span>
              <span>-{{ totalDiscount.toFixed(2) }} mdl</span>
            </div>
            <div class="flex justify-between font-bold text-base pt-1">
              <span>Итог:</span>
              <span>{{ totalWithDiscount.toFixed(2) }} mdl</span>
            </div>
          </div>

          <button
            @click="submitOrder"
            :disabled="loading"
            class="w-full bg-pink-500 hover:bg-pink-600 text-white py-2 rounded mt-4 transition-colors"
          >
            {{ loading ? 'Отправка...' : 'Подтвердить заказ' }}
          </button>

          <p v-if="error" class="text-red-500 text-sm mt-2">{{ error }}</p>
        </div>
      </div>
    </div>
  </div>
</template>
