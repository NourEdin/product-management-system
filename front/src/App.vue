<script setup>
import { computed } from 'vue'
import { RouterView, RouterLink, useRouter } from 'vue-router'
import LocalizedLink from './components/l10n/LocalizedLink.vue';
import { useI18n } from 'vue-i18n';
import { languages } from '@/i18n';
import { useUserStore } from '@/stores/user';

import Layout from './components/Layout.vue';


const { locale } = useI18n();
const navLanguages = computed( () => 
    languages.filter(lang => lang.locale != locale.value)
)
const router = useRouter();
const userStore = useUserStore()
const isLoggedIn = computed(() => userStore.isLoggedIn)

function logout() {
  console.log("Logging user out")
  userStore.logout()
  router.push('/')
}
</script>

<template>
  <div id="wrapper">
    <Layout />

  </div>
</template>

<style scoped lang="scss">

nav {
  display: flex;
  flex-direction: row;
  justify-content: flex-end;
  width: 100%;
  background-color: $primary-color;
  height: 50px;
  font-weight: bold;

  #nav-menu {
    display: flex;
    align-items: center;
    flex-grow: 1;
  }
  #user-menu {
    display: flex;
    align-items: center;
  }
  a {
    padding: 10px;
  }

}
</style>
