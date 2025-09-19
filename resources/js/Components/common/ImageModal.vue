<template>
  <div v-if="isOpen" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-90" @click="closeModal">
    <!-- Контейнер модального окна -->
    <div class="relative w-full max-w-5xl h-[85vh] flex items-center justify-center p-6">
      <!-- Кнопка закрытия -->
      <button
        @click="closeModal"
        class="absolute top-4 right-4 text-white hover:text-gray-300 z-20 bg-my_red bg-opacity-50 rounded-full w-10 h-10 flex items-center justify-center"
      >
        <span class="text-xl font-bold">×</span>
      </button>

      <!-- Подсказка по зуму -->
      <div class="absolute top-4 left-4 z-20 bg-black bg-opacity-50 text-white text-xs px-3 py-2 rounded">
        {{ t['product_modal_zoom'] }}
      </div>

      <!-- Swiper с зумом -->
      <div class="w-full h-full flex items-center justify-center" @click.stop="">
        <Swiper
          :modules="[Navigation, Pagination, Zoom]"
          :initial-slide="initialImageIndex"
          :navigation="images.length > 1"
          :pagination="{ clickable: true }"
          :slides-per-view="1"
          :space-between="0"
          :loop="images.length > 2"
          :zoom="{
            maxRatio: 3,
            minRatio: 1,
            toggle: true,
          }"
          @swiper="onSwiperInit"
          @slide-change="onSlideChange"
          @wheel.prevent="handleWheel"
          class="w-full h-full"
        >
          <SwiperSlide v-for="(image, index) in images" :key="index" class="flex items-center justify-center">
            <!-- Обязательная обертка для зума -->
            <div class="swiper-zoom-container">
              <img
                :src="`/storage/${image.path}`"
                :alt="`${productTitle} — изображение ${index + 1} из ${images.length}`"
                class="max-w-full max-h-full object-contain"
              />
            </div>
          </SwiperSlide>
        </Swiper>
      </div>

      <!-- Кнопки зума (опционально) -->
      <div class="absolute bottom-4 right-4 z-20 flex gap-2" @click.stop="">
        <button
          @click.stop="zoomIn"
          class="bg-black bg-opacity-50 text-white w-8 h-8 rounded-full flex items-center justify-center hover:bg-opacity-70"
          title="Увеличить"
        >
          +
        </button>
        <button
          @click.stop="zoomOut"
          class="bg-black bg-opacity-50 text-white w-8 h-8 rounded-full flex items-center justify-center hover:bg-opacity-70"
          title="Уменьшить"
        >
          −
        </button>
        <button
          @click.stop="resetZoom"
          class="bg-black bg-opacity-50 text-white px-2 h-8 rounded flex items-center justify-center hover:bg-opacity-70 text-xs"
          title="Сбросить зум"
        >
          1:1
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
  import { ref, watch, onMounted, onBeforeUnmount } from 'vue'
  import { Swiper, SwiperSlide } from 'swiper/vue'
  import { Navigation, Pagination, Zoom } from 'swiper'
  import 'swiper/css'
  import 'swiper/css/navigation'
  import 'swiper/css/pagination'
  import 'swiper/css/zoom'
  import { useTranslations } from '@/composables/useTranslations'

  const t = useTranslations()

  const props = defineProps({
    isOpen: {
      type: Boolean,
      default: false,
    },
    images: {
      type: Array,
      default: () => [],
    },
    initialImageIndex: {
      type: Number,
      default: 0,
    },
    productTitle: {
      type: String,
      default: 'Товар',
    },
  })

  const emit = defineEmits(['close', 'imageChange'])

  const swiperInstance = ref(null)
  const currentImageIndex = ref(props.initialImageIndex)

  // Методы
  const closeModal = () => {
    emit('close')
  }

  const onSwiperInit = (swiper) => {
    swiperInstance.value = swiper

    // Ждем чтобы swiper был полностью готов
    setTimeout(() => {
      if (!swiperInstance.value) return

      const hasLoop = props.images.length > 2

      if (hasLoop && swiperInstance.value.slideToLoop) {
        swiperInstance.value.slideToLoop(props.initialImageIndex, 0)
      } else if (swiperInstance.value.slideTo) {
        swiperInstance.value.slideTo(props.initialImageIndex, 0)
      }
    }, 50)
  }

  const onSlideChange = (swiper) => {
    // При включенном loop используем realIndex, иначе activeIndex
    const newIndex = swiper.realIndex !== undefined ? swiper.realIndex : swiper.activeIndex
    currentImageIndex.value = newIndex
    emit('imageChange', newIndex)
  }

  // Методы зума
  const zoomIn = () => {
    if (swiperInstance.value?.zoom) {
      swiperInstance.value.zoom.in()
    }
  }

  const zoomOut = () => {
    if (swiperInstance.value?.zoom) {
      swiperInstance.value.zoom.out()
    }
  }

  const resetZoom = () => {
    if (swiperInstance.value?.zoom) {
      swiperInstance.value.zoom.out()
    }
  }

  // Обработчик колеса мыши
  const handleWheel = (event) => {
    event.preventDefault()
    event.stopPropagation()

    if (!swiperInstance.value?.zoom) return

    if (event.deltaY < 0) {
      // Прокрутка вверх - увеличиваем
      swiperInstance.value.zoom.in()
    } else {
      // Прокрутка вниз - уменьшаем
      swiperInstance.value.zoom.out()
    }
  }

  // Обработка клавиш
  const handleKeydown = (event) => {
    if (!props.isOpen || !swiperInstance.value) return

    switch (event.key) {
      case 'Escape':
        closeModal()
        break
      case 'ArrowLeft':
        if (props.images.length > 1) {
          swiperInstance.value.slidePrev()
        }
        break
      case 'ArrowRight':
        if (props.images.length > 1) {
          swiperInstance.value.slideNext()
        }
        break
      case '+':
      case '=':
        zoomIn()
        break
      case '-':
        zoomOut()
        break
      case '0':
        resetZoom()
        break
    }
  }

  // Lifecycle хуки
  onMounted(() => {
    document.addEventListener('keydown', handleKeydown)
  })

  onBeforeUnmount(() => {
    document.removeEventListener('keydown', handleKeydown)
  })

  // Следим за изменениями props
  watch(
    () => props.initialImageIndex,
    (newIndex) => {
      if (swiperInstance.value && props.isOpen) {
        const hasLoop = props.images.length > 2

        // Добавляем небольшую задержку чтобы swiper был готов
        setTimeout(() => {
          if (hasLoop && swiperInstance.value.slideToLoop) {
            swiperInstance.value.slideToLoop(newIndex, 300)
          } else if (swiperInstance.value.slideTo) {
            swiperInstance.value.slideTo(newIndex, 300)
          }
        }, 50)
      }
    }
  )

  watch(
    () => props.isOpen,
    (newValue) => {
      if (newValue) {
        document.body.style.overflow = 'hidden'

        // Ждем следующий тик чтобы swiper успел инициализироваться
        setTimeout(() => {
          if (swiperInstance.value) {
            const hasLoop = props.images.length > 2

            if (hasLoop && swiperInstance.value.slideToLoop) {
              swiperInstance.value.slideToLoop(props.initialImageIndex, 0)
            } else if (swiperInstance.value.slideTo) {
              swiperInstance.value.slideTo(props.initialImageIndex, 0)
            }
          }
        }, 100)
      } else {
        document.body.style.overflow = 'auto'
      }
    }
  )
</script>

<style>
  /* Кастомные стили для Swiper в модальном окне */
  .swiper-button-next,
  .swiper-button-prev {
    color: white !important;
    background: rgba(0, 0, 0, 0.5) !important;
    border-radius: 50% !important;
    width: 60px !important;
    height: 60px !important;
    margin-top: -22px !important;
  }

  .swiper-button-next:hover,
  .swiper-button-prev:hover {
    background: rgba(0, 0, 0, 0.7) !important;
  }

  .swiper-pagination-bullet {
    background: rgba(255, 255, 255, 0.5) !important;
    opacity: 1 !important;
  }

  .swiper-pagination-bullet-active {
    background: white !important;
  }

  .swiper-pagination {
    bottom: 20px !important;
  }

  /* Стили для зума */
  .swiper-zoom-container {
    width: 100%;
    height: 100%;
    display: flex;
    justify-content: center;
    align-items: center;
  }

  .swiper-slide-zoomed {
    cursor: grab;
  }

  .swiper-slide-zoomed:active {
    cursor: grabbing;
  }
</style>
