<script setup> 
import { onMounted, ref } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { productApi } from '../services/api';
import { useI18n } from 'vue-i18n';
import Dismissable from '../components/Dismissable.vue';

const props = defineProps(['id'])
const route = useRoute();
const { locale } = useI18n();
const router = useRouter()
const product = ref({
  id: 0,
  name: '',
  number: ''

})
const success = ref('');

const error = ref('')
function saveProduct() {
  // console.log("Saving product")
  if (props.id) { //Case of edit
    productApi.edit(
      props.id,
      product.value,
      (fetchedProduct) => {
        console.log("Edited product. ressult", fetchedProduct)
        product.value = fetchedProduct
        success.value = 'edited'
      },
      (fetchedError) => {
        updateError(fetchedError, 'notSavedError')
      }
    )
  } else { //Case of add
    productApi.add(
      product.value,
      (fetchedProduct) => {
        console.log("Added product. result", fetchedProduct)
        router.push(`/${locale.value}/products?success=added`)
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

//Clear success if error happened and vice-versa
watch(success, (success) => {if(success) error.value = ''})
watch(error, (error) => {if(error) success.value = ''})

onMounted(() => {
  //If this is edit, fetch the product from backend
  if (props.id) {
    productApi.get(props.id,
      (fetchedProduct) => {product.value = fetchedProduct},
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
    <h2 v-if="props.id">{{ $t("editProduct") }}{{ props.id }}</h2>
    <h2 v-else>{{ $t("addNewProduct") }}</h2>
    <!-- Success Banners -->
    <Dismissable v-if="success == 'edited'" :text="$t('productEditedSuccessfully')" type="success" @dismiss="success=''" />

    <!-- Error Banner -->
    <Dismissable v-if="error" :text="$t(error)" type="error" @dismiss="error=''" />  

    <q-form
    @submit="saveProduct"
    class="q-gutter-md"
    >
     
      <q-input
        filled
        v-model="product.name"
        :label="$t('Product name')"
        lazy-rules
        :rules="[ val => val && val.length > 0 || $t('Please type something')]"
      />

      <q-input
        filled
        type="text"
        v-model="product.number"
        :label="$t('Product number')"
      />
      <div>
        <q-btn :label="$t('submit') " type="submit" color="primary"/>
      </div>
    </q-form>
    
  </q-page>
</template>

<style scoped lang="scss">

</style>
