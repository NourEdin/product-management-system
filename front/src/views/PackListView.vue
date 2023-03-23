
<script setup>
import { ref, computed, onMounted } from 'vue';
import { packApi } from '@/services/api';
import { localizePath } from '@/i18n';
import { useI18n } from 'vue-i18n';
import { useRoute } from 'vue-router';

const route = useRoute()
const {t, locale} = useI18n()
const tableRef = ref()
const packs = ref([])
const total = ref(0)
//sorting, filtering, pagination options sent to API
const pagination = ref({
  sortBy: 'id',
  descending: false,
  page: 1,
  rowsPerPage: 3,
  rowsNumber: 10
})
const filter = ref('')
const loading = ref(false)

const columns = [
  { name: 'id', required: true, label: t('ID'), align: 'left', field: pack => pack.id, sortable: true },
  { name: 'name', required: true, label: t('Name'), align: 'center', field: pack => pack.name, sortable: true },
  { name: 'products', required: true, label: t('Products'), align: 'center', field: pack => pack.products, sortable: true },
  { name: 'created_at', required: true, label: t('createdAt'), align: 'center', field: pack => pack.created_at, sortable: true, format: dateFormat },
  { name: 'updated_at', required: true, label: t('lastUpdated'), align: 'center', field: pack => pack.updated_at, sortable: true, format: dateFormat },
  { name: 'actions', required: false, label: t('Actions'), field: pack => pack.id, sortable: false}
]
const success = ref('');

function dateFormat(date) {
  //If updated date is empty, show something else.
  //Created date always has a value
  if (!date) {
    return t("Not updated yet");
  }
  //Format date in a readable way
  const dateOb = new Date(date); 
  return dateOb.toLocaleString(locale.value);
}
function deleteOne (id)  {
  console.log("Deleting pack", id);
  packApi.remove(id, () => {
    tableRef.value.requestServerInteraction()
  });
}
function getAll(params, props) {
  packApi.list(
    params,
    (data) => {
      packs.value = data.packs
      total.value = data.total

      //Update the pagination to reflect on the table
      pagination.value.rowsNumber = data.total
      pagination.value.page = props.page
      pagination.value.rowsPerPage = props.rowsPerPage
      pagination.value.sortBy = props.sortBy
      pagination.value.descending = props.descending
  })
}

function onRequest(props) {
  const { page, rowsPerPage, sortBy, descending } = props.pagination
  const filter = props.filter

  // loading.value = true

  const params = {
    term: filter,
    sort: sortBy,
    order: descending ? 'desc' : 'asc',
    max: rowsPerPage,
    offset: (page - 1) * rowsPerPage
  }

  getAll(params, { page, rowsPerPage, sortBy, descending })
}
onMounted(() => {
  // get initial data from server (1st page)
  tableRef.value.requestServerInteraction()

  //If you find '?success' in the url, save it to the variable
  if (route.query.success) {
    success.value = route.query.success
  }
})

</script>

<template>
  <q-page id="main">
    <h2>{{ $t('Packs') }}</h2>
    <div v-if="success == 'added'">
      {{ $t('packAddedSuccessfully') }}
    </div>

    <q-btn color="primary" :to="localizePath('/packs/add')">
      {{ $t("Add New") }}
    </q-btn>

    
    <div id="data-table">
      <q-table
        ref="tableRef"
        :title="$t('Packs')"
        :rows="packs"
        :columns="columns"
        row-key="id"
        v-model:pagination="pagination"
        :loading="loading"
        :filter="filter"
        binary-state-sort
        @request="onRequest"
      >
        <template v-slot:top-right>
          <q-input borderless dense debounce="300" v-model="filter" placeholder="Search">
            <template v-slot:append>
              <q-icon name="search" />
            </template>
          </q-input>
        </template>

        <template v-slot:body-cell-products="props">
          <q-td class="pack-products">
            <p v-for="product in props.value" :key="product.id">{{ product.name }} #{{ product.id }}</p>
          </q-td>
        </template>

        <template v-slot:body-cell-actions="props">
          <q-td class="pack-actions">
            <q-btn color="negative" @click="deleteOne(props.value)" >{{ $t('Delete') }}</q-btn>
            <q-btn color="primary" :to="localizePath(`/packs/edit/${props.value}`)" >{{ $t('Edit') }}</q-btn>
          </q-td>
        </template>

      </q-table>
    </div>

  </q-page>
</template>

<style scoped lang="scss">
#data-table {
  padding-top: 20px;
}
.pack-actions {
  button {
    margin: 0 10px;
  }
}
</style>
