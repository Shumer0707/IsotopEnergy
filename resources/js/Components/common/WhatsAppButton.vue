<template>
  <div class="fixed bottom-4 right-4 z-50">
    <!-- Основная кнопка -->
    <button
      ref="buttonRef"
      @click="isOpen = !isOpen"
      class="bg-green-500 hover:bg-green-600 text-white w-14 h-14 rounded-full shadow-lg flex items-center justify-center"
    >
      <i class="fa-brands fa-whatsapp text-3xl"></i>
    </button>

    <!-- Раскрывающееся меню -->
    <div
      ref="menuRef"
      v-if="isOpen"
      class="absolute right-0 mt-2 w-56 bg-white rounded-xl shadow-xl border border-gray-200 overflow-hidden sm:relative sm:bottom-auto sm:mt-2 fixed bottom-20 right-4 z-50 sm:z-auto sm:w-56 md:animate-fade-in"
    >
      <a
        href="https://wa.me/+37360838688"
        target="_blank"
        class="block px-4 py-3 text-gray-800 hover:bg-green-50 flex items-center gap-2"
      >
        <i class="fa-brands fa-whatsapp text-green-500 text-lg"></i>
        {{ t['whatsapp_write'] }}
      </a>
      <button @click="showForm = true" class="w-full text-left px-4 py-3 text-gray-800 hover:bg-gray-100 flex items-center gap-2">
        <i class="fa-solid fa-phone text-blue-500 text-sm"></i>
        {{ t['whatsapp_order'] }}
      </button>
    </div>

    <!-- Модалка формы -->
    <div v-if="showForm" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50" @click.self="closeForm">
      <div class="bg-white p-6 rounded shadow-md w-96">
        <h2 class="text-lg font-semibold mb-4">{{ t['whatsapp_leave'] }}</h2>
        <form @submit.prevent="submitForm">
          <input v-model="form.name" type="text" :placeholder=t.whatsapp_name class="w-full mb-1 border border-gray-300 p-2 rounded" />
          <p v-if="errors.name" class="text-sm text-red-500 mb-2">
            {{ errors.name }}
          </p>

          <input
            ref="phoneInput"
            v-model="form.phone"
            type="text"
            placeholder="+373 (__) ___-___"
            class="w-full mb-1 border border-gray-300 p-2 rounded"
          />
          <p v-if="errors.phone" class="text-sm text-red-500 mb-2">
            {{ errors.phone }}
          </p>
          <div class="flex justify-end space-x-2">
            <button type="button" @click="showForm = false" class="px-4 py-2 bg-gray-300 rounded">{{ t['whatsapp_back'] }}</button>
            <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">{{ t['whatsapp_send'] }}</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
  import { ref, onMounted, watch, onUnmounted, nextTick } from 'vue'
  import IMask from 'imask'
  import { useClickOutside } from '@/composables/useClickOutside'
  import { useTranslations } from '@/composables/useTranslations'

  const t = useTranslations()
  const phoneInput = ref(null)
  const isOpen = ref(false)
  const showForm = ref(false)
  const menuRef = ref(null)
  const buttonRef = ref(null)

  useClickOutside(
    menuRef,
    () => {
      isOpen.value = false
    },
    buttonRef
  )

  const errors = ref({
    name: '',
    phone: '',
  })

  const form = ref({
    name: '',
    phone: '',
  })

  const isSubmitting = ref(false)

  watch(showForm, async (val) => {
    if (val) {
      await nextTick()
      if (phoneInput.value) {
        IMask(phoneInput.value, {
          mask: '+{373} (00) 000-000',
          lazy: false,
          placeholderChar: '_',
        })
      }
    }
  })

  onMounted(() => {
    document.addEventListener('keydown', handleKeydown)
  })
  onUnmounted(() => {
    document.removeEventListener('keydown', handleKeydown)
  })

  function handleKeydown(e) {
    if (e.key === 'Escape') {
      closeForm()
    }
  }

  function closeForm() {
    showForm.value = false
    isOpen.value = false
    form.value = { name: '', phone: '' }
    errors.value = { name: '', phone: '' }
  }

  function submitForm() {
    const name = form.value.name?.trim() || ''
    const phone = form.value.phone?.trim() || ''

    errors.value = { name: '', phone: '' }

    if (!name) errors.value.name = 'Введите имя'
    if (!phone) errors.value.phone = 'Введите телефон'

    if (errors.value.name || errors.value.phone) return

    isSubmitting.value = true

    const message = `Привет! Меня зовут ${name}, мой номер: ${phone}. Позвоните мне, пожалуйста.`
    const url = `https://wa.me/+37360838688?text=${encodeURIComponent(message)}`
    window.open(url, '_blank')

    setTimeout(() => {
      isSubmitting.value = false
      closeForm()
    }, 3000)
  }
</script>
