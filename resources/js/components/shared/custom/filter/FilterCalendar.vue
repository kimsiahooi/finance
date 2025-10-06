<script setup lang="ts">
import { DateTimePicker } from '@/components/shared/calendar';
import { FilterContainer } from '@/components/shared/custom/filter';
import { Label } from '@/components/ui/label';

defineProps<{
    label: string;
    placeholder?: string;
}>();

const model = defineModel<Date>();
</script>

<template>
    <FilterContainer>
        <Label>{{ label }}:</Label>
        <DateTimePicker
            class="w-full"
            v-model:model-value="model"
            :placeholder="placeholder"
        />
    </FilterContainer>
</template>
