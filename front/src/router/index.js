import { createRouter, createWebHistory } from 'vue-router'
import HomeView from '../views/HomeView.vue'
import LoginView from '../views/LoginView.vue'
import { defaultLang, setLocale } from '../i18n'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      redirect: `/${defaultLang}`
    },
    {
      path: '/:locale',
      children: [
        {
          path: '',
          name: 'home',
          component: HomeView
        },    
        {
          path: 'login',
          name: 'login',
          component: LoginView
        },
      ]
    }

  ]
})

router.beforeEach((to, from) => {
  const newLocale = to.params.locale
  const prevLocale = from.params.locale
  // If the locale hasn't changed, do nothing
  if (newLocale === prevLocale) {
    return
  }
  setLocale(newLocale)
})

export default router
