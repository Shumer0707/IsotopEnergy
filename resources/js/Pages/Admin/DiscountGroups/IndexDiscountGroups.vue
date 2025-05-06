<script setup>
  import { ref } from 'vue'
  import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
  import { useForm, router, Link } from '@inertiajs/vue3'

  defineProps({
    discountGroups: Array,
  })

  const form = useForm({
    name: '',
    discount_percent: '',
  })

  function destroy(id) {
    if (confirm('Удалить эту группу?')) {
      router.delete(route('admin.discount-groups.destroy', id))
    }
  }
</script>

<template>
  <Head title="Групы скидок" />
  <AuthenticatedLayout>
    <div class="max-w-3xl mx-auto p-6">
      <h1 class="text-2xl font-bold mb-6">Группы скидок</h1>
      <nav class="flex gap-4 mb-6">
        <Link
          href="/admin/promotions"
          class="flex items-center justify-center px-4 my-2 bg-pink-600 text-white rounded-lg hover:bg-pink-700"
        >
          Скидки
        </Link>
        <Link
          href="/admin/discount-groups"
          class="flex items-center justify-center px-4 my-2 bg-pink-600 text-white rounded-lg hover:bg-pink-700"
        >
          Группы скидок
        </Link>
      </nav>
      <!-- 🔹 Добавление -->
      <form @submit.prevent="form.post(route('admin.discount-groups.store'))" class="bg-white p-4 rounded shadow space-y-4 mb-8">
        <div>
          <label class="block mb-1">Название</label>
          <input v-model="form.name" type="text" class="w-full border rounded px-3 py-2" placeholder="Например, Скидка 10%" />
        </div>

        <div>
          <label class="block mb-1">Процент</label>
          <input
            v-model="form.discount_percent"
            type="number"
            min="1"
            max="100"
            class="w-full border rounded px-3 py-2"
            placeholder="10"
          />
        </div>

        <button type="submit" class="bg-pink-600 text-white px-4 py-2 rounded hover:bg-pink-700">Добавить группу</button>
      </form>

      <!-- 🔹 Список -->
      <div class="bg-white p-4 rounded shadow">
        <h2 class="text-lg font-semibold mb-4">Существующие группы</h2>
        <table class="w-full text-sm">
          <thead>
            <tr class="border-b">
              <th class="py-2 text-left">Название</th>
              <th class="py-2 text-left">%</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="group in discountGroups" :key="group.id" class="border-b">
              <td class="py-2">{{ group.name }}</td>
              <td class="py-2">{{ group.discount_percent }}%</td>
              <td class="py-2 text-right">
                <button @click="destroy(group.id)" class="text-red-600 hover:underline text-sm">Удалить</button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
