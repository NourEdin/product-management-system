<script setup>
import { ref, computed, onMounted } from 'vue';
import { productApi } from '@/services/api';
import { localizePath } from '@/i18n';
import { useI18n } from 'vue-i18n';

const props = defineProps(['context', 'title', 'selected'])
const emit = defineEmits(['update:selected'])
const selected = computed({
  get() {
    return props.selected
  },
  set(value) {
    emit('update:selected', value)
  }
})

const selectable = computed(() => props.context == 'pack')
const columns = computed(() => {
  //Add these in all cases
  let cols = [
    { name: 'id', required: true, label: t('ID'), align: 'left', field: product => product.id, sortable: true },
    { name: 'name', required: true, label: t('Name'), align: 'center', field: product => product.name, sortable: true },
  ]
  if (props.context == 'product') {
    cols.push(...[
    { name: 'number', required: true, label: t('Number'), align: 'center', field: product => product.number, sortable: true },
    { name: 'created_at', required: true, label: t('createdAt'), align: 'center', field: product => product.created_at, sortable: true, format: dateFormat },
    { name: 'updated_at', required: true, label: t('lastUpdated'), align: 'center', field: product => product.updated_at, sortable: true, format: dateFormat },
    { name: 'actions', required: false, label: t('Actions'), field: product => product.id, sortable: false}
    ])
  }
  return cols;
})
const {t, locale} = useI18n()
const tableRef = ref()
const products = ref([])
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
function getAll(params, props) {
  productApi.list(
    params,
    (data) => {
      products.value = data.products
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
function requestServerInteraction() {
  tableRef.value.requestServerInteraction()
}
onMounted(() => {
  // get initial data from server (1st page)
  requestServerInteraction();
})

defineExpose({
  requestServerInteraction
})
</script>
<template>
  <q-table
    ref="tableRef"
    :title="props.title"
    :rows="products"
    :columns="columns"
    row-key="id"
    v-model:pagination="pagination"
    :filter="filter"
    binary-state-sort
    @request="onRequest"
    v-model:selected="selected"
    :selection="selectable ? 'multiple' : 'none'"
  >
    <template v-slot:top-right>
      <q-input borderless dense debounce="300" v-model="filter" placeholder="Search">
        <template v-slot:append>
          <q-icon name="search" />
        </template>
      </q-input>
    </template>

    <template v-slot:body-cell-actions="props">
      <slot name="body-cell-actions" :value="props.value"></slot>
    </template>

  </q-table>

</template>