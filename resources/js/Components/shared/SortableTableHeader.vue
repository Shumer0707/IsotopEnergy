<!-- Components/shared/SortableTableHeader.vue -->
<template>
  <th :class="thClass">
    <button
      v-if="sortable"
      @click="handleSort"
      :class="[
        'flex items-center justify-center gap-1 w-full font-medium transition-colors',
        'hover:text-blue-600 focus:outline-none focus:text-blue-600',
        isActive ? 'text-blue-700' : 'text-gray-700',
      ]"
      :title="`Сортировать по ${label}`"
    >
      {{ label }}

      <!-- Стрелки сортировки -->
      <span class="text-sm">
        <span v-if="isActive">
          <!-- Активная стрелка -->
          <span v-if="currentDirection === 'asc'" class="text-blue-600">▲</span>
          <span v-else class="text-blue-600">▼</span>
        </span>
        <span v-else class="text-gray-400">
          <!-- Неактивные стрелки (показываем при ховере) -->
          <span class="opacity-0 group-hover:opacity-50 transition-opacity">⇅</span>
        </span>
      </span>
    </button>

    <!-- Если не сортируемая колонка -->
    <span v-else class="font-medium text-gray-700">
      {{ label }}
    </span>
  </th>
</template>

<script setup>
  import { computed } from 'vue'

  const props = defineProps({
    // Текст заголовка
    label: {
      type: String,
      required: true,
    },

    // Поле для сортировки
    field: {
      type: String,
      required: false,
    },

    // Можно ли сортировать эту колонку
    sortable: {
      type: Boolean,
      default: true,
    },

    // Текущее поле сортировки
    currentField: {
      type: String,
      default: '',
    },

    // Текущее направление сортировки
    currentDirection: {
      type: String,
      default: 'asc',
      validator: (value) => ['asc', 'desc'].includes(value),
    },

    // Дополнительные CSS классы для th
    thClass: {
      type: String,
      default: 'border px-4 py-2 bg-gray-50',
    },
  })

  const emit = defineEmits(['sort'])

  // Проверяем, активна ли эта колонка для сортировки
  const isActive = computed(() => {
    return props.sortable && props.field === props.currentField
  })

  // Обработчик клика по заголовку
  const handleSort = () => {
    if (!props.sortable || !props.field) return

    let newDirection = 'asc'

    // Если кликнули на уже активную колонку - меняем направление
    if (isActive.value) {
      newDirection = props.currentDirection === 'asc' ? 'desc' : 'asc'
    }

    // Отправляем событие наверх
    emit('sort', {
      field: props.field,
      direction: newDirection,
    })
  }
</script>

<style scoped>
  /* Дополнительные стили для ховер-эффекта */
  .group:hover .opacity-0 {
    opacity: 0.5;
  }

  button:hover .text-gray-400 {
    color: #6b7280;
  }
</style>
