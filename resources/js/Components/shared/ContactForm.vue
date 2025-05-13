<template>
    <h2 class="text-2xl font-bold text-center mb-2">Свяжитесь с нами</h2>
    <p class="text-center mb-8">
      Напишите или позвоните нам, задайте все интересующие вас вопросы, а мы с удовольствием ответим на них в кратчайшие сроки!
    </p>

    <form @submit.prevent="submit" class="grid grid-cols-1 md:grid-cols-3 gap-6">
      <div class="space-y-4 md:col-span-1">
        <input v-model="form.name" type="text" placeholder="Как вас зовут?*" class="w-full p-3 border rounded-xl" required/>
        <p v-if="form.errors.name" class="text-sm text-red-500">{{ form.errors.name }}</p>

        <input v-model="form.phone" type="tel" placeholder="Номер телефона*" class="w-full p-3 border rounded-xl " required/>
        <p v-if="form.errors.phone" class="text-sm text-red-500">{{ form.errors.phone }}</p>

        <input v-model="form.email" type="email" placeholder="Адрес электронной почты" class="w-full p-3 border rounded-xl" />
        <p v-if="form.errors.email" class="text-sm text-red-500">{{ form.errors.email }}</p>
      </div>

      <div class="md:col-span-2 flex flex-col gap-4">
        <textarea v-model="form.message" placeholder="Сообщение" rows="6" class="w-full p-3 border rounded-xl" required></textarea>
        <p v-if="form.errors.message" class="text-sm text-red-500">{{ form.errors.message }}</p>

        <div class="flex items-start gap-2">
          <input type="checkbox" v-model="form.agree" class="mt-1" required/>
          <label class="text-sm">Я согласен на обработку персональных данных согласно Политике конфиденциальности</label>
        </div>
        <p v-if="form.errors.agree" class="text-sm text-red-500">{{ form.errors.agree }}</p>

        <button
          type="submit"
          :disabled="form.processing"
          class="self-start bg-gray-700 text-white px-6 py-2 rounded-xl hover:bg-gray-800 disabled:opacity-50"
        >
          <span v-if="form.processing">Отправка...</span>
          <span v-else>Отправить</span>
        </button>

        <p v-if="isSuccess" class="text-green-600 text-sm font-medium">Спасибо! Ваше сообщение отправлено.</p>
      </div>
    </form>
</template>

<script setup>
  import { useForm } from '@inertiajs/vue3'
  import { ref } from 'vue'

  const isSuccess = ref(false)

  const form = useForm({
    name: '',
    phone: '',
    email: '',
    message: '',
    agree: false,
  })

  const submit = () => {
    isSuccess.value = false

    form.post('/contact', {
      onSuccess: () => {
        isSuccess.value = true
        form.reset()
      },
    })
  }
</script>
