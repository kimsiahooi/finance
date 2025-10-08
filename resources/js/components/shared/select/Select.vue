<script setup lang="ts" generic="T extends AcceptableValue">
import type { SelectOption } from '@/components/shared/select';
import {
    Select,
    SelectContent,
    SelectGroup,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import type { AcceptableValue } from 'reka-ui';
import { HTMLAttributes } from 'vue';

defineProps<{
    options: SelectOption<T>[];
    placeholder: string;
    triggerClass?: HTMLAttributes['class'];
}>();

const model = defineModel<T | T[]>();
</script>

<template>
    <Select v-model:model-value="model" :disabled="!options.length">
        <SelectTrigger class="min-w-40" :class="triggerClass">
            <SelectValue :placeholder="placeholder" />
        </SelectTrigger>
        <SelectContent>
            <SelectGroup>
                <SelectItem
                    v-for="(option, index) in options"
                    :key="index"
                    :value="option.value"
                >
                    {{ option.name }}
                </SelectItem>
            </SelectGroup>
        </SelectContent>
    </Select>
</template>
