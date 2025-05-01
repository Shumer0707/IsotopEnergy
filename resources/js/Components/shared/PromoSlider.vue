<script setup>
import { Swiper, SwiperSlide } from 'swiper/vue';
import { Navigation, Pagination, Autoplay } from 'swiper';
import 'swiper/css';
import 'swiper/css/navigation';
import 'swiper/css/pagination';

const slides = [
  {
    title: 'Скидки до 50%',
    text: 'На всю линейку штукатурок',
    button: 'Узнать больше',
    link: '#',
  },
  {
    title: 'Новинка',
    text: 'Грунтовка глубокого проникновения',
    button: 'Смотреть товар',
    link: '#',
  },
  {
    title: 'Распродажа',
    text: 'Грунтовка глубокого проникновения',
    button: 'Смотреть товар',
    link: '#',
  },
];
</script>

<template>
  <Swiper
    :modules="[Navigation, Pagination, Autoplay]"
    :loop="true"
    :autoplay="{ delay: 6000, disableOnInteraction: false, pauseOnMouseEnter: true}"
    :navigation="true"
    :pagination="{ clickable: true }"
    :slides-per-view="1"
    :speed="1000"
    :grabCursor="true"
    class="rounded-xl overflow-hidden"
  >
    <SwiperSlide v-for="(slide, index) in slides" :key="index">
      <div class="bg-gray-300 h-[400px] flex flex-col justify-center items-center text-center">
        <h2 class="text-2xl font-bold mb-2">{{ slide.title }}</h2>
        <p class="text-sm mb-4">{{ slide.text }}</p>
        <a
          :href="slide.link"
          class="bg-pink-600 text-white px-4 py-2 rounded-xl text-sm"
        >
          {{ slide.button }}
        </a>
      </div>
    </SwiperSlide>
  </Swiper>
</template>
