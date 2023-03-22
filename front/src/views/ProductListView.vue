
<script setup>
import { ref, computed, onMounted } from 'vue';
import { getProducts, deleteProduct } from '@/services/api';
import LocalizedLink from '@/components/l10n/LocalizedLink.vue'

const products = ref([])

function deleteOne (id)  {
  console.log("Deleting product", id);
  deleteProduct(id, () => getAll());
}
function getAll() {
  getProducts((allProducts) => {products.value = allProducts})
}

onMounted(() => {
  getAll()
})

</script>

<template>
  <main>
    <h1>{{ $t('Products') }}</h1>
    <LocalizedLink :to="`/products/add`">{{ $t("Add New") }}</LocalizedLink>
    <table v-if="products.length > 0">
      <thead>
        <tr>
          <td>{{ $t('ID') }}</td>
          <td>{{ $t('Name') }}</td>
          <td>{{ $t('Number') }}</td>
          <td>{{ $t('createdAt') }}</td>
          <td>{{ $t('lastUpdated') }}</td>
          <td>{{ $t('Actions') }}</td>
        </tr>
      </thead>
      <tbody>
        <tr v-for="product in products" :key="product.id" >
          <td>{{ product.id }}</td>
          <td>{{ product.name }}</td>
          <td>{{ product.number }}</td>
          <td>{{ product.created_at }}</td>
          <td>{{ product.updated_at }}</td>
          <td>
            <LocalizedLink :to="`/products/edit/${product.id}`">{{ $t("Edit") }}</LocalizedLink>
            <a href="#" @click="deleteOne(product.id)">{{ $t("Delete") }}</a>
          </td>
        </tr>
      </tbody>
    </table>
    <div v-else>
      {{ $t("noProducts") }}
    </div>
    
  </main>
</template>

<style scoped>
main {
  flex-direction: column;
  justify-content: center;
  align-items: center;
}
</style>
