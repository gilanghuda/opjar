<script setup lang="ts">
import { ref, watch, defineEmits, defineProps } from 'vue';
import Dialog from './dialog/Dialog.vue';
import DialogContent from './dialog/DialogContent.vue';
import DialogTitle from './dialog/DialogTitle.vue';
import DialogDescription from './dialog/DialogDescription.vue';
import DialogFooter from './dialog/DialogFooter.vue';
import DialogTrigger from './dialog/DialogTrigger.vue';
import DialogClose from './dialog/DialogClose.vue';
import Button from '@/components/ui/button/Button.vue';

const props = defineProps({
  modelValue: { type: Boolean, default: false },
  title: { type: String, default: 'Konfirmasi' },
  description: { type: String, default: '' },
  confirmLabel: { type: String, default: 'Hapus' },
  cancelLabel: { type: String, default: 'Batal' },
});
const emits = defineEmits(['update:modelValue','confirm']);

const open = ref(!!props.modelValue);

watch(() => props.modelValue, v => open.value = !!v);
watch(open, v => emits('update:modelValue', v));

function confirm() {
  emits('confirm');
  open.value = false;
}
</script>

<template>
  <Dialog v-model="open">
    <DialogContent>
      <DialogTitle>{{ title }}</DialogTitle>
      <DialogDescription v-if="description">{{ description }}</DialogDescription>
      <DialogFooter class="mt-4 flex justify-end gap-2">
        <Button @click="open = false" variant="secondary">{{ cancelLabel }}</Button>
        <Button @click="confirm" variant="destructive">{{ confirmLabel }}</Button>
      </DialogFooter>
    </DialogContent>
  </Dialog>
</template>
