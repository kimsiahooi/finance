<script setup lang="ts">
import { DateTimePicker } from '@/components/shared/calendar';
import { FormContainer, FormError } from '@/components/shared/custom/form';
import { Label } from '@/components/ui/label';
import { useError } from '@/composables/useError';
import { computed } from 'vue';

const props = defineProps<{
    label: string;
    placeholder?: string;
    error?: string;
    errorKey?: string;
}>();

const { findErrors } = useError();
const model = defineModel<Date>();

const existingErrors = computed(
    () => props.errorKey && findErrors(props.errorKey),
);
</script>

<template>
    <FormContainer>
        <Label
            :class="{
                'text-destructive': error || existingErrors,
            }"
        >
            {{ label }}:
        </Label>
        <DateTimePicker
            v-model:model-value="model"
            :placeholder="placeholder"
        />
        <FormError :error="error" :error-key="errorKey" />
    </FormContainer>
</template>
