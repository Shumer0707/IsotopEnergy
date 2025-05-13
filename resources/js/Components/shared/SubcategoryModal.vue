<script setup>
  import { ref, onMounted, onBeforeUnmount } from 'vue'
  import { Link } from '@inertiajs/vue3'

  defineProps({
    category: Object,
  })

  const emit = defineEmits(['close'])

  const modalRef = ref(null)

  const handleClickOutside = (event) => {
    if (modalRef.value && !modalRef.value.contains(event.target)) {
      emit('close')
    }
  }

  onMounted(() => {
    document.addEventListener('mousedown', handleClickOutside)
  })
  onBeforeUnmount(() => {
    document.removeEventListener('mousedown', handleClickOutside)
  })
</script>

<template>
  <transition
    name="fade"
    enter-active-class="transition-opacity duration-300"
    leave-active-class="transition-opacity duration-200"
    enter-from-class="opacity-0"
    leave-to-class="opacity-0"
    enter-to-class="opacity-100"
    leave-from-class="opacity-100"
  >
    <div v-if="category" class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center z-50">
      <div ref="modalRef" class="bg-white rounded-xl p-6 w-full max-w-4xl shadow-lg relative">
        <button @click="$emit('close')" class="absolute top-2 right-2 text-gray-500 hover:text-black text-xl">&times;</button>

        <h2 class="text-xl font-semibold mb-4 text-center">
          {{ category.name }}
        </h2>

        <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-6 gap-4">
          <Link
            v-for="sub in category.children"
            :key="sub.id"
            :href="`/category/${sub.id}`"
            class="flex flex-col items-center text-center hover:bg-gray-100 p-2 rounded"
            @click="$emit('close')"
          >
            <img src="/images/placeholder.jpg" alt="Subcategory" class="w-16 h-16 object-contain bg-white rounded-md" />
            <span class="text-sm mt-2">
              {{ sub.name }}
            </span>
          </Link>
        </div>
      </div>
    </div>
  </transition>
</template>
