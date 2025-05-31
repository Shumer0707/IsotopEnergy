import { ref } from 'vue'
import { useFavoritesStore } from '@/Stores/favorites'
import { useCartStore } from '@/Stores/cart'
import { useAppStateStore } from '@/Stores/appState'
import { useCategoryStore } from '@/Stores/category'
import { usePage } from '@inertiajs/vue3'
import { waitForAllImages } from '@/utils/imageLoadRegistry'

export function useInitialLoad() {
  const isReady = ref(false)

  const load = async () => {
    isReady.value = false

    const favorites = useFavoritesStore()
    const cart = useCartStore()
    const app = useAppStateStore()
    const categoryStore = useCategoryStore()
    const page = usePage()
    const { cartFromServer, locale } = page.props

    cart.set(cartFromServer)

    await Promise.all([
      !app.isLoaded('favorites') && favorites.load(),
      !app.isLoaded('cart') && cart.init(),
      categoryStore.loadCategories(locale),
    ])

    await waitForAllImages()

    isReady.value = true
  }

  return { isReady, load }
}
