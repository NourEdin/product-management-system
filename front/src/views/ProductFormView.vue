
<script setup> 
import { defineProps, onMounted, ref } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { getProduct, editProduct, addProduct } from '../services/api';
import { useI18n } from 'vue-i18n';

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
    editProduct(
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
    addProduct(
      product.value,
      (fetchedProduct) => {
        console.log("Added product. result", fetchedProduct)
        router.push(`/${locale.value}/products/edit/${fetchedProduct.id}?success=added`)
      },
      (fetchedError) => {
        updateError(fetchedError, 'notAdded')
      }
    )
  }
}
function updateError(fetchedError, defaultError) {
  error.value = fetchedError.message ? fetchedError.message : $t(defaultError)
}
onMounted(() => {
  //If this is edit, fetch the product from backend
  if (props.id) {
    getProduct(props.id,
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
  <main>
    <div v-if="success == 'added'">
      {{ $t('productAddedSuccessfully') }}
    </div>
    <div v-if="success == 'edited'">
      {{ $t('productEditedSuccessfully') }}
    </div>
    <div v-if="error">
      {{ $t(error) }}
    </div>

    <h1 v-if="props.id">{{ $t("editProduct") }}{{ props.id }}</h1>
    <h1 v-else>{{ $t("addNewProduct") }}</h1>

    <input v-model="product.name" />
    <br/>
    <input v-model="product.number" />
    <br />
    <button @click="saveProduct">{{ $t("submit") }}</button>

    
  </main>
</template>

<style scoped>
main {
  flex-direction: column;
  justify-content: center;
  align-items: center;
}
</style>
