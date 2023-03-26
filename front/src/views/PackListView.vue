
<script setup>
import { ref, computed, onMounted } from 'vue';
import { packApi } from '@/services/api';
import { localizePath } from '@/i18n';
import { useI18n } from 'vue-i18n';
import { useRoute } from 'vue-router';
import Dismissable from '../components/Dismissable.vue';

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
  rowsPerPage: 10,
  rowsNumber: 10
})
const filter = ref('')
const loading = ref(false)
const deletePack = ref({})
const deletePrompt = ref(false)

const columns = [
  { name: 'id', required: true, label: t('ID'), align: 'left', field: pack => pack.id, sortable: true },
  { name: 'name', required: true, label: t('Name'), align: 'center', field: pack => pack.name, sortable: true },
  { name: 'products', required: true, label: t('Products'), align: 'center', field: pack => pack.products, sortable: false },
  { name: 'enabled', required: true, label: t('Enabled'), align: 'center', field: pack => pack.enabled, sortable: false },
  { name: 'created_at', required: true, label: t('createdAt'), align: 'center', field: pack => pack.created_at, sortable: true, format: dateFormat },
  { name: 'updated_at', required: true, label: t('lastUpdated'), align: 'center', field: pack => pack.updated_at, sortable: true, format: dateFormat },
  { name: 'actions', required: false, label: t('Actions'), align: 'center', field: pack => pack, sortable: false}
]
const success = ref('');
const error = ref('');

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
function updateAll(enabled) {
  packApi.batchUpdate(
    enabled, 
    () => {
      success.value = t("All packs were enabled successfully")
      tableRef.value.requestServerInteraction()
    },
    (errorMsg) => {
      error.value = errorMsg 
    }
  )
}
function openConfirmDelete(pack) {
  deletePack.value = pack
  deletePrompt.value = true
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

//Clear success if error happened and vice-versa
watch(success, (success) => {if(success) error.value = ''})
watch(error, (error) => {if(error) success.value = ''})

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
    <Dismissable v-if="success == 'added'" :text="$t('packAddedSuccessfully')" type="success" @dismiss="success=''" />
    <Dismissable v-if="success == 'deleted'" :text="$t('packDeletedSuccessfully')" type="success" @dismiss="success=''" />   
    <!-- Error Banner -->
    <Dismissable v-if="error" :text="$t(error)" type="error" @dismiss="error=''" />   


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

        <template v-slot:body-cell-enabled="props">
          <q-td class="cell-enabled">
            <q-badge v-if="props.value" rounded color="primary" />
            <q-badge v-else rounded color="grey" />
          </q-td>
        </template>

        <template v-slot:body-cell-actions="props">
          <q-td class="pack-actions">
            <q-btn color="negative" @click="openConfirmDelete(props.value)" >{{ $t('Delete') }}</q-btn>
            <q-btn color="primary" :to="localizePath(`/packs/edit/${props.value.id}`)" >{{ $t('Edit') }}</q-btn>
          </q-td>
        </template>

      </q-table>
    </div>

    <div id="all-actions">
      <q-btn color="primary" @click="updateAll(true)">
        {{ $t("Enable All") }}
      </q-btn>
      <q-btn color="warning" @click="updateAll(false)">
        {{ $t("Disable All") }}
      </q-btn>
    </div>

    <q-dialog v-model="deletePrompt" persistent>
      <q-card>
        <q-card-section class="row items-center">
          <span class="q-ml-sm">{{$t("Are you sure you want to delete this pack?")}}    
            <br />
              #{{ deletePack.id }} - {{ deletePack.name }} ({{ deletePack.products.length }} {{ $t("Products") }})
          </span>
          
        </q-card-section>

        <q-card-actions align="right">
          <q-btn flat :label="$t('Cancel')" color="primary" v-close-popup />
          <q-btn flat :label="$t('Delete')" color="primary" @click="deleteOne(deletePack.id)" v-close-popup />
        </q-card-actions>
      </q-card>
    </q-dialog>

  </q-page>
</template>

<style scoped lang="scss">
#data-table {
  padding-top: 20px;

  .pack-actions {
    button {
      margin: 0 10px;
    }
  }
  .cell-enabled {
    text-align: center;
  }
}

#all-actions {
  margin-top: 20px;

  button {
    margin-right: 10px;
  }
}
</style>
