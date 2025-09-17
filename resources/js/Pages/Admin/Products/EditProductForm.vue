<template>
  <div class="p-4 bg-yellow-50 rounded">
    <h3 class="text-lg font-semibold mb-4">Редактировать товар</h3>

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

          <!-- Полное описание (опционально) -->
          <label class="block mt-2">Полное описание (опционально)</label>
          <textarea
            v-model="form.descriptions[lang].full_description"
            class="w-full p-2 border rounded border-gray-300"
          ></textarea>
        </div>
      </div>

      <!-- Базовый SKU и измерение -->
      <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
          <label class="block">Базовый артикул</label>
          <input
            type="text"
            v-model="form.base_sku"
            data-error
            :class="[
              'w-full p-2 border rounded mb-1',
              form.errors.base_sku ? 'border-red-500 ring-1 ring-red-500' : 'border-gray-300',
            ]"
          />
          <p v-if="form.errors.base_sku" class="mt-1 text-sm text-red-600">{{ form.errors.base_sku }}</p>
        </div>

        <div>
          <label class="block">Единицы измерения</label>
          <select
            v-model="form.measurement"
            data-error
            :class="[
              'w-full p-2 border rounded mb-1',
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
      </div>

      <!-- СЕКЦИЯ ВАРИАНТОВ ТОВАРА -->
      <div class="mt-8">
        <div class="flex items-center justify-between mb-4">
          <h4 class="text-lg font-semibold">Варианты товара ({{ form.variants.length }})</h4>
          <button type="button" @click="addVariant" class="px-3 py-2 bg-green-600 text-white rounded hover:bg-green-700">
            + Добавить вариант
          </button>
        </div>

        <!-- Список вариантов -->
        <div v-if="form.variants.length > 0" class="space-y-4">
          <div
            v-for="(variant, index) in form.variants"
            :key="variant.id || index"
            class="border rounded-lg p-4 bg-white"
            :class="{ 'ring-2 ring-blue-300': variant.is_default }"
          >
            <div class="flex items-center justify-between mb-3">
              <div class="flex items-center gap-2">
                <span class="text-sm font-medium text-gray-600">Вариант {{ index + 1 }}</span>
                <span v-if="variant.is_default" class="px-2 py-1 text-xs bg-blue-100 text-blue-800 rounded">По умолчанию</span>
                <span class="text-xs text-gray-500">SKU: {{ variant.sku }}</span>
              </div>

              <div class="flex items-center gap-2">
                <label class="text-xs">
                  <input
                    type="radio"
                    :value="index"
                    v-model="defaultVariantIndex"
                    @change="setDefaultVariant(index)"
                    class="mr-1"
                  />
                  По умолчанию
                </label>
                <button
                  type="button"
                  @click="removeVariant(index)"
                  :disabled="form.variants.length <= 1"
                  class="px-2 py-1 text-xs bg-red-600 text-white rounded hover:bg-red-700 disabled:opacity-50"
                >
                  Удалить
                </button>
              </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <!-- Цена варианта -->
              <div>
                <label class="block text-sm font-medium mb-1">Цена</label>
                <input
                  type="number"
                  step="0.01"
                  v-model="variant.price"
                  data-error
                  :class="[
                    'w-full p-2 border rounded',
                    form.errors[`variants.${index}.price`] ? 'border-red-500 ring-1 ring-red-500' : 'border-gray-300',
                  ]"
                />
                <p v-if="form.errors[`variants.${index}.price`]" class="mt-1 text-sm text-red-600">
                  {{ form.errors[`variants.${index}.price`] }}
                </p>
              </div>

              <!-- Атрибуты варианта -->
              <div>
                <label class="block text-sm font-medium mb-1">Атрибуты</label>
                <div v-if="variant.attributes && variant.attributes.length > 0" class="space-y-1">
                  <div
                    v-for="attr in variant.attributes"
                    :key="attr.attribute_id + '_' + attr.value_id"
                    class="flex items-center justify-between bg-gray-50 p-2 rounded text-sm"
                  >
                    <span>
                      <strong>{{ attr.attribute_name }}:</strong>
                      {{ attr.value_name }}
                    </span>
                    <button type="button" @click="removeVariantAttribute(index, attr)" class="text-red-600 hover:text-red-800">
                      ×
                    </button>
                  </div>
                </div>
                <div v-else class="text-sm text-gray-500 italic">Атрибутов нет</div>

                <!-- Кнопка добавления атрибута -->
                <button
                  type="button"
                  @click="openAddVariantAttribute(index)"
                  class="mt-2 px-2 py-1 text-xs bg-blue-600 text-white rounded hover:bg-blue-700"
                >
                  + Добавить атрибут
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Если нет вариантов -->
        <div v-else class="text-center py-8 text-gray-500">
          <p>У товара нет вариантов</p>
          <button type="button" @click="addVariant" class="mt-2 px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">
            Создать первый вариант
          </button>
        </div>
      </div>

      <!-- Отладочная секция для просмотра данных -->
      <div class="mt-8 p-4 bg-gray-100 rounded">
        <details>
          <summary class="cursor-pointer font-medium">🔍 Отладочные данные</summary>
          <div class="mt-2 text-xs">
            <h5 class="font-medium mb-2">Исходные данные товара:</h5>
            <pre class="bg-white p-2 rounded overflow-auto">{{
              JSON.stringify(
                {
                  id: product.id,
                  base_sku: product.base_sku,
                  variants: product.variants,
                  variantsForEdit: product.variantsForEdit,
                },
                null,
                2
              )
            }}</pre>

            <h5 class="font-medium mb-2 mt-4">Данные формы:</h5>
            <pre class="bg-white p-2 rounded overflow-auto">{{
              JSON.stringify(
                {
                  variants: form.variants,
                  defaultVariantIndex: defaultVariantIndex,
                },
                null,
                2
              )
            }}</pre>
          </div>
        </details>
      </div>

      <!-- Кнопки -->
      <div class="flex gap-2 mt-8">
        <button
          type="submit"
          :disabled="form.processing"
          :class="[
            'px-4 py-2 text-white rounded',
            form.processing ? 'bg-yellow-400 cursor-not-allowed' : 'bg-yellow-600 hover:bg-yellow-700',
          ]"
        >
          Обновить товар
        </button>
        <button type="button" @click="$emit('cancel')" class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600">
          Отмена
        </button>
      </div>
    </form>
  </div>

  <!-- Модалка быстрого добавления -->
  <QuickAddValueModal :isOpen="isQuickAddOpen" @close="isQuickAddOpen = false" @save="handleQuickAddSave" />

  <!-- Модалка добавления атрибута к варианту -->
  <AddVariantAttributeModal
    :isOpen="isVariantAttributeModalOpen"
    :attributes="attributes"
    :values="values"
    :existingAttributes="currentVariantAttributes"
    @close="closeVariantAttributeModal"
    @save="handleVariantAttributeSave"
  />
