import { createApp } from 'vue'
import { createPinia } from 'pinia'
import { Quasar } from 'quasar'

import App from './App.vue'
import router from './router'
import { i18n } from './i18n'

// Import Quasar css
import 'quasar/src/css/index.sass'

const app = createApp(App)
// app.provide("i18n", i18n)

app.use(createPinia())
app.use(router)
app.use(i18n)
app.use(Quasar, {
    plugins: {}
})

app.mount('#app')
