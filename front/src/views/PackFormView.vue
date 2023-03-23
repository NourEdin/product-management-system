<script setup> 
import { defineProps, onMounted, ref } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { packApi } from '../services/api';
import { useI18n } from 'vue-i18n';
import ProductTable from '../components/ProductTable.vue';

const props = defineProps(['id'])
const route = useRoute();
const { locale } = useI18n();
const router = useRouter()
const pack = ref({
  id: 0,
  name: '',
  products: []
})

const success = ref('');

const error = ref('')
function savePack() {
  if (props.id) { //Case of edit
    packApi.edit(
      props.id,
      {
        name: pack.value.name,
        productIds: pack.value.products.map(product => product.id)
      },
      fetchedPack => {
        updateState
        success.value = 'edited'
        window.scrollTo(0,0)
      },
      updateError
    )
  } else { //Case of add
    packApi.add(
      {
        name: pack.value.name,
        productIds: pack.value.products.map(product => product.id)
      },
      (fetchedPack) => {
        router.push(`/${locale.value}/packs?success=added`)
      },
      updateError
    )
  }
}
function updateError(fetchedError) {
  error.value = fetchedError ? fetchedError : 'notFoundError'
}
//Updates the ref pack and selectedProducts
function updateState(fetchedPack) {
  pack.value = fetchedPack
  selectedProducts = pack.value.products.map((product) => product.id)
}
onMounted(() => {
  //If this is edit, fetch the pack from backend
  if (props.id) {
    packApi.get(
      props.id,
      updateState,
      updateError
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
      <p>{{ $t("selectPackProducts") }}</p>
      <ProductTable
        :title="$t('Pack Products')"
        context="pack"
        v-model:selected="pack.products"
      />
      <div>
        <q-btn :label="$t('submit') " type="submit" color="primary"/>
      </div>
    </q-form>
    
  </q-page>
</template>

<style scoped lang="scss">

</style>
