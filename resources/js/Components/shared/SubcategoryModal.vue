<script setup>
import { onMounted, onBeforeUnmount, ref } from "vue";
import { Link } from "@inertiajs/vue3";

defineProps({
    category: Object,
});
const emit = defineEmits(["close"]);

const modalRef = ref(null);

const handleClickOutside = (event) => {
    if (modalRef.value && !modalRef.value.contains(event.target)) {
        emit("close");
    }
};

onMounted(() => {
    document.addEventListener("mousedown", handleClickOutside);
});
onBeforeUnmount(() => {
    document.removeEventListener("mousedown", handleClickOutside);
});
</script>

<template>
    <transition
        name="fade"
        enter-active-class="transition-opacity duration-300"
        leave-active-class="transition-opacity duration-200"
        enter-from-class="opacity-0"
        enter-to-class="opacity-100"
        leave-from-class="opacity-100"
        leave-to-class="opacity-0"
    >
        <div
            v-if="category"
            class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center z-50"
        >
            <div
                ref="modalRef"
                class="bg-white rounded-xl p-6 w-full max-w-xl shadow-lg relative"
            >
                <button
                    @click="$emit('close')"
                    class="absolute top-2 right-2 text-gray-500 hover:text-black text-xl"
                >
                    &times;
                </button>
                <h2 class="text-xl font-semibold mb-4">{{ category.name }}</h2>

                <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                    <div
                        v-for="sub in category.children"
                        :key="sub.id"
                        class="flex flex-col items-center justify-center border rounded-lg p-4 hover:bg-gray-100"
                    >
                        <Link
                            :href="`/category/${sub.id}`"
                            class="text-center font-medium text-black"
                        >
                            {{ sub.name }}
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </transition>
</template>