</template>

<script setup>
  import axios from 'axios'
  import QuickAddValueModal from '@/Pages/Admin/AttributeValues/QuickAddValueModal.vue'
  import AddVariantAttributeModal from './AddVariantAttributeModal.vue'
  import { computed, ref } from 'vue'
  import { useForm } from '@inertiajs/vue3'

  const emit = defineEmits(['productUpdated', 'cancel'])

  const props = defineProps({
    product: Object,
    categories: Array,
    attributes: Array,
    brands: Array,
    values: Array,
  })

  const childCategories = computed(() => props.categories.filter((cat) => cat.parent_id !== null))

  // Находим индекс дефолтного варианта
  const findDefaultVariantIndex = () => {
    if (!props.product.variantsForEdit) return 0
    const defaultIndex = props.product.variantsForEdit.findIndex((v) => v.is_default)
    return defaultIndex >= 0 ? defaultIndex : 0
  }

  const defaultVariantIndex = ref(findDefaultVariantIndex())

  const form = useForm({
    id: props.product.id,
    category_id: props.product.category_id ?? '',
    brand_id: props.product.brand_id ?? '',
    base_sku: props.product.base_sku ?? '',
    currency: props.product.currency ?? 'MDL',
    measurement: props.product.measurement ?? '',
    descriptions: {
      ru: { title: '', short_description: '', full_description: '' },
      ro: { title: '', short_description: '', full_description: '' },
      en: { title: '', short_description: '', full_description: '' },
    },
    variants: [], // Варианты товара
  })

  // Заполняем descriptions из пропсов
  if (Array.isArray(props.product.descriptions)) {
    for (const desc of props.product.descriptions) {
      const lang = desc.language
      form.descriptions[lang] = {
        title: desc.title ?? '',
        short_description: desc.short_description ?? '',
        full_description: desc.full_description ?? '',
      }
    }
  }

  // Заполняем варианты из пропсов
  if (props.product.variantsForEdit && Array.isArray(props.product.variantsForEdit)) {
    form.variants = props.product.variantsForEdit.map((variant) => ({
      id: variant.id,
      sku: variant.sku,
      price: variant.price,
      is_default: variant.is_default,
      attributes: variant.attributes || [],
    }))
  }

  // Методы для работы с вариантами
  const addVariant = () => {
    form.variants.push({
      id: null, // null означает новый вариант
      sku: 'Будет сгенерирован',
      price: 0,
      is_default: form.variants.length === 0, // Первый вариант автоматически дефолтный
      attributes: [],
    })

    if (form.variants.length === 1) {
      defaultVariantIndex.value = 0
    }
  }

  const removeVariant = (index) => {
    if (form.variants.length <= 1) return

    const wasDefault = form.variants[index].is_default
    form.variants.splice(index, 1)

    // Если удалили дефолтный вариант, делаем дефолтным первый
    if (wasDefault && form.variants.length > 0) {
      form.variants[0].is_default = true
      defaultVariantIndex.value = 0
    }
  }

  const setDefaultVariant = (index) => {
    // Сбрасываем все дефолтные значения
    form.variants.forEach((variant) => (variant.is_default = false))
    // Устанавливаем новый дефолтный
    form.variants[index].is_default = true
    defaultVariantIndex.value = index
  }

  const addVariantAttribute = (variantIndex) => {
    // TODO: Открыть модалку выбора атрибута и значения
    alert('TODO: Добавление атрибута к варианту')
  }

  const removeVariantAttribute = (variantIndex, attrToRemove) => {
    const variant = form.variants[variantIndex]
    variant.attributes = variant.attributes.filter(
      (attr) => !(attr.attribute_id === attrToRemove.attribute_id && attr.value_id === attrToRemove.value_id)
    )
  }

  /** Модалка быстрого добавления */
  const isQuickAddOpen = ref(false)
  const quickAddIndex = ref(null)

  /** Модалка добавления атрибута к варианту */
  const isVariantAttributeModalOpen = ref(false)
  const currentVariantIndex = ref(null)
  const currentVariantAttributes = ref([])

  const openQuickAdd = (index) => {
    quickAddIndex.value = index
    isQuickAddOpen.value = true
  }

  /** Сохранение нового значения через axios */
  const handleQuickAddSave = async ({ ru, ro, en }) => {
    // TODO: Логика для старых атрибутов если нужна
    isQuickAddOpen.value = false
  }

  const submit = () => {
    console.log('Отправка формы:', form.data())

    form.post(`/admin/products/update/${form.id}`, {
      onSuccess: () => emit('productUpdated'),
      onError: () => {
        // скролл к первому полю с ошибкой
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
  }
</script>
