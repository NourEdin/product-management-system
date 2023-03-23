<script setup>
import { computed } from 'vue'
import { RouterView, RouterLink, useRouter } from 'vue-router'
import { useI18n } from 'vue-i18n';
import { languages, localizePath } from '@/i18n';
import { useUserStore } from '@/stores/user';


const { locale } = useI18n();
const navLanguages = computed( () => 
    languages.filter(lang => lang.locale != locale.value)
)
const router = useRouter();
const userStore = useUserStore()
const isLoggedIn = computed(() => userStore.isLoggedIn)
const loading = computed(() => userStore.loading)

function logout() {
  console.log("Logging user out")
  userStore.logout()
  router.push('/')
}
</script>
<template>
    <q-layout view="hHh lpR fff">
  
      <q-header elevated class="bg-primary text-white" height-hint="98">
        <q-toolbar>
          <q-toolbar-title>
            <q-avatar>
              <img src="@/assets/temp-logo.svg">
            </q-avatar>
            {{ $t('appTitle') }}
          </q-toolbar-title>

          <q-space />

          <q-tabs v-model="tab" shrink>
            <q-route-tab v-for="lang, i in navLanguages" :key="i"  :to="`/${lang.locale}`" :name="lang.locale" :label="lang.label" />
            <q-separator dark vertical inset />
            <q-tab v-if="isLoggedIn" @click="logout" name="logout" :label="$t('logout')" />
            <q-route-tab v-else :to="localizePath('/login')" :label="$t('login')" />
        </q-tabs>
        </q-toolbar>
  
        <q-tabs align="left">
            <q-route-tab :to="localizePath('/')"  :label="$t('Home')" />
            <q-route-tab v-if="isLoggedIn" :to="localizePath('/products')"  :label="$t('Products')" />
            <q-route-tab v-if="isLoggedIn" :to="localizePath('/packs')"  :label="$t('Packs')" />
        </q-tabs>
        
      </q-header>
      
      <q-page-container>
        <div id="progress-bar" >
          <q-linear-progress :class="loading ? '' : 'invisible'" indeterminate  color="secondary" />
        </div>
        <router-view />
      </q-page-container>
  
      <q-footer class="bg-grey-8 text-white">
        <q-toolbar>
        </q-toolbar>
      </q-footer>
  
    </q-layout>
</template>
<style scoped lang="scss">
#progress-bar {
  .invisible {
    visibility: hidden;
  }
}
</style>