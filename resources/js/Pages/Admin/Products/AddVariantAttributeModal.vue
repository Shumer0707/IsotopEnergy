<template>
  <!-- Модальное окно -->
  <div
    v-if="isOpen"
    class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
    @click.self="close"
  >
    <div class="bg-white rounded-lg p-6 w-full max-w-md mx-4">
      <h3 class="text-lg font-semibold mb-4">Добавить атрибут к варианту</h3>

      <div class="space-y-4">
        <!-- Выбор атрибута -->
        <div>
          <label class="block text-sm font-medium mb-2">Атрибут</label>
          <select
            v-model="selectedAttributeId"
            @change="onAttributeChange"
            :class="[
              'w-full p-2 border rounded',
              errors.attribute_id ? 'border-red-500 ring-1 ring-red-500' : 'border-gray-300',
            ]"
          >
            <option value="">Выберите атрибут...</option>
            <option v-for="attr in availableAttributes" :key="attr.id" :value="attr.id">
              {{ attr.translated_name }}
            </option>
          </select>
          <p v-if="errors.attribute_id" class="mt-1 text-sm text-red-600">{{ errors.attribute_id }}</p>
        </div>

        <!-- Выбор значения атрибута -->
        <div v-if="selectedAttributeId">
          <label class="block text-sm font-medium mb-2">Значение</label>
          <select
            v-model="selectedValueId"
            :class="[
              'w-full p-2 border rounded',
              errors.value_id ? 'border-red-500 ring-1 ring-red-500' : 'border-gray-300',
            ]"
          >
            <option value="">Выберите значение...</option>
            <option v-for="value in filteredValues" :key="value.id" :value="value.id">
              {{ value.translated_value }}
            </option>
          </select>
          <p v-if="errors.value_id" class="mt-1 text-sm text-red-600">{{ errors.value_id }}</p>
        </div>

        <!-- Предпросмотр -->
        <div v-if="selectedAttributeId && selectedValueId" class="p-3 bg-gray-50 rounded">
          <p class="text-sm text-gray-600">Будет добавлено:</p>
          <p class="font-medium">
            {{ getAttributeName(selectedAttributeId) }}: {{ getValueName(selectedValueId) }}
          </p>
        </div>
      </div>

      <!-- Кнопки -->
      <div class="flex justify-end gap-2 mt-6">
        <button
          type="button"
          @click="close"
          class="px-4 py-2 text-gray-600 border border-gray-300 rounded hover:bg-gray-50"
        >
          Отмена
        </button>
        <button
          type="button"
          @click="save"
          :disabled="!canSave"
          class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed"
        >
          Добавить
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'

const emit = defineEmits(['close', 'save'])

const props = defineProps({
  isOpen: {
    type: Boolean,
    default: false
  },
  // Все доступные атрибуты
  attributes: {
    type: Array,
    default: () => []
  },
  // Все доступные значения атрибутов
  values: {
    type: Array,
    default: () => []
  },
  // Атрибуты уже добавленные к этому варианту (чтобы исключить дубли)
  existingAttributes: {
    type: Array,
    default: () => []
  }
})

const selectedAttributeId = ref('')
const selectedValueId = ref('')
const errors = ref({})

// Доступные атрибуты (исключаем уже добавленные)
const availableAttributes = computed(() => {
  const existingAttributeIds = props.existingAttributes.map(attr => attr.attribute_id)
  return props.attributes.filter(attr => !existingAttributeIds.includes(attr.id))
})

// Значения для выбранного атрибута
const filteredValues = computed(() => {
  if (!selectedAttributeId.value) return []
  return props.values.filter(value => value.attribute_id == selectedAttributeId.value)
})

// Можно ли сохранить
const canSave = computed(() => {
  return selectedAttributeId.value && selectedValueId.value
})

// Получить название атрибута
const getAttributeName = (attributeId) => {
  const attr = props.attributes.find(a => a.id == attributeId)
  return attr?.translated_name || '?'
}

// Получить название значения
const getValueName = (valueId) => {
  const value = props.values.find(v => v.id == valueId)
  return value?.translated_value || '?'
}

// При изменении атрибута - сбрасываем значение
const onAttributeChange = () => {
  selectedValueId.value = ''
  errors.value = {}
}

// Закрыть модалку
const close = () => {
  selectedAttributeId.value = ''
  selectedValueId.value = ''
  errors.value = {}
  emit('close')
}

// Сохранить выбранный атрибут
const save = () => {
  errors.value = {}

  // Валидация
  if (!selectedAttributeId.value) {
    errors.value.attribute_id = 'Выберите атрибут'
    return
  }

  if (!selectedValueId.value) {
    errors.value.value_id = 'Выберите значение'
    return
  }

  // Проверяем что такой атрибут еще не добавлен
  const existingAttr = props.existingAttributes.find(attr =>
    attr.attribute_id == selectedAttributeId.value
  )

  if (existingAttr) {
    errors.value.attribute_id = 'Этот атрибут уже добавлен к варианту'
    return
  }

  // Отправляем данные родителю
  emit('save', {
    attribute_id: parseInt(selectedAttributeId.value),
    attribute_name: getAttributeName(selectedAttributeId.value),
    value_id: parseInt(selectedValueId.value),
    value_name: getValueName(selectedValueId.value)
  })

  close()
}
</script>
