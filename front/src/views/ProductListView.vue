
<script setup>
import { ref, computed, onMounted } from 'vue';
import { productApi } from '@/services/api';
import { localizePath } from '@/i18n';
import { useI18n } from 'vue-i18n';
import { useRoute } from 'vue-router';
import ProductTable from '../components/ProductTable.vue';

const route = useRoute()
const {t, locale} = useI18n()
const childRef = ref(null)

const loading = ref(false)
const success = ref('');


function deleteOne (id)  {
  console.log("Deleting product", id);
  productApi.remove(id, () => {
    childRef.value.requestServerInteraction()
  });
}

onMounted(() => {
  //If you find '?success' in the url, save it to the variable
  if (route.query.success) {
    success.value = route.query.success
  }
})

</script>

<template>
  <q-page id="main">
    <h2>{{ $t('Products') }}</h2>
    <div v-if="success == 'added'">
      {{ $t('productAddedSuccessfully') }}
    </div>

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
            <q-btn color="negative" @click="deleteOne(props.value)" >{{ $t('Delete') }}</q-btn>
            <q-btn color="primary" :to="localizePath(`/products/edit/${props.value}`)" >{{ $t('Edit') }}</q-btn>
          </q-td>
        </template>

      </ProductTable>
    </div>

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
