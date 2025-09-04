<!-- SearchableSelect.vue -->
<template>
  <div class="relative" ref="containerRef">
    <!-- Поле ввода -->
    <div
      :class="[
        'w-full p-2 border rounded cursor-pointer flex items-center justify-between',
        hasError ? 'border-red-500 ring-1 ring-red-500' : 'border-gray-300',
        isOpen ? 'border-blue-500' : '',
      ]"
      @click="toggleDropdown"
    >
      <span v-if="selectedOption" class="text-gray-900">
        {{ selectedOption.translated_value }}
      </span>
      <span v-else class="text-gray-500">
        {{ placeholder }}
      </span>

      <!-- Стрелка -->
      <svg
        :class="['w-4 h-4 text-gray-500 transition-transform', isOpen ? 'rotate-180' : '']"
        fill="none"
        stroke="currentColor"
        viewBox="0 0 24 24"
      >
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
      </svg>
    </div>

    <!-- Выпадающий список -->
    <div
      v-if="isOpen"
      class="absolute top-full left-0 right-0 z-50 mt-1 bg-white border border-gray-300 rounded shadow-lg max-h-60 overflow-hidden"
    >
      <!-- Поле поиска -->
      <div class="p-2 border-b">
        <input
          ref="searchRef"
          v-model="searchQuery"
          type="text"
          placeholder="Поиск..."
          class="w-full p-2 text-sm border border-gray-300 rounded focus:outline-none focus:border-blue-500"
          @click.stop
        />
      </div>

      <!-- Список опций -->
      <div class="overflow-y-auto max-h-48">
        <div v-if="filteredOptions.length === 0" class="p-3 text-sm text-gray-500 text-center">Ничего не найдено</div>

        <div
          v-for="option in filteredOptions"
          :key="option.id"
          :class="[
            'p-2 cursor-pointer hover:bg-gray-100 text-sm',
            selectedValue === option.id ? 'bg-blue-100 text-blue-900' : 'text-gray-900',
          ]"
          @click="selectOption(option)"
        >
          {{ option.translated_value }}
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
  import { ref, computed, watch, nextTick } from 'vue'
  import { useClickOutside } from '@/composables/useClickOutside'

  const props = defineProps({
    modelValue: [String, Number],
    options: {
      type: Array,
      default: () => [],
    },
    placeholder: {
      type: String,
      default: 'Выберите значение',
    },
    hasError: {
      type: Boolean,
      default: false,
    },
  })

  const emit = defineEmits(['update:modelValue'])

  const isOpen = ref(false)
  const searchQuery = ref('')
  const containerRef = ref(null)
  const searchRef = ref(null)
  const selectedValue = ref(props.modelValue)

  // Находим выбранную опцию
  const selectedOption = computed(() => {
    return props.options.find((option) => option.id === selectedValue.value)
  })

  // Фильтруем опции по поисковому запросу
  const filteredOptions = computed(() => {
    if (!searchQuery.value.trim()) {
      return sortedOptions.value
    }

    const query = searchQuery.value.toLowerCase()
    return sortedOptions.value.filter((option) => option.translated_value?.toLowerCase().includes(query))
  })

  // Сортируем опции по алфавиту
  const sortedOptions = computed(() => {
    return [...props.options].sort((a, b) => {
      const nameA = a.translated_value?.toLowerCase() || ''
      const nameB = b.translated_value?.toLowerCase() || ''
      return nameA.localeCompare(nameB, 'ru')
    })
  })

  // Открытие/закрытие выпадающего списка
  const toggleDropdown = async () => {
    isOpen.value = !isOpen.value
    searchQuery.value = ''

    if (isOpen.value) {
      await nextTick()
      searchRef.value?.focus()
    }
  }

  // Выбор опции
  const selectOption = (option) => {
    selectedValue.value = option.id
    emit('update:modelValue', option.id)
    isOpen.value = false
    searchQuery.value = ''
  }

  // Закрытие при клике вне компонента
  useClickOutside(containerRef, () => {
    isOpen.value = false
    searchQuery.value = ''
  })

  // Синхронизация с внешним значением
  watch(
    () => props.modelValue,
    (newValue) => {
      selectedValue.value = newValue
    }
  )
</script>
