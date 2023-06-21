<script setup lang="ts">
import { computed } from 'vue';

const props = defineProps(['text', 'type'])
const emit = defineEmits(['dismiss'])

const classes = computed(() => props.type == 'success' ? 'bg-positive text-white' : 'bg-negative text-white')
</script>
<template>
      <div class="q-pa-md q-gutter-sm">
      <q-banner inline-actions rounded :class="classes">
      {{ text }}

      <template v-slot:action>
          <q-btn flat :label="$t('Dismiss')" @click="emit('dismiss')"/>
      </template>
      </q-banner>
  </div>
</template>