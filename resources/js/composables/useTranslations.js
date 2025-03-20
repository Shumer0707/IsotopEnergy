import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';

export function useTranslations() {
    return computed(() => usePage().props.translations || {});
}
