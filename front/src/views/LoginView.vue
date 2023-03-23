
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
  <q-page id="main">
    <q-form
    @submit="login"
    class="q-gutter-md"
    >
      <h2>{{ $t('loginTitle') }}</h2>
      <p class="error" v-if="error">{{ error }}</p>

      <q-input
        filled
        v-model="username"
        :label="$t('Username')"
        lazy-rules
        :rules="[ val => val && val.length > 0 || $t('Please type something')]"
      />

      <q-input
        filled
        type="password"
        v-model="password"
        :label="$t('Password')"
        lazy-rules
        :rules="[
          val => val !== null && val !== '' || $t('Please enter a password')
        ]"
      />
      <div>
        <q-btn label="Submit" type="submit" color="primary"/>
      </div>
    </q-form>

  </q-page>
</template>

<style scoped lang="scss">
#main {
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
}
</style>
