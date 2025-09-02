<template>
  <div class="p-4 bg-gray-100 rounded">
    <h3 class="text-lg font-semibold mb-4">Добавить товар</h3>

    <form @submit.prevent="submit">
      <!-- Категория -->
      <label class="block mb-1">Категория</label>
      <select
        v-model="form.category_id"
        data-error
        :class="[
          'w-full p-2 border rounded mb-1',
          form.errors.category_id ? 'border-red-500 ring-1 ring-red-500' : 'border-gray-300',
        ]"
      >
        <option disabled value="">Выберите подкатегорию</option>
        <option v-for="category in childCategories" :key="category.id" :value="category.id">
          {{ category.translation?.name ?? '—' }}
        </option>
      </select>
      <p v-if="form.errors.category_id" class="mt-1 text-sm text-red-600">{{ form.errors.category_id }}</p>

      <!-- Бренд -->
      <label class="block mt-3">Бренд</label>
      <select
        v-model="form.brand_id"
        data-error
        :class="[
          'w-full p-2 border rounded mb-1',
          form.errors.brand_id ? 'border-red-500 ring-1 ring-red-500' : 'border-gray-300',
        ]"
      >
        <option disabled value="">Выберите бренд</option>
        <option v-for="brand in brands" :key="brand.id" :value="brand.id">
          {{ brand.name }}
        </option>
      </select>
      <p v-if="form.errors.brand_id" class="mt-1 text-sm text-red-600">{{ form.errors.brand_id }}</p>

      <!-- Описания -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-4">
        <div v-for="lang in ['ru', 'ro', 'en']" :key="lang" class="bg-gray-50 p-4 rounded shadow-sm">
          <h4 class="font-semibold mb-2 uppercase">Описание ({{ lang }})</h4>

          <label class="block">Название</label>
          <input
            v-model="form.descriptions[lang].title"
            data-error
            :class="[
              'w-full p-2 border rounded mb-1',
              form.errors[`descriptions.${lang}.title`] ? 'border-red-500 ring-1 ring-red-500' : 'border-gray-300',
            ]"
          />
          <p v-if="form.errors[`descriptions.${lang}.title`]" class="mt-1 text-sm text-red-600">
            {{ form.errors[`descriptions.${lang}.title`] }}
          </p>

          <label class="block mt-2">Краткое описание</label>
          <textarea
            v-model="form.descriptions[lang].short_description"
            data-error
            :class="[
              'w-full p-2 border rounded mb-1',
              form.errors[`descriptions.${lang}.short_description`] ? 'border-red-500 ring-1 ring-red-500' : 'border-gray-300',
            ]"
          ></textarea>
          <p v-if="form.errors[`descriptions.${lang}.short_description`]" class="mt-1 text-sm text-red-600">
            {{ form.errors[`descriptions.${lang}.short_description`] }}
          </p>
        </div>
      </div>

      <!-- Цена -->
      <div class="mt-6">
        <label class="block">Базовая цена</label>
        <input
          type="number"
          step="0.01"
          v-model="form.price"
          data-error
          :class="[
            'w-full p-2 border rounded mb-1 max-w-xs',
            form.errors.price ? 'border-red-500 ring-1 ring-red-500' : 'border-gray-300',
          ]"
        />
        <p v-if="form.errors.price" class="mt-1 text-sm text-red-600">{{ form.errors.price }}</p>
      </div>

      <!-- Измерение -->
      <div class="mt-6">
        <label class="block">Единицы измерения</label>
        <select
          v-model="form.measurement"
          data-error
          :class="[
            'w-full p-2 border rounded mb-1 max-w-xs',
            form.errors.measurement ? 'border-red-500 ring-1 ring-red-500' : 'border-gray-300',
          ]"
        >
          <option disabled value="">Выберите единицу измерения</option>
          <option value="m²">m² (метр квадратный)</option>
          <option value="m.p.">m.p. (метр погонный)</option>
          <option value="kg">kg (килограмм)</option>
          <option value="pcs">pcs (штука)</option>
        </select>
        <p v-if="form.errors.measurement" class="mt-1 text-sm text-red-600">{{ form.errors.measurement }}</p>
      </div>

      <!-- Чекбокс создания вариаций -->
      <div class="mt-8 p-4 bg-yellow-50 border border-yellow-200 rounded">
        <label class="flex items-center">
          <input type="checkbox" v-model="form.create_variations" class="mr-2" />
          <span class="font-medium">Создать вариации товара</span>
        </label>
        <p class="text-sm text-gray-600 mt-1">Создать несколько товаров с разными характеристиками (размер, плотность и т.д.)</p>
      </div>

      <!-- Секция вариаций -->
      <div v-if="form.create_variations" class="mt-6 p-4 bg-blue-50 border border-blue-200 rounded">
        <h4 class="font-semibold mb-4">Настройка вариаций</h4>

        <!-- Общие фото для всех вариаций -->
        <div class="mb-6 p-3 bg-white rounded border">
          <label class="flex items-center mb-2">
            <input type="checkbox" v-model="form.use_common_images" class="mr-2" />
            <span class="font-medium">Использовать общие фото для всех вариаций</span>
          </label>
          <p class="text-sm text-gray-600 mb-3">Одни и те же изображения будут добавлены ко всем создаваемым товарам</p>

          <div v-if="form.use_common_images">
            <input type="file" multiple accept="image/*" @change="handleCommonImages" class="mb-2" />

            <!-- Предпросмотр общих фото -->
            <div v-if="form.common_images && form.common_images.length > 0" class="flex gap-2 flex-wrap">
              <div
                v-for="(file, index) in form.common_images"
                :key="index"
                class="relative w-16 h-16 border rounded overflow-hidden"
              >
                <img :src="getImagePreview(file)" alt="Preview" class="w-full h-full object-cover" />
                <button
                  type="button"
                  @click="removeCommonImage(index)"
                  class="absolute top-0 right-0 bg-red-600 text-white text-xs w-4 h-4 flex items-center justify-center"
                >
                  ×
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Выбор атрибутов для вариаций -->
        <div v-for="(selectedValues, attrId) in form.variation_attributes" :key="attrId" class="mb-4">
          <div class="flex items-center justify-between mb-2">
            <label class="font-medium">
              {{ getAttributeName(attrId) }}
            </label>
            <button type="button" @click="removeVariationAttribute(attrId)" class="text-red-600 hover:text-red-800 text-sm">
              Удалить
            </button>
          </div>

          <div class="flex flex-wrap gap-2">
            <label
              v-for="value in getAttributeValues(attrId)"
              :key="value.id"
              class="flex items-center p-2 bg-white border rounded cursor-pointer hover:bg-gray-50"
            >
              <input type="checkbox" :value="value.id" v-model="form.variation_attributes[attrId]" class="mr-2" />
              {{ value.translated_value }}
            </label>
          </div>
        </div>

        <!-- Кнопка добавления атрибута -->
        <div class="mb-4">
          <select v-model="selectedAttributeToAdd" class="w-full max-w-xs p-2 border rounded">
            <option value="">Добавить атрибут для вариаций...</option>
            <option v-for="attr in availableAttributes" :key="attr.id" :value="attr.id">
              {{ attr.translated_name }}
            </option>
          </select>
          <button
            type="button"
            @click="addVariationAttribute"
            :disabled="!selectedAttributeToAdd"
            class="ml-2 px-3 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 disabled:opacity-50"
          >
            Добавить
          </button>
        </div>

        <!-- Предпросмотр комбинаций -->
        <div v-if="generatedCombinations.length > 0" class="mt-6">
          <div class="flex items-center justify-between mb-2">
            <h5 class="font-medium">
              Будет создано комбинаций: {{ form.selected_combinations.length }} из {{ generatedCombinations.length }}
            </h5>

            <div class="flex gap-2">
              <button
                type="button"
                @click="selectAllCombinations"
                class="px-3 py-1 text-sm bg-green-600 text-white rounded hover:bg-green-700"
              >
                Выбрать все
              </button>
              <button
                type="button"
                @click="deselectAllCombinations"
                class="px-3 py-1 text-sm bg-gray-500 text-white rounded hover:bg-gray-600"
              >
                Снять все
              </button>
            </div>
          </div>

          <div class="max-h-64 overflow-y-auto border rounded p-2 bg-white">
            <div v-for="combination in generatedCombinations" :key="combination.key" class="p-3 border rounded bg-gray-50">
              <label class="flex items-center mb-2">
                <input type="checkbox" :value="combination.key" v-model="form.selected_combinations" class="mr-2" />
                <span class="text-sm font-medium">{{ combination.displayName }}</span>
              </label>

              <div class="flex items-center gap-4">
                <div>
                  <label class="text-xs text-gray-600">Цена:</label>
                  <input
                    type="number"
                    step="0.01"
                    v-model="form.prices[combination.key]"
                    :disabled="!form.selected_combinations.includes(combination.key)"
                    placeholder="Цена"
                    class="w-24 p-1 text-sm border rounded"
                  />
                </div>

                <!-- Загрузка изображений для вариации -->
                <div v-if="form.selected_combinations.includes(combination.key)">
                  <label class="text-xs text-gray-600">Фото:</label>
                  <input
                    type="file"
                    multiple
                    accept="image/*"
                    @change="handleVariationImages(combination.key, $event)"
                    class="text-xs w-32"
                  />

                  <!-- Предпросмотр выбранных фото -->
                  <div
                    v-if="form.variation_images[combination.key] && form.variation_images[combination.key].length > 0"
                    class="flex gap-1 mt-1"
                  >
                    <div
                      v-for="(file, index) in form.variation_images[combination.key]"
                      :key="index"
                      class="relative w-12 h-12 border rounded overflow-hidden"
                    >
                      <img :src="getImagePreview(file)" alt="Preview" class="w-full h-full object-cover" />
                      <button
                        type="button"
                        @click="removeVariationImage(combination.key, index)"
                        class="absolute top-0 right-0 bg-red-600 text-white text-xs w-4 h-4 flex items-center justify-center"
                      >
                        ×
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Обычные атрибуты товара -->
      <div v-if="!form.create_variations" class="mt-8">
        <h4 class="font-semibold mb-2">Атрибуты товара</h4>

        <div
          v-for="(attr, index) in form.attributes"
          :key="index"
          class="flex flex-col md:flex-row gap-2 items-start md:items-center mb-2"
        >
          <div class="w-full md:w-1/3">
            <select
              v-model="attr.attribute_id"
              data-error
              :class="[
                'w-full p-2 border rounded',
                form.errors[`attributes.${index}.attribute_id`] ? 'border-red-500 ring-1 ring-red-500' : 'border-gray-300',
              ]"
            >
              <option disabled value="">Атрибут</option>
              <option v-for="a in attributes" :key="a.id" :value="a.id">
                {{ a.translated_name }}
              </option>
            </select>
            <p v-if="form.errors[`attributes.${index}.attribute_id`]" class="mt-1 text-sm text-red-600">
              {{ form.errors[`attributes.${index}.attribute_id`] }}
            </p>
          </div>

          <div class="w-full md:w-1/3 flex items-start gap-2">
            <select
              v-model="attr.value_id"
              data-error
              :class="[
                'w-full p-2 border rounded',
                form.errors[`attributes.${index}.value_id`] ? 'border-red-500 ring-1 ring-red-500' : 'border-gray-300',
              ]"
            >
              <option disabled value="">Значение</option>
              <option v-for="v in filteredValues(attr.attribute_id)" :key="v.id" :value="v.id">
                {{ v.translated_value }}
              </option>
            </select>

            <button
              type="button"
              class="px-2 py-1 text-xs rounded bg-emerald-600 text-white hover:bg-emerald-700 disabled:opacity-50"
              :disabled="!attr.attribute_id"
              @click="openQuickAdd(index)"
              title="Добавить новое значение"
            >
              ➕
            </button>
          </div>

          <button
            type="button"
            @click="removeAttribute(index)"
            class="text-red-600 hover:text-red-800 text-xl"
            aria-label="Удалить атрибут"
          >
            ✖
          </button>
        </div>

        <button type="button" @click="addAttribute" class="mt-2 px-3 py-1 bg-gray-300 rounded hover:bg-gray-400 text-sm">
          ➕ Добавить атрибут
        </button>
      </div>

      <!-- Кнопки -->
      <div class="flex gap-2 mt-8">
        <button
          type="submit"
          :disabled="form.processing"
          :class="[
            'px-4 py-2 text-white rounded',
            form.processing ? 'bg-blue-400 cursor-not-allowed' : 'bg-blue-600 hover:bg-blue-700',
          ]"
        >
          {{ form.create_variations ? 'Создать вариации' : 'Сохранить товар' }}
        </button>
        <button type="button" @click="$emit('cancel')" class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600">
          Отмена
        </button>
      </div>
    </form>
  </div>

  <!-- Модалка быстрого добавления -->
  <QuickAddValueModal :isOpen="isQuickAddOpen" @close="isQuickAddOpen = false" @save="handleQuickAddSave" />
