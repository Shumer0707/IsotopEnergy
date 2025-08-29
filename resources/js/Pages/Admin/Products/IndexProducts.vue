<script setup>
  import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
  import { Head, router, usePage, Link } from '@inertiajs/vue3'
  import { ref, watch } from 'vue'
  import CreateProductForm from './CreateProductForm.vue'
  import EditProductForm from './EditProductForm.vue'
  import EditProductImages from './EditProductImages.vue'

  const props = defineProps({
    products: Object, // Теперь это объект с данными пагинации
    categories: Array,
    attributes: Array,
    brands: Array,
    values: Array,
  })

  console.log(props.products.data?.map((p) => p.descriptions))

  const viewMode = ref('list')
  const productToEdit = ref(null)
  const flashMessage = ref(null)
  const selectedCategory = ref('')

  watch(selectedCategory, (newVal) => {
    router.get(
      '/admin/products',
      {
        category: newVal,
        page: 1, // Сбрасываем на первую страницу при смене категории
      },
      {
        preserveScroll: true,
        preserveState: true,
        only: ['products'],
      }
    )
  })

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
    router.reload({ only: ['products'] })
  }

  const editProduct = (product) => {
    productToEdit.value = JSON.parse(JSON.stringify(product)) // глубокая копия
    viewMode.value = 'edit'
  }

  const deleteProduct = (id) => {
    if (confirm('Удалить товар?')) {
      router.delete(`/admin/products/${id}`, {
        onSuccess: () => router.reload({ only: ['products'] }),
      })
    }
  }

  const manageImages = (product) => {
    productToEdit.value = JSON.parse(JSON.stringify(product))
    viewMode.value = 'images'
  }

  // Функция для перехода на определенную страницу
  const goToPage = (url) => {
    if (!url) return

    router.get(
      url,
      {},
      {
        preserveScroll: true,
        preserveState: true,
        only: ['products'],
      }
    )
  }
</script>

<template>
  <Head title="Товары" />
  <div v-if="flashMessage" class="fixed top-5 right-5 bg-green-500 text-white px-4 py-2 rounded shadow-lg z-50">
    {{ flashMessage }}
  </div>
  <AuthenticatedLayout>
    <template #header>
      <h2 class="text-xl font-semibold leading-tight text-gray-800">Товары</h2>
    </template>

    <div class="py-12">
      <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
        <div v-if="viewMode === 'list'">
          <div class="mb-4 flex items-center gap-4">
            <div>
              <label class="block text-sm mb-1">Фильтр по категории</label>
              <select v-model="selectedCategory" class="p-2 border rounded w-64">
                <option value="">— Все категории —</option>
                <option v-for="cat in categories" :key="cat.id" :value="cat.id">
                  {{ cat.translation?.name ?? '—' }}
                </option>
              </select>
            </div>

            <button
              v-if="selectedCategory"
              @click="selectedCategory = ''"
              class="mt-6 px-3 py-2 bg-gray-300 hover:bg-gray-400 rounded text-sm"
            >
              ❌ Сбросить фильтр
            </button>
          </div>

          <div class="flex justify-between items-center mb-4">
            <div>
              <h3 class="text-lg font-semibold">Список товаров</h3>
              <p class="text-sm text-gray-600">
                Показано {{ products.from || 0 }}-{{ products.to || 0 }} из {{ products.total || 0 }} товаров
              </p>
            </div>
            <button @click="viewMode = 'create'" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
              ➕ Добавить товар
            </button>
          </div>

          <table class="w-full border-collapse border border-gray-300 mb-4">
            <thead>
              <tr class="bg-gray-200">
                <th class="border px-4 py-2">ID</th>
                <th class="border px-4 py-2">Название (RU)</th>
                <th class="border px-4 py-2">Категория</th>
                <th class="border px-4 py-2">Цена</th>
                <th class="border px-4 py-2">Действия</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="product in products.data" :key="product.id">
                <td class="border px-4 py-2">
                  {{ product.id }}
                </td>
                <td class="border px-4 py-2">
                  {{ product.descriptions.find((t) => t.language === 'ru')?.title ?? '—' }}
                </td>
                <td class="border px-4 py-2">
                  {{ product.category?.translation?.name ?? '—' }}
                </td>
                <td class="border px-4 py-2">
                  {{ product.price }}
                </td>
                <td class="border px-4 py-2">
                  <button
                    @click="editProduct(product)"
                    class="px-2 py-1 bg-yellow-500 text-white rounded hover:bg-yellow-600 mr-1"
                  >
                    ✏ Редактировать
                  </button>
                  <button
                    @click="deleteProduct(product.id)"
                    class="px-2 py-1 bg-red-600 text-white rounded hover:bg-red-700 mr-1"
                  >
                    🗑 Удалить
                  </button>
                  <button @click="manageImages(product)" class="px-2 py-1 bg-indigo-600 text-white rounded hover:bg-indigo-700">
                    🖼 Фото
                  </button>
                </td>
              </tr>
            </tbody>
          </table>

          <!-- Пагинация -->
          <div v-if="products.last_page > 1" class="flex justify-center items-center space-x-2">
            <!-- Предыдущая страница -->
            <button
              @click="goToPage(products.prev_page_url)"
              :disabled="!products.prev_page_url"
              class="px-3 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400 disabled:opacity-50 disabled:cursor-not-allowed"
            >
              « Предыдущая
            </button>

            <!-- Номера страниц -->
            <template v-for="page in products.links" :key="page.label">
              <button
                v-if="page.url && !page.label.includes('Previous') && !page.label.includes('Next')"
                @click="goToPage(page.url)"
                :class="[
                  'px-3 py-2 rounded',
                  page.active ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300',
                ]"
                v-html="page.label"
              ></button>
              <span
                v-else-if="!page.url && !page.label.includes('Previous') && !page.label.includes('Next')"
                class="px-3 py-2 text-gray-500"
                v-html="page.label"
              ></span>
            </template>

            <!-- Следующая страница -->
            <button
              @click="goToPage(products.next_page_url)"
              :disabled="!products.next_page_url"
              class="px-3 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400 disabled:opacity-50 disabled:cursor-not-allowed"
            >
              Следующая »
            </button>
          </div>

          <!-- Информация о пагинации -->
          <div v-if="products.last_page > 1" class="text-center text-sm text-gray-600 mt-4">
            Страница {{ products.current_page }} из {{ products.last_page }}
          </div>
        </div>

        <CreateProductForm
          v-if="viewMode === 'create'"
          :categories="categories"
          :attributes="attributes"
          :values="values"
          :brands="brands"
          @productAdded="refreshList"
          @cancel="viewMode = 'list'"
        />
        <EditProductForm
          v-if="viewMode === 'edit'"
          :product="productToEdit"
          :categories="categories"
          :attributes="attributes"
          :values="values"
          :brands="brands"
          @productUpdated="refreshList"
          @cancel="viewMode = 'list'"
        />
        <EditProductImages v-if="viewMode === 'images'" :product="productToEdit" @cancel="viewMode = 'list'" />
      </div>
    </div>
  </AuthenticatedLayout>
</template>
