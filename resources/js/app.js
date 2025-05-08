import '../css/app.css'
import './bootstrap'

import { createInertiaApp } from '@inertiajs/vue3'
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers'
import { createApp, h } from 'vue'
import { ZiggyVue } from '../../vendor/tightenco/ziggy'
import { createPinia } from 'pinia'
import Layout from './Layouts/Layout.vue'
import { library } from '@fortawesome/fontawesome-svg-core'
import { faHeart as farHeart } from '@fortawesome/free-regular-svg-icons'
import { faShoppingCart, faHeart as fasHeart } from '@fortawesome/free-solid-svg-icons'
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'

// Добавляем нужные иконки
library.add(farHeart, fasHeart, faShoppingCart)

const appName = import.meta.env.VITE_APP_NAME || 'Laravel'

// 🔹 Определяем страницы, которые должны использовать Layout.vue
const publicPages = ['Home', 'About', 'Cart', 'Contacts', 'Product', 'Products/ProductsByCategory', 'Favorites/IndexFavorites'] // Названия компонентов страниц

createInertiaApp({
  title: (title) => `${title} - ${appName}`,
  resolve: (name) =>
    resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')).then((page) => {
      if (publicPages.includes(name)) {
        page.default.layout = page.default.layout || Layout
      }
      return page
    }),
  setup({ el, App, props, plugin }) {
    const pinia = createPinia()
    return createApp({ render: () => h(App, props) })
      .component('font-awesome-icon', FontAwesomeIcon)
      .use(plugin)
      .use(ZiggyVue)
      .use(pinia)
      .mount(el)
  },
  progress: {
    color: '#4B5563',
  },
})
