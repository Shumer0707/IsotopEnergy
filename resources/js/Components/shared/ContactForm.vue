<template>
  <h2 class="text-2xl font-bold text-center mb-2">{{ t['contact_title_2'] }}</h2>
  <p class="text-center mb-8">
    {{ t['contact_text'] }}
  </p>

  <form @submit.prevent="submit" class="grid grid-cols-1 md:grid-cols-3 gap-6">
    <div class="space-y-4 md:col-span-1">
      <input v-model="form.name" type="text" :placeholder=t.contact_name class="w-full p-3 border rounded-xl" required />
      <p v-if="form.errors.name" class="text-sm text-red-500">{{ form.errors.name }}</p>

      <input v-model="form.phone" type="tel" :placeholder=t.contact_phone class="w-full p-3 border rounded-xl" required />
      <p v-if="form.errors.phone" class="text-sm text-red-500">{{ form.errors.phone }}</p>

      <input v-model="form.email" type="email" :placeholder=t.contact_mail class="w-full p-3 border rounded-xl" />
      <p v-if="form.errors.email" class="text-sm text-red-500">{{ form.errors.email }}</p>
    </div>

    <div class="md:col-span-2 flex flex-col gap-4">
      <textarea v-model="form.message" :placeholder=t.contact_mess rows="6" class="w-full p-3 border rounded-xl" required></textarea>
      <p v-if="form.errors.message" class="text-sm text-red-500">{{ form.errors.message }}</p>

      <div class="flex items-start gap-2">
        <input type="checkbox" v-model="form.agree" class="mt-1" required />
        <label class="text-sm">{{ t['contact_agreement'] }}</label>
      </div>
      <p v-if="form.errors.agree" class="text-sm text-red-500">{{ form.errors.agree }}</p>

      <button
        type="submit"
        :disabled="form.processing"
        class="self-start bg-my_green text-white px-6 py-2 rounded-xl hover:bg-gray-800 disabled:opacity-50"
      >
        <span v-if="form.processing">{{ t['contact_send_end'] }}</span>
        <span v-else>{{ t['contact_send'] }}</span>
      </button>

      <p v-if="isSuccess" class="text-green-600 text-sm font-medium">{{ t['contact_send_thanks'] }}</p>
    </div>
  </form>
</template>

<script setup>
  import { useForm } from '@inertiajs/vue3'
  import { ref } from 'vue'
  import { useTranslations } from '@/composables/useTranslations'

  const t = useTranslations()
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
