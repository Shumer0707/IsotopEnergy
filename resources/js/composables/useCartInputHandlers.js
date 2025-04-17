import { useCartStore } from '@/Stores/cart';

export function useCartInputHandlers() {
    const cart = useCartStore();

    const onQtyInput = (e, productOrId) => {
        const id = getId(productOrId);
        const qty = parseInt(e.target.value);

        if (!Number.isNaN(qty) && qty > 0) {
            cart.update(id, qty);
        }
    };

    const onQtyBlur = (e, productOrId) => {
        const id = getId(productOrId);
        const qty = parseInt(e.target.value);

        if (e.target.value === '' || Number.isNaN(qty) || qty <= 0) {
            cart.remove(id);
        }
    };

    const getId = (input) => {
        return typeof input === 'object' ? input.id : input;
    };

    return { onQtyInput, onQtyBlur };
}
