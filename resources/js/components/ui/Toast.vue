<script setup lang="ts">
import { toastState } from '@/composables/toast';
</script>

<template>
  <div class="fixed top-4 right-4 z-50 flex flex-col gap-2">
    <div v-for="m in toastState.messages" :key="m.id" :class="['max-w-sm w-full rounded p-3 shadow', m.type === 'success' ? 'bg-green-50 border border-green-200 text-green-900' : m.type === 'error' ? 'bg-red-50 border border-red-200 text-red-900' : 'bg-neutral-50 border']">
      <div class="font-semibold" v-if="m.title">{{ m.title }}</div>
      <div class="text-sm">{{ m.body }}</div>
    </div>
  </div>
</template>
