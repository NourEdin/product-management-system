<script setup>
import { computed } from 'vue'
import { RouterView, RouterLink } from 'vue-router'
import LocalizedLink from './components/l10n/LocalizedLink.vue';
import { useI18n } from 'vue-i18n';
import { languages } from '@/i18n'

const { locale } = useI18n();
const navLanguages = computed( () => 
    languages.filter(lang => lang.locale != locale.value)
)
</script>

<template>
  <div id="wrapper">
    <nav>
      <div id="nav-menu">
        <LocalizedLink class="normal-text" to="/">Home</LocalizedLink>
      </div>
      <div id="user-menu">
        <RouterLink v-for="lang,i in navLanguages " :key="i" class="normal-text" :to="`/${lang.locale}`">{{ lang.label }}</RouterLink>
        <LocalizedLink class="normal-text" to="/login">Login</LocalizedLink>
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
