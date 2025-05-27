import { ref } from 'vue'
import { useFavoritesStore } from '@/Stores/favorites'
import { useCartStore } from '@/Stores/cart'
import { useAppStateStore } from '@/Stores/appState'

export function useInitialLoad() {
  const isReady = ref(false)

  const load = async () => {
    const favorites = useFavoritesStore()
    const cart = useCartStore()
    const app = useAppStateStore()

    if (!app.isLoaded('favorites')) await favorites.load()
    if (!app.isLoaded('cart')) await cart.init()

    isReady.value = true
  }

  return { isReady, load }
}
