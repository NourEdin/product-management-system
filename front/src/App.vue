<script setup lang="ts">
import { watch } from 'vue';
import { storeToRefs } from 'pinia';
import { useRouter } from 'vue-router';
import Layout from '@/components/Layout.vue';
import { useUserStore } from './stores/user';
import { localizePath } from '@/i18n'


const router = useRouter()
const userStore = useUserStore()
const { isLoggedIn } = storeToRefs(userStore)

//If use is logged out, redirect to login page
watch(isLoggedIn, (isLoggedIn, wasLoggedIn) => {
  console.log("logged in state changed", isLoggedIn)
  if (!isLoggedIn) {
    console.log("User has logged out")
    router.push(localizePath('/login'))
  } else {
    console.log("User is still logged in")
  }
})

</script>

<template>
  <div id="wrapper">
    <Layout />

  </div>
</template>

<style lang="scss">
#main {
  padding: 10px;

  h2 {
    margin-bottom: 20px;
  }
}
</style>
