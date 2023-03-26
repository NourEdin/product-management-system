<script setup> 
import { onMounted, ref, watch } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { packApi } from '../services/api';
import { useI18n } from 'vue-i18n';
import ProductTable from '../components/ProductTable.vue';
import Dismissable from '../components/Dismissable.vue';

const props = defineProps(['id'])
const route = useRoute();
const { t, locale } = useI18n();
const router = useRouter()
const pack = ref({
  id: 0,
  name: '',
  products: [],
  enabled: false
})

const success = ref('');
const error = ref('');

function savePack() {
  //Validating the pack
  //Note: Name is validated in the table component.
  //Validating number of products
  if (pack.value.products.length < 2) {
    error.value = 'e_addMoreProducts'
    return;
  }
  if (props.id) { //Case of edit
    packApi.edit(
      props.id,
      {
        name: pack.value.name,
        productIds: pack.value.products.map(product => product.id),
        enabled: pack.value.enabled
      },
      fetchedPack => {
        updateState(fetchedPack)
        success.value = 'edited'
        window.scrollTo(0,0)
      },
      updateError
    )
  } else { //Case of add
    packApi.add(
      {
        name: pack.value.name,
        productIds: pack.value.products.map(product => product.id),
        enabled: pack.value.enabled
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
}

watch(success, (success) => {if(success) error.value = ''})
watch(error, (error) => {if(error) success.value = ''})

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
    <h2 v-if="props.id">{{ $t("editPack") }} {{ props.id }}</h2>
    <h2 v-else>{{ $t("addNewPack") }}</h2>

    <!-- Success Banners -->
    <Dismissable v-if="success == 'edited'" :text="$t('packEditedSuccessfully')" type="success" @dismiss="success=''" />

    <!-- Error Banner -->
    <Dismissable v-if="error" :text="$t(error)" type="error" @dismiss="error=''" />  

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
      <div id="enabled-btn">
        <q-toggle 
          v-model="pack.enabled"
          :label="pack.enabled ? $t('Pack is enabled') : $t('Pack is disabled')" />
      </div>
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
