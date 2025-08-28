<template>
  <!-- Кнопка появляется только когда пользователь прокрутил страницу -->
  <button
    v-if="isVisible"
    @click="scrollToTop"
    class="fixed bottom-20 right-4 z-40 w-14 h-14 bg-green-500 hover:bg-green-600 text-white p-3 rounded-full shadow-lg transition-all duration-300 transform shadow-lg flex items-center justify-center"
    aria-label="Вернуться наверх"
  >
    <!-- Иконка стрелки вверх -->
    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18" />
    </svg>
  </button>
</template>

<script setup>
  import { ref, onMounted, onUnmounted } from 'vue'

  // Переменная для отслеживания видимости кнопки
  const isVisible = ref(false)

  // Функция для плавной прокрутки наверх
  const scrollToTop = () => {
    window.scrollTo({
      top: 0,
      behavior: 'smooth', // Плавная прокрутка
    })
  }

  // Функция для проверки позиции скролла
  const checkScroll = () => {
    // Показываем кнопку если пользователь прокрутил больше чем на 300px
    isVisible.value = window.scrollY > 300
  }

  // Когда компонент монтируется
  onMounted(() => {
    // Добавляем слушатель события прокрутки
    window.addEventListener('scroll', checkScroll)
    // Проверяем сразу (вдруг страница уже прокручена)
    checkScroll()
  })

  // Когда компонент удаляется
  onUnmounted(() => {
    // Убираем слушатель чтобы не было утечек памяти
    window.removeEventListener('scroll', checkScroll)
  })
</script>
