<script setup>
import { computed } from 'vue'
import { RouterView, RouterLink, useRouter } from 'vue-router'
import LocalizedLink from './components/l10n/LocalizedLink.vue';
import { useI18n } from 'vue-i18n';
import { languages } from '@/i18n';
import { useUserStore } from '@/stores/user';


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
    <nav>
      <div id="nav-menu">
        <LocalizedLink class="normal-text" to="/">{{ $t('Home') }}</LocalizedLink>
        <div v-if="isLoggedIn">
          <LocalizedLink class="normal-text" to="/products">{{ $t('Products') }}</LocalizedLink>
        </div>
      </div>
      <div id="user-menu">
        <RouterLink v-for="lang,i in navLanguages " :key="i" class="normal-text" :to="`/${lang.locale}`">{{ lang.label }}</RouterLink>
        <a v-if="isLoggedIn" class="normal-text" href="#" @click.prevent="logout">{{ $t("logout") }}</a>
        <LocalizedLink v-else class="normal-text" to="/login">{{ $t("login") }}</LocalizedLink>
      </div>
    </nav>
    <RouterView />
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
