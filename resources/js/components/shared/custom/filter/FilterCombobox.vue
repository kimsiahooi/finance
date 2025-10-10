<script setup lang="ts" generic="T extends AcceptableValue">
import { Combobox } from '@/components/shared/combobox';
import { FilterContainer } from '@/components/shared/custom/filter';
import type { SelectOption } from '@/components/shared/select';
import { Label } from '@/components/ui/label';
import { AcceptableValue } from 'reka-ui';

defineProps<{
    label: string;
    placeholder: string;
    options: SelectOption<T>[];
}>();

const model = defineModel<T | T[]>();
</script>

<template>
    <FilterContainer>
        <Label>{{ label }}:</Label>
        <Combobox
            v-bind="$attrs"
            :options="options"
            :placeholder="placeholder"
            v-model:model-value="model"
        />
    </FilterContainer>
</template>
