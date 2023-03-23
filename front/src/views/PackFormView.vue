<script setup> 
import { defineProps, onMounted, ref } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { packApi } from '../services/api';
import { useI18n } from 'vue-i18n';

const props = defineProps(['id'])
const route = useRoute();
const { locale } = useI18n();
const router = useRouter()
const pack = ref({
  id: 0,
  name: '',
  number: ''

})
const success = ref('');

const error = ref('')
function savePack() {
  // console.log("Saving pack")
  if (props.id) { //Case of edit
    packApi.edit(
      props.id,
      pack.value,
      (fetchedPack) => {
        console.log("Edited pack. ressult", fetchedPack)
        pack.value = fetchedPack
        success.value = 'edited'
      },
      (fetchedError) => {
        updateError(fetchedError, 'notSavedError')
      }
    )
  } else { //Case of add
    packApi.add(
      pack.value,
      (fetchedPack) => {
        console.log("Added pack. result", fetchedPack)
        router.push(`/${locale.value}/packs?success=added`)
      },
      (fetchedError) => {
        updateError(fetchedError, 'notAdded')
      }
    )
  }
}
function updateError(fetchedError, defaultError) {
  error.value = fetchedError ? fetchedError : defaultError
}
onMounted(() => {
  //If this is edit, fetch the pack from backend
  if (props.id) {
    packApi.get(props.id,
      (fetchedPack) => {pack.value = fetchedPack},
      (fetchedError) => {
        updateError(fetchedError, 'notFoundError')
      }
    )
  }
  //If you find '?success' in the url, save it to the variable
  if (route.query.success) {
    success.value = route.query.success
  }
})


</script>

<template>
  <q-page id="main">
    <h2 v-if="props.id">{{ $t("editPack") }}{{ props.id }}</h2>
    <h2 v-else>{{ $t("addNewPack") }}</h2>

    <div v-if="success == 'edited'">
      {{ $t('packEditedSuccessfully') }}
    </div>
    <div v-if="error">
      {{ $t(error) }}
    </div>

    <q-form
    @submit="savePack"
    class="q-gutter-md"
    >
     
      <q-input
        filled
        v-model="pack.name"
        :label="$t('Pack name')"
        lazy-rules
        :rules="[ val => val && val.length > 0 || $t('Please type something')]"
      />

    <h3>Pack Products</h3>
      <div>
        <q-btn :label="$t('submit') " type="submit" color="primary"/>
      </div>
    </q-form>
    
  </q-page>
</template>

<style scoped lang="scss">

</style>
