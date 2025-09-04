<script setup>
  import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
  import { Head, router, usePage } from '@inertiajs/vue3'
  import { ref, watch, computed } from 'vue'
  import CreateAttributeValueForm from './CreateAttributeValueForm.vue'
  import EditAttributeValueForm from './EditAttributeValueForm.vue'
  import SortableTableHeader from '@/Components/shared/SortableTableHeader.vue'

  const props = defineProps({
    values: Array,
    attributes: Array,
    activeAttribute: [String, Number, null],
  })

  const viewMode = ref('list')
  const valueToEdit = ref(null)
  const flashMessage = ref(null)
  const selectedAttributeId = ref(props.activeAttribute || '')
  const sortField = ref('id')
  const sortDirection = ref('asc')

  const filterByAttribute = () => {
    router.visit('/admin/attribute-values', {
      method: 'get',
      data: {
        attribute: selectedAttributeId.value || null,
      },
      preserveScroll: true,
      preserveState: true,
      only: ['values', 'activeAttribute'],
    })
  }

  const resetFilter = () => {
    selectedAttributeId.value = ''
    filterByAttribute()
  }

  watch(
    () => usePage().props.flash?.success,
    (newVal) => {
      if (newVal) {
        flashMessage.value = newVal
        setTimeout(() => (flashMessage.value = null), 3000)
      }
    },
    { immediate: true }
  )

  const refreshList = () => {
    viewMode.value = 'list'
    router.reload({ only: ['values'] })
  }

  const editValue = (val) => {
    valueToEdit.value = { ...val }
    viewMode.value = 'edit'
  }

  const deleteValue = (id) => {
    if (confirm('Удалить значение?')) {
      router.delete(`/admin/attribute-values/${id}`, {
        onSuccess: () => {
          flashMessage.value = usePage().props.flash?.success
          router.visit('/admin/attribute-values', {
            only: ['values'],
            preserveScroll: true,
          })
        },
      })
    }
  }
  const handleSort = ({ field, direction }) => {
    sortField.value = field
    sortDirection.value = direction
  }

  const sortedValues = computed(() => {
    const valuesCopy = [...props.values]

    return valuesCopy.sort((a, b) => {
      let valueA, valueB

      switch (sortField.value) {
        case 'id':
          valueA = a.id
          valueB = b.id
          break
        case 'ru':
          valueA = a.translations.find((t) => t.language === 'ru')?.value || ''
          valueB = b.translations.find((t) => t.language === 'ru')?.value || ''
          break
        case 'ro':
          valueA = a.translations.find((t) => t.language === 'ro')?.value || ''
          valueB = b.translations.find((t) => t.language === 'ro')?.value || ''
          break
        case 'en':
          valueA = a.translations.find((t) => t.language === 'en')?.value || ''
          valueB = b.translations.find((t) => t.language === 'en')?.value || ''
          break
        case 'attribute':
          valueA = a.attribute?.translation?.name || ''
          valueB = b.attribute?.translation?.name || ''
          break
        default:
          return 0
      }

      let comparison = 0
      if (typeof valueA === 'string' && typeof valueB === 'string') {
        comparison = valueA.localeCompare(valueB, 'ru')
      } else {
        comparison = valueA < valueB ? -1 : valueA > valueB ? 1 : 0
      }

      return sortDirection.value === 'asc' ? comparison : -comparison
    })
  })
</script>

<template>
  <Head title="Значения атрибутов" />
  <div v-if="flashMessage" class="fixed top-5 right-5 bg-green-500 text-white px-4 py-2 rounded shadow-lg z-50">
    {{ flashMessage }}
  </div>
  <AuthenticatedLayout>
    <template #header>
      <h2 class="text-xl font-semibold leading-tight text-gray-800">Управление значениями атрибутов</h2>
    </template>

    <div class="py-12">
      <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
        <div class="bg-white p-6 shadow-sm sm:rounded-lg">
          <div v-if="viewMode === 'list'">
            <div class="flex justify-between items-center mb-4">
              <h3 class="text-lg font-semibold">Список значений</h3>
              <button @click="viewMode = 'create'" class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">
                Добавить значение
              </button>
            </div>
            <div class="mb-4 flex items-center space-x-2">
              <label for="filter" class="font-medium">Фильтр по атрибуту:</label>
              <select id="filter" v-model="selectedAttributeId" @change="filterByAttribute" class="py-2 pr-8 pl-4 border rounded">
                <option value="">Показать все</option>
                <option v-for="attr in attributes" :key="attr.id" :value="attr.id">
                  {{ attr.translation.name }}
                </option>
              </select>
              <button
                v-if="selectedAttributeId"
                @click="resetFilter"
                class="px-3 py-2 bg-gray-300 text-gray-800 rounded hover:bg-gray-400"
              >
                ✖ Сбросить
              </button>
            </div>
            <table class="w-full border-collapse border border-gray-300">
              <thead>
                <tr class="bg-gray-200">
                  <SortableTableHeader
                    label="ID"
                    field="id"
                    :current-field="sortField"
                    :current-direction="sortDirection"
                    @sort="handleSort"
                  />
                  <SortableTableHeader
                    label="RU"
                    field="ru"
                    :current-field="sortField"
                    :current-direction="sortDirection"
                    @sort="handleSort"
                  />
                  <SortableTableHeader
                    label="RO"
                    field="ro"
                    :current-field="sortField"
                    :current-direction="sortDirection"
                    @sort="handleSort"
                  />
                  <SortableTableHeader
                    label="EN"
                    field="en"
                    :current-field="sortField"
                    :current-direction="sortDirection"
                    @sort="handleSort"
                  />
                  <SortableTableHeader
                    label="Атрибут"
                    field="attribute"
                    :current-field="sortField"
                    :current-direction="sortDirection"
                    @sort="handleSort"
                  />
                  <SortableTableHeader label="Действия" :sortable="false" />
                </tr>
              </thead>
              <tbody>
                <tr v-for="value in sortedValues" :key="value.id">
                  <td class="border px-4 py-2">
                    {{ value.id }}
                  </td>
                  <td class="border px-4 py-2">
                    {{ value.translations.find((t) => t.language === 'ru')?.value ?? '—' }}
                  </td>
                  <td class="border px-4 py-2">
                    {{ value.translations.find((t) => t.language === 'ro')?.value ?? '—' }}
                  </td>
                  <td class="border px-4 py-2">
                    {{ value.translations.find((t) => t.language === 'en')?.value ?? '—' }}
                  </td>
                  <td class="border px-4 py-2">
                    {{ value.attribute?.translation?.name ?? '—' }}
                  </td>
                  <td class="border px-4 py-2 flex space-x-2">
                    <button @click="editValue(value)" class="px-2 py-1 bg-yellow-500 text-white rounded hover:bg-yellow-600">
                      ✏ Редактировать
                    </button>
                    <button @click="deleteValue(value.id)" class="px-2 py-1 bg-red-600 text-white rounded hover:bg-red-700">
                      🗑 Удалить
                    </button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <CreateAttributeValueForm
            v-if="viewMode === 'create'"
            :attributes="attributes"
            @valueAdded="refreshList"
            @cancel="viewMode = 'list'"
          />
          <EditAttributeValueForm
            v-if="viewMode === 'edit'"
            :valueItem="valueToEdit"
            :attributes="attributes"
            @valueUpdated="refreshList"
            @cancel="viewMode = 'list'"
          />
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
