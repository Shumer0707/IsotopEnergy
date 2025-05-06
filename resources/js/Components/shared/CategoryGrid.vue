<script setup>
  import { ref, onMounted } from 'vue'
  import axios from 'axios'
  import SubcategoryModal from './SubcategoryModal.vue'

  const categories = ref([])
  const activeCategory = ref(null)

  const fetchCategories = async () => {
    const res = await axios.get('/layout-data')
    categories.value = res.data.navCategories
  }

  const openModal = (category) => {
    activeCategory.value = {
      id: category.id,
      name: category.translation?.name ?? 'Без названия',
      children: (category.children || []).map((sub) => ({
        id: sub.id,
        name: sub.translation?.name ?? 'Без названия',
      })),
    }
  }

  onMounted(fetchCategories)
</script>

<template>
  <div>
    <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-8 lg:grid-cols-10 gap-4">
      <div
        v-for="category in categories"
        :key="category.id"
        class="flex flex-col items-center cursor-pointer"
        @click="openModal(category)"
      >
        <img
          :src="category.logo ? `/storage/${category.logo}` : '/images/placeholder.jpg'"
          alt="logo"
          class="w-20 h-20 object-contain rounded-md bg-white"
        />
        <span class="text-md mt-2 text-center">
          {{ category.translation?.name ?? 'Без названия' }}
        </span>
      </div>
    </div>

    <SubcategoryModal :category="activeCategory" @close="activeCategory = null" />
  </div>
</template>
