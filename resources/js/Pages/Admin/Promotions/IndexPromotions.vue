<script setup>
  import { ref } from 'vue'
  import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
  import { useForm, router, Link } from '@inertiajs/vue3'

  defineProps({
    promotions: Array,
    products: Array,
    discountGroups: Array,
  })

  const form = useForm({
    product_id: '',
    discount_group_id: '',
    ends_at: '',
  })

  function destroy(id) {
    if (confirm('Удалить акцию?')) {
      router.delete(route('admin.promotions.destroy', id))
    }
  }
</script>

<template>
  <Head title="Скидки" />
  <AuthenticatedLayout>
    <div class="max-w-4xl mx-auto p-6">
      <h1 class="text-2xl font-bold mb-6">Управление скачухами</h1>
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
      <form @submit.prevent="form.post(route('admin.promotions.store'))" class="space-y-4 bg-white p-4 rounded shadow mb-8">
        <div>
          <label class="block mb-1">Товар</label>
          <select v-model="form.product_id" class="w-full border rounded px-3 py-2">
            <option value="">Выберите товар</option>
            <option v-for="product in products" :key="product.id" :value="product.id">
              {{ product.description?.title || 'Без названия' }}
            </option>
          </select>
        </div>

        <div>
          <label class="block mb-1">Скидка</label>
          <select v-model="form.discount_group_id" class="w-full border rounded px-3 py-2">
            <option value="">Выберите скидку</option>
            <option v-for="group in discountGroups" :key="group.id" :value="group.id">
              {{ group.name }} ({{ group.discount_percent }}%)
            </option>
          </select>
        </div>

        <div>
          <label class="block mb-1">Дата окончания акции</label>
          <input type="date" v-model="form.ends_at" class="w-full border rounded px-3 py-2" />
        </div>

        <button type="submit" class="bg-pink-600 text-white px-4 py-2 rounded hover:bg-pink-700">Добавить в акцию</button>
      </form>

      <!-- 🔹 Список -->
      <div class="bg-white p-4 rounded shadow">
        <h2 class="text-lg font-semibold mb-4">Текущие акции</h2>

        <table class="w-full text-sm">
          <thead>
            <tr class="border-b">
              <th class="py-2 text-left">Товар</th>
              <th class="py-2 text-left">Скидка</th>
              <th class="py-2 text-left">До</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="promo in promotions" :key="promo.id" class="border-b">
              <td class="py-2">{{ promo.product.description?.title || 'Без названия' }}</td>
              <td class="py-2">{{ promo.discount_group?.name }} ({{ promo.discount_group?.discount_percent }}%)</td>
              <td class="py-2">{{ promo.ends_at?.slice(0, 10) || '—' }}</td>
              <td class="py-2 text-right">
                <button @click="destroy(promo.id)" class="text-red-600 hover:underline text-sm">Удалить</button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
