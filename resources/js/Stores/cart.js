import { defineStore } from 'pinia';
import axios from 'axios';

export const useCartStore = defineStore('cart', {
    state: () => ({
        items: {},
    }),

    actions: {
        set(cartFromServer) {
            this.items = cartFromServer;
        },

        clear() {
            this.items = {};
            axios.delete('/cart/clear');
        },

        add(productId, quantity = 1) {
            const existing = this.items[productId] ?? 0;

            this.items = {
                ...this.items,
                [productId]: existing + quantity,
            };

            axios.post('/cart/add', {
                product_id: productId,
                quantity,
            });
        },

        update(productId, quantity) {
            if (quantity < 1) return;

            this.items = {
                ...this.items,
                [productId]: quantity,
            };

            axios.post('/cart/update', {
                product_id: productId,
                quantity,
            });
        },

        increment(productId) {
            const qty = (this.items[productId] ?? 0) + 1;
            this.update(productId, qty);
        },

        decrement(productId) {
            const qty = (this.items[productId] ?? 0) - 1;

            if (qty < 1) {
                this.remove(productId);
            } else {
                this.update(productId, qty);
            }
        },

        remove(productId) {
            const { [productId]: _, ...rest } = this.items;
            this.items = rest;

            axios.delete(`/cart/remove/${productId}`);
        },
    },
});
