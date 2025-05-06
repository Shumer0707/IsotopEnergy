<script setup>
  import { useForm } from '@inertiajs/vue3'

  const props = defineProps({
    category: Object,
    parentCategories: Array,
  })

  const emit = defineEmits(['categoryUpdated', 'cancel'])

  const form = useForm({
    id: props.category.id,
    parent_id: props.category.parent_id,
    translations: {
      ru: props.category.translations.find((t) => t.language === 'ru')?.name ?? '',
      ro: props.category.translations.find((t) => t.language === 'ro')?.name ?? '',
      en: props.category.translations.find((t) => t.language === 'en')?.name ?? '',
    },
    logo: null,
  })

  const handleFile = (e) => {
    form.logo = e.target.files[0]
  }

  const updateCategory = () => {
    form.post(`/admin/categories/update/${form.id}`, {
      forceFormData: true,
      onSuccess: () => {
        emit('categoryUpdated')
      },
    })
  }
</script>

<template>
  <div class="p-4 bg-gray-100 rounded">
    <h3 class="text-lg font-semibold mb-4">Редактировать категорию</h3>
    <form @submit.prevent="updateCategory" enctype="multipart/form-data">
      <label class="block">Название (RU)</label>
      <input v-model="form.translations.ru" class="w-full p-2 border rounded mb-2" />

      <label class="block">Название (RO)</label>
      <input v-model="form.translations.ro" class="w-full p-2 border rounded mb-2" />

      <label class="block">Название (EN)</label>
      <input v-model="form.translations.en" class="w-full p-2 border rounded mb-2" />

      <label class="block">Родительская категория</label>
      <select v-model="form.parent_id" class="w-full p-2 border rounded mb-2">
        <option :value="null">Без родителя</option>
        <option v-for="cat in parentCategories" :key="cat.id" :value="cat.id" :disabled="cat.id === form.id">
          {{ cat.translation?.name ?? '—' }}
        </option>
      </select>

      <label class="block">Новый логотип (необязательно)</label>
      <input type="file" @change="handleFile" class="w-full p-2 border rounded mb-4" />

      <div class="flex space-x-2 mt-4">
        <button type="submit" class="px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700">Обновить</button>
        <button type="button" @click="$emit('cancel')" class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600">
          Отмена
        </button>
      </div>
    </form>
  </div>
</template>
