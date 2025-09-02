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

      <!-- Цена / Скидка -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
        <div>
          <label class="block">Цена</label>
          <input
            type="number"
            step="0.01"
            v-model="form.price"
            data-error
            :class="[
              'w-full p-2 border rounded mb-1',
              form.errors.price ? 'border-red-500 ring-1 ring-red-500' : 'border-gray-300',
            ]"
          />
          <p v-if="form.errors.price" class="mt-1 text-sm text-red-600">{{ form.errors.price }}</p>
        </div>
      </div>

      <!-- Измерение -->
      <div class="mt-6">
        <label class="block">Единицы измерения</label>
        <input
          v-model="form.measurement"
          data-error
          :class="[
            'w-full p-2 border rounded mb-1 max-w-xs',
            form.errors.measurement ? 'border-red-500 ring-1 ring-red-500' : 'border-gray-300',
          ]"
        />
      </div>

      <!-- Атрибуты -->
      <div class="mt-8">
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

          <!-- Значение + быстрый ввод -->
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
            form.processing ? 'bg-yellow-400 cursor-not-allowed' : 'bg-yellow-600 hover:bg-yellow-700',
          ]"
        >
          Обновить
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

  const form = useForm({
    id: props.product.id,
    category_id: props.product.category_id ?? '',
    brand_id: props.product.brand_id ?? '',
    price: props.product.price ?? '',
    discount_price: props.product.discount_price ?? '',
    currency: props.product.currency ?? 'MDL',
    measurement: props.product.measurement,
    descriptions: {
      ru: { title: '', short_description: '', full_description: '' },
      ro: { title: '', short_description: '', full_description: '' },
      en: { title: '', short_description: '', full_description: '' },
    },
    attributes: [],
  })

  // descriptions из пропсов
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

  // attributes из пропсов
  form.attributes =
    props.product.attributeValues?.map((attr) => ({
      attribute_id: attr.attribute_id,
      value_id: attr.attribute_value_id,
    })) ?? []

  const addAttribute = () => form.attributes.push({ attribute_id: '', value_id: '' })
  const removeAttribute = (index) => form.attributes.splice(index, 1)

  /** Локальный список значений (инициализируем из пропсов, чтобы можно было дополнять) */
  const allValues = ref([...(props.values ?? [])])
  const filteredValues = (attrId) => allValues.value.filter((v) => v.attribute_id === attrId)

  /** Модалка быстрого добавления */
  const isQuickAddOpen = ref(false)
  const quickAddIndex = ref(null)

  const openQuickAdd = (index) => {
    quickAddIndex.value = index
    isQuickAddOpen.value = true
  }

  /** Сохранение нового значения через axios */
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

      // подмешиваем и сразу выбираем
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

  const submit = () => {
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
