<template>
  <div v-if="isOpen" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
    <div class="bg-white rounded-lg shadow-lg w-full max-w-md p-6">
      <h3 class="text-lg font-semibold mb-4">Добавить значение атрибута</h3>

      <!-- Поля ввода -->
      <div v-for="lang in ['ru', 'ro', 'en']" :key="lang" class="mb-4">
        <label class="block text-sm font-medium mb-1">Значение ({{ lang.toUpperCase() }})</label>
        <input
          type="text"
          v-model="form[lang]"
          class="w-full p-2 border rounded border-gray-300"
          placeholder="Введите значение"
        />
      </div>

      <!-- Кнопки -->
      <div class="flex justify-end gap-2 mt-6">
        <button type="button" @click="$emit('close')" class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600">
          Отмена
        </button>
        <button type="button" @click="submit" class="px-4 py-2 bg-emerald-600 text-white rounded hover:bg-emerald-700">
          Сохранить
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
  import { reactive } from 'vue'

  const props = defineProps({
    isOpen: Boolean,
  })

  const emit = defineEmits(['close', 'save'])

  const form = reactive({
    ru: '',
    ro: '',
    en: '',
  })

  const submit = () => {
    emit('save', { ...form })
    // Очистим поля после сохранения
    form.ru = ''
    form.ro = ''
    form.en = ''
  }
</script>
