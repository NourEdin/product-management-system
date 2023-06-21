
<script setup lang="ts">
import { ref, watch, onMounted, Ref } from 'vue';
import { productApi } from '@/services/api';
import { localizePath } from '@/i18n';
import { useI18n } from 'vue-i18n';
import { useRoute } from 'vue-router';
import ProductTable from '../components/ProductTable.vue';
import Dismissable from '../components/Dismissable.vue';
import { Product } from '@/services/api/product';

const route = useRoute()
const {t, locale} = useI18n()
const childRef = ref()

const loading = ref(false)
const success = ref('');
const error = ref('');
const deleteProduct:Ref<Product|undefined> = ref();
const deletePrompt = ref(false);

function openConfirmDelete(product: Product) {
  deleteProduct.value = product
  deletePrompt.value = true
}

function deleteOne (id: number)  {
  console.log("Deleting product", id);
  productApi.remove(id, () => {
    success.value = 'deleted'
    childRef.value.requestServerInteraction()
  });
}

//Clear success if error happened and vice-versa
watch(success, (success) => {if(success) error.value = ''})
watch(error, (error) => {if(error) success.value = ''})

onMounted(() => {
  //If you find '?success' in the url, save it to the variable
  if (route.query.success) {
    success.value = route.query.success as string
  }
})

</script>

<template>
  <q-page id="main">
    <h2>{{ $t('Products') }}</h2>
    <!-- Success banners -->
    <Dismissable v-if="success == 'added'" :text="$t('productAddedSuccessfully')" type="success" @dismiss="success=''" />
    <Dismissable v-if="success == 'deleted'" :text="$t('productDeletedSuccessfully')" type="success" @dismiss="success=''" />


    <q-btn color="primary" :to="localizePath('/products/add')">
      {{ $t("Add New") }}
    </q-btn>

    
    <div id="data-table">
      <ProductTable
        ref="childRef"
        :title="$t('Products')"
        context="product"
      >

        <template v-slot:body-cell-actions="props">
          <q-td class="product-actions">
            <q-btn color="negative" @click="openConfirmDelete(props.value)" >{{ $t('Delete') }}</q-btn>
            <q-btn color="primary" :to="localizePath(`/products/edit/${props.value.id}`)" >{{ $t('Edit') }}</q-btn>
          </q-td>
        </template>

      </ProductTable>
    </div>

    <q-dialog v-model="deletePrompt" persistent>
      <q-card>
        <q-card-section class="row items-center">
          <span class="q-ml-sm">{{$t("Are you sure you want to delete this product?")}}    
            <br />
              #{{ deleteProduct?.id }} - {{ deleteProduct?.name }} 
          </span>
          
        </q-card-section>

        <q-card-actions align="right">
          <q-btn flat :label="$t('Cancel')" color="primary" v-close-popup />
          <q-btn flat :label="$t('Delete')" color="primary" @click="deleteOne(deleteProduct?.id!)" v-close-popup />
        </q-card-actions>
      </q-card>
    </q-dialog>

  </q-page>
</template>

<style scoped lang="scss">
#data-table {
  padding-top: 20px;
}
.product-actions {
  button {
    margin: 0 10px;
  }
}
</style>
