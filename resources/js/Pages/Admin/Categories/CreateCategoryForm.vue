<script setup>
  import { useForm } from '@inertiajs/vue3'

  const props = defineProps({
    parentCategories: Array,
  })

  const emit = defineEmits(['categoryAdded', 'cancel'])

  const form = useForm({
    translations: {
      ru: '',
      ro: '',
      en: '',
    },
    parent_id: null,
    logo: null,
  })

  const handleFile = (e) => {
    form.logo = e.target.files[0]
  }

  const submitCategory = () => {
    form.post('/admin/categories/store', {
      forceFormData: true,
      onSuccess: () => {
        form.reset()
        emit('categoryAdded')
      },
    })
  }
</script>

<template>
  <div class="p-4 bg-gray-100 rounded">
    <h3 class="text-lg font-semibold mb-4">Добавить категорию</h3>
    <form @submit.prevent="submitCategory" enctype="multipart/form-data">
      <label class="block">Название (RU)</label>
      <input v-model="form.translations.ru" class="w-full p-2 border rounded mb-2" />

      <label class="block">Название (RO)</label>
      <input v-model="form.translations.ro" class="w-full p-2 border rounded mb-2" />

      <label class="block">Название (EN)</label>
      <input v-model="form.translations.en" class="w-full p-2 border rounded mb-2" />

      <label class="block">Родительская категория</label>
      <select v-model="form.parent_id" class="w-full p-2 border rounded mb-2">
        <option :value="null">Без родителя</option>
        <option v-for="cat in parentCategories" :key="cat.id" :value="cat.id">
          {{ cat.translation?.name ?? '—' }}
        </option>
      </select>

      <label class="block">Логотип</label>
      <input type="file" @change="handleFile" class="w-full p-2 border rounded mb-4" />

      <div class="flex space-x-2">
        <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">Сохранить</button>
        <button type="button" @click="$emit('cancel')" class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600">
          Отмена
        </button>
      </div>
    </form>
  </div>
</template>
