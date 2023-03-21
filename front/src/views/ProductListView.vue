
<script setup>
import { ref, computed, onMounted } from 'vue';
import { getProducts } from '@/services/api';
import LocalizedLink from '@/components/l10n/LocalizedLink.vue'

const products = ref([])

function deleteProd (id)  {
  console.log("Deleting product", id);
}
onMounted(() => {
  getProducts((allProducts) => {products.value = allProducts})
})

</script>

<template>
  <main>
    <h1>{{ $t('Products') }}</h1>
    <LocalizedLink :to="`/products/add`">{{ $t("Add New") }}</LocalizedLink>
    <table>
      <thead>
        <tr>
          <td>{{ $t('ID') }}</td>
          <td>{{ $t('Name') }}</td>
          <td>{{ $t('Number') }}</td>
          <td>{{ $t('Actions') }}</td>
        </tr>
      </thead>
      <tbody>
        <tr v-for="product in products" :key="product.id" >
          <td>{{ product.id }}</td>
          <td>{{ product.name }}</td>
          <td>{{ product.number }}</td>
          <td>
            <LocalizedLink :to="`/products/edit/${product.id}`">{{ $t("Edit") }}</LocalizedLink>
            <a href="#" @click="deleteProd(product.id)">{{ $t("Delete") }}</a>
          </td>
        </tr>
      </tbody>
    </table>
    
  </main>
</template>

<style scoped>
main {
  flex-direction: column;
  justify-content: center;
  align-items: center;
}
</style>
