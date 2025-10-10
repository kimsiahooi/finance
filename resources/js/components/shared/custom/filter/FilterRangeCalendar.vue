<script setup lang="ts">
import { DateRangePicker } from '@/components/shared/calendar';
import { FilterContainer } from '@/components/shared/custom/filter';
import { Label } from '@/components/ui/label';

defineProps<{
    label: string;
}>();

const model = defineModel<{ start?: Date; end?: Date }>();
</script>

<template>
    <FilterContainer>
        <Label>{{ label }}:</Label>
        <DateRangePicker v-model:model-value="model" />
    </FilterContainer>
</template>
