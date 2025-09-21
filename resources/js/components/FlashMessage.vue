<script setup lang="ts">
import { computed } from 'vue';
import { usePage, router } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';

interface Flash {
  success?: boolean;
  text?: string;
  invoiceId?: number;
}

const page = usePage();
const flash = computed(() => page.props.flash as Flash);

function clearFlash() {
  // @ts-ignore
  page.props.flash = {};
}

function autoClearFlash() {
  if (flash.value?.text) {
    setTimeout(() => clearFlash(), 5000);
  }
}

function viewInvoice(id: any) {
  return router.get(route('invoice.show', { id }));
}
</script>

<template>
  <Transition @vue:mounted="autoClearFlash" name="slide-fade">
    <div v-if="flash && flash.text" @click="clearFlash" class="fixed font-medium flex flex-col justify-center items-start right-6 top-6 min-w-64 rounded-lg border p-4 w-fit h-fit z-50" :class="{ 'text-foreground bg-chart-2': flash.success, 'text-foreground bg-destructive': flash.success === false }">
      <p>
        {{ flash.text }}
      </p>
      <Button v-if="flash.invoiceId" @click.stop="viewInvoice(flash.invoiceId)">
        Ver documento
      </Button>
    </div>
  </Transition>
</template>

<style scoped>
.slide-fade-enter-active {
  transition: all 0.3s ease-out;
}

.slide-fade-leave-active {
  transition: all 0.8s cubic-bezier(1, 0.5, 0.8, 1);
}

.slide-fade-enter-from,
.slide-fade-leave-to {
  transform: translateX(20px);
  opacity: 0;
}
</style>
