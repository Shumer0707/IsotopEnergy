<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import { Swiper, SwiperSlide } from 'swiper/vue';
import { Autoplay } from 'swiper';
import 'swiper/css';

const brands = ref([]);

onMounted(async () => {
  const res = await axios.get('/layout-data');
  brands.value = res.data.brands ?? [];
});
</script>

<template>
  <Swiper
    :modules="[Autoplay]"
    :slides-per-view="2"
    :breakpoints="{
      640: { slidesPerView: 3 },
      768: { slidesPerView: 5 },
      1024: { slidesPerView: 7 }
    }"
    :space-between="20"
    :loop="true"
    :autoplay="{ delay: 2000, disableOnInteraction: false, pauseOnMouseEnter: true }"
    :speed="1000"
    :grabCursor="true"
    class="pb-2"
  >
    <SwiperSlide
      v-for="brand in brands"
      :key="brand.id"
      class="flex items-center justify-center"
    >
      <img
        :src="brand.logo ? `/storage/${brand.logo}` : '/images/placeholder.jpg'"
        :alt="brand.name"
        class="max-h-10 max-w-[100px] object-contain"
      />
    </SwiperSlide>
  </Swiper>
</template>
