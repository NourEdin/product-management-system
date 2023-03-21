
<script setup>
import { ref, computed } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { useUserStore } from '../stores/user';

const userStore = useUserStore();
const router = useRouter();
const route = useRoute();

const username = ref("");
const password = ref("");
const error = computed(() => userStore.error)

async function login() {
  userStore.login(username.value, password.value, () => {
    router.push(route.query.redirect || '/')
  });
}
</script>

<template>
  <main>
    <h1>{{ $t('loginTitle') }}</h1>
    <label for="username">
      <input id="username" type="text" v-model="username" placeholder="Please enter the username" />
    </label>
    <label for="password">
      <input id="password" type="password" v-model="password" placeholder="Please enter the password" />
    </label>
    <p class="error" v-if="error">{{ error }}</p>
    <button @click="login">Login</button>
  </main>
</template>

<style scoped>
main {
  flex-direction: column;
  justify-content: center;
  align-items: center;
}
</style>