</template>

<script setup>
  import axios from 'axios'
  import QuickAddValueModal from '@/Pages/Admin/AttributeValues/QuickAddValueModal.vue'
  import { ref, computed, watch } from 'vue'
  import { useForm } from '@inertiajs/vue3'

  const emit = defineEmits(['productAdded', 'cancel'])

  const props = defineProps({
    categories: Array,
    attributes: Array,
    values: Array,
    brands: Array,
  })

  const form = useForm({
    category_id: '',
    brand_id: '',
    price: '',
    currency: 'MDL',
    measurement: '',
    descriptions: {
      ru: { title: '', short_description: '' },
      ro: { title: '', short_description: '' },
      en: { title: '', short_description: '' },
    },
    attributes: [],
    // Поля для вариаций
    create_variations: false,
    variation_attributes: {},
    selected_combinations: [],
    prices: {},
    variation_images: {}, // Храним файлы для каждой вариации
    use_common_images: false, // Использовать общие фото
    common_images: [], // Общие фото для всех вариаций
  })

  const childCategories = computed(() => props.categories.filter((cat) => cat.parent_id !== null))

  // Переменные для работы с вариациями
  const selectedAttributeToAdd = ref('')

  // Доступные атрибуты для добавления в вариации
  const availableAttributes = computed(() => {
    return props.attributes.filter((attr) => !form.variation_attributes.hasOwnProperty(attr.id))
  })

  // Генерация всех комбинаций
  const generatedCombinations = computed(() => {
    const attributes = form.variation_attributes
    const attrIds = Object.keys(attributes).filter((id) => attributes[id].length > 0)

    if (attrIds.length === 0) return []

    const combinations = []

    function generateRecursive(index, current) {
      if (index >= attrIds.length) {
        const key = attrIds.map((id) => `${id}_${current[id]}`).join('_')
        const displayName = attrIds
          .map((id) => {
            const value = allValues.value.find((v) => v.id === current[id])
            return value?.translated_value || '?'
          })
          .join(', ')

        combinations.push({ key, displayName, attributes: current })
        return
      }

      const attrId = attrIds[index]
      for (const valueId of attributes[attrId]) {
        generateRecursive(index + 1, { ...current, [attrId]: valueId })
      }
    }

    generateRecursive(0, {})
    return combinations
  })

  // Методы для работы с общими изображениями
  const handleCommonImages = (event) => {
    const files = Array.from(event.target.files)
    form.common_images.push(...files)
  }

  const removeCommonImage = (index) => {
    form.common_images.splice(index, 1)
  }

  // Методы для работы с изображениями вариаций
  const handleVariationImages = (combinationKey, event) => {
    const files = Array.from(event.target.files)
    if (!form.variation_images[combinationKey]) {
      form.variation_images[combinationKey] = []
    }
    form.variation_images[combinationKey].push(...files)
  }

  const removeVariationImage = (combinationKey, index) => {
    if (form.variation_images[combinationKey]) {
      form.variation_images[combinationKey].splice(index, 1)
    }
  }

  const getImagePreview = (file) => {
    return URL.createObjectURL(file)
  }

  // Методы для работы с комбинациями
  const selectAllCombinations = () => {
    form.selected_combinations = generatedCombinations.value.map((c) => c.key)
    // Устанавливаем базовую цену для всех
    generatedCombinations.value.forEach((combo) => {
      if (!form.prices[combo.key]) {
        form.prices[combo.key] = form.price
      }
    })
  }

  const deselectAllCombinations = () => {
    form.selected_combinations = []
  }

  // Автовыбор всех комбинаций при их генерации
  watch(
    generatedCombinations,
    (newCombinations) => {
      if (newCombinations.length > 0) {
        // Выбираем все новые комбинации
        const newKeys = newCombinations.map((c) => c.key)
        const existingKeys = form.selected_combinations.filter((key) => newKeys.includes(key))

        // Добавляем новые комбинации к уже выбранным
        form.selected_combinations = [...existingKeys, ...newKeys.filter((key) => !existingKeys.includes(key))]

        // Устанавливаем базовые цены для новых комбинаций
        newCombinations.forEach((combo) => {
          if (!form.prices[combo.key]) {
            form.prices[combo.key] = form.price
          }
        })
      }
    },
    { deep: true }
  )
  const addVariationAttribute = () => {
    if (selectedAttributeToAdd.value) {
      form.variation_attributes[selectedAttributeToAdd.value] = []
      selectedAttributeToAdd.value = ''
    }
  }

  const removeVariationAttribute = (attrId) => {
    delete form.variation_attributes[attrId]
  }

  const getAttributeName = (attrId) => {
    return props.attributes.find((a) => a.id == attrId)?.translated_name || '?'
  }

  const getAttributeValues = (attrId) => {
    return allValues.value.filter((v) => v.attribute_id == attrId)
  }

  // Сброс данных вариаций при отключении
  watch(
    () => form.create_variations,
    (newVal) => {
      if (!newVal) {
        form.variation_attributes = {}
        form.selected_combinations = []
        form.prices = {}
        form.variation_images = {}
        form.use_common_images = false
        form.common_images = []
        form.measurement = {}
      }
    }
  )

  const submit = () => {
    if (form.create_variations) {
      // Для вариаций отправляем через FormData
      const formData = new FormData()

      // Добавляем основные поля
      formData.append('category_id', form.category_id)
      formData.append('brand_id', form.brand_id || '')
      formData.append('price', form.price)
      formData.append('currency', form.currency)
      formData.append('create_variations', 'true')
      formData.append('measurement', form.measurement)

      // Добавляем описания
      Object.keys(form.descriptions).forEach((lang) => {
        formData.append(`descriptions[${lang}][title]`, form.descriptions[lang].title)
        formData.append(`descriptions[${lang}][short_description]`, form.descriptions[lang].short_description)
      })

      // Добавляем атрибуты вариаций
      Object.keys(form.variation_attributes).forEach((attrId) => {
        form.variation_attributes[attrId].forEach((valueId, index) => {
          formData.append(`variation_attributes[${attrId}][${index}]`, valueId)
        })
      })

      // Добавляем выбранные комбинации
      form.selected_combinations.forEach((combo, index) => {
        formData.append(`selected_combinations[${index}]`, combo)
      })

      // Добавляем цены
      Object.keys(form.prices).forEach((combo) => {
        formData.append(`prices[${combo}]`, form.prices[combo])
      })

      // Добавляем изображения вариаций
      Object.keys(form.variation_images).forEach((combinationKey) => {
        const images = form.variation_images[combinationKey]
        if (images && images.length > 0) {
          images.forEach((file) => {
            formData.append(`variation_images[${combinationKey}][]`, file)
          })
        }
      })

      form.post('/admin/products/store', {
        data: formData,
        forceFormData: true,
        onSuccess: () => {
          form.reset()
          emit('productAdded')
        },
        onError: () => {
          queueMicrotask(() => {
            const el =
              document.querySelector('[data-error].ring-red-500, [data-error].border-red-500') ||
              document.querySelector('.text-red-600')
            el?.scrollIntoView({ behavior: 'smooth', block: 'center' })
            el?.focus?.()
          })
        },
        preserveScroll: true,
      })
    } else {
      // Обычная отправка для простых товаров
      form.post('/admin/products/store', {
        onSuccess: () => {
          form.reset()
          emit('productAdded')
        },
        onError: () => {
          queueMicrotask(() => {
            const el =
              document.querySelector('[data-error].ring-red-500, [data-error].border-red-500') ||
              document.querySelector('.text-red-600')
            el?.scrollIntoView({ behavior: 'smart', block: 'center' })
            el?.focus?.()
          })
        },
        preserveScroll: true,
      })
    }
  }

  // Обычные атрибуты (для не-вариационных товаров)
  const addAttribute = () => form.attributes.push({ attribute_id: '', value_id: '' })
  const removeAttribute = (index) => form.attributes.splice(index, 1)

  const allValues = ref([...(props.values ?? [])])
  const filteredValues = (attrId) => allValues.value.filter((v) => v.attribute_id === attrId)

  const isQuickAddOpen = ref(false)
  const quickAddIndex = ref(null)

  const openQuickAdd = (index) => {
    quickAddIndex.value = index
    isQuickAddOpen.value = true
  }

  const handleQuickAddSave = async ({ ru, ro, en }) => {
    const row = quickAddIndex.value
    const attrId = form.attributes[row]?.attribute_id
    if (!attrId) {
      alert('Сначала выберите атрибут.')
      return
    }

    try {
      const { data } = await axios.post('/admin/attribute-values/quick-store', {
        attribute_id: attrId,
        translations: { ru, ro, en },
      })

      allValues.value.push(data)
      form.attributes[row].value_id = data.id
      isQuickAddOpen.value = false
    } catch (e) {
      const msg =
        e?.response?.data?.message ||
        (e?.response?.data?.errors && Object.values(e.response.data.errors).flat().join('\n')) ||
        'Не удалось создать значение.'
      alert(msg)
    }
  }
</script>
