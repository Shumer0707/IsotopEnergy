<script setup>
  import { registerImage } from '@/utils/imageLoadRegistry'
  import { Swiper, SwiperSlide } from 'swiper/vue'
  import { Navigation, Pagination, Autoplay } from 'swiper'
  import 'swiper/css'
  import 'swiper/css/navigation'
  import 'swiper/css/pagination'
  import { useTranslations } from '@/composables/useTranslations'

  const t = useTranslations()
  const slides = [
    {
      title: t.value.slider_title_1,
      text: t.value.slider_text_1,
      button: 'Узнать больше',
      link: '#',
      image: '/storage/varia/promo-1.webp',
    },
    {
      title: t.value.slider_title_2,
      text: t.value.slider_text_2,
      button: 'Смотреть товар',
      link: '#',
      image: '/storage/varia/promo-2.webp',
    },
    {
      title: t.value.slider_title_3,
      text: t.value.slider_text_3,
      button: 'Смотреть товар',
      link: '#',
      image: '/storage/varia/promo-3.webp',
    },
  ]

  slides.forEach((slide) => registerImage(slide.image))
</script>

<template>
  <h2 class="sr-only">{{ t['slider_section_h2'] }}</h2>

  <div class="max-w-[1280px] mx-auto">
    <Swiper
      :modules="[Navigation, Pagination, Autoplay]"
      :loop="true"
      :autoplay="{ delay: 6000, disableOnInteraction: false, pauseOnMouseEnter: true }"
      :navigation="true"
      :pagination="{ clickable: true }"
      :slides-per-view="1"
      :speed="1000"
      :grabCursor="true"
      class="rounded-xl overflow-hidden"
    >
      <SwiperSlide v-for="(slide, index) in slides" :key="index">
        <div class="relative h-[280px] lg:h-[560px] flex flex-col justify-center items-center text-center text-white">
          <img :src="slide.image" :alt="slide.title" class="rounded-xl absolute inset-0 w-full h-full object-cover z-0" />
          <div class="relative z-10 p-6 rounded-xl">
            <h3 aria-hidden="true" class="font-heading text-2xl lg:text-4xl font-bold mb-2">
              {{ slide.title }}
            </h3>
            <p class="text-xl lg:text-2xl text-shadow-xl mb-4">{{ slide.text }}</p>
            <!-- <a :href="slide.link" class="bg-pink-600 text-white px-4 py-2 rounded-xl text-sm">
              {{ slide.button }}
            </a> -->
          </div>
        </div>
      </SwiperSlide>
    </Swiper>
  </div>
</template>

<style>
  .swiper-button-prev,
  .swiper-button-next {
    color: rgba(101, 177, 25) !important;
  }

  .swiper-pagination-bullet {
    background-color: rgba(101, 177, 25, 0.3) !important;
    opacity: 1 !important;
  }

  .swiper-pagination-bullet-active {
    background-color: rgba(101, 177, 25) !important;
  }
</style>
