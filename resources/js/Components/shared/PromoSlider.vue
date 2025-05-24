<script setup>
  import { Swiper, SwiperSlide } from 'swiper/vue'
  import { Navigation, Pagination, Autoplay } from 'swiper'
  import 'swiper/css'
  import 'swiper/css/navigation'
  import 'swiper/css/pagination'

  const slides = [
    {
      title: 'Скидки до 50%',
      text: 'На всю линейку штукатурок',
      button: 'Узнать больше',
      link: '#',
      image: '/storage/varia/promo-1.webp',
    },
    {
      title: 'Новинка',
      text: 'Грунтовка глубокого проникновения',
      button: 'Смотреть товар',
      link: '#',
      image: '/storage/varia/promo-2.webp',
    },
    {
      title: 'Распродажа',
      text: 'Грунтовка глубокого проникновения',
      button: 'Смотреть товар',
      link: '#',
      image: '/storage/varia/promo-3.webp',
    },
  ]
</script>

<template>
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
        <div class="relative h-[560px] flex flex-col justify-center items-center text-center text-white">
          <img :src="slide.image" alt="slide" class="rounded-xl absolute inset-0 w-full h-full object-cover z-0" />
          <div class="relative z-10 p-6 rounded-xl">
            <h2 class="text-4xl font-bold mb-2">{{ slide.title }}</h2>
            <p class="text-2xl mb-4">{{ slide.text }}</p>
            <!-- <a :href="slide.link" class="bg-pink-600 text-white px-4 py-2 rounded-xl text-sm">
              {{ slide.button }}
            </a> -->
          </div>
        </div>
      </SwiperSlide>
    </Swiper>
  </div>
</template>
