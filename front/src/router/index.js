import { createRouter, createWebHistory } from 'vue-router'
import HomeView from '../views/HomeView.vue'
import LoginView from '../views/LoginView.vue'
import { defaultLang, setLocale } from '../i18n'
import { useUserStore } from '../stores/user'

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
        {
          path: 'products/',
          children: [
            {
              path: '',
              name: 'product-list',
              component: () => import('../views/ProductListView.vue'),
              meta: { requiresAuth: true }
            },
            {
              path: 'add',
              name: 'product-add',
              component: () => import('../views/ProductFormView.vue'),
              meta: { requiresAuth: true }
            },
            {
              path: 'edit/:id',
              name: 'product-edit',
              component: () => import('../views/ProductFormView.vue'),
              meta: { requiresAuth: true },
              props: true
            }
          ]
        }
      ]
    }

  ]
})

router.beforeEach((to, from) => {
  //Check if route requires auth and user isn't logged in
  const userStore = useUserStore();
  if (to.meta.requiresAuth && !userStore.isLoggedIn) {
    console.log("User is not logged in, redirecting..");
    return {
      path: `${to.params.locale}/login`,
      query: {redirect: to.fullPath}
    }
  }

  //Check if language was changed in the url
  const newLocale = to.params.locale
  const prevLocale = from.params.locale

  // If the locale hasn't changed, do nothing
  if (newLocale === prevLocale) {
    return
  }
  setLocale(newLocale)

})

export default router
