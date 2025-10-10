<script setup lang="ts">
import { Card } from '@/components/shared/card';
import { Layout } from '@/components/shared/custom/container';
import {
    FormButton,
    FormCombobox,
    FormDatetimePicker,
    FormInput,
    FormTextarea,
} from '@/components/shared/custom/form';
import { SelectOption } from '@/components/shared/select';
import { Label } from '@/components/ui/label';
import { Switch } from '@/components/ui/switch';
import AppLayout from '@/layouts/AppLayout.vue';
import AppMainLayout from '@/layouts/AppMainLayout.vue';
import { dashboard } from '@/routes';
import { edit, index, update } from '@/routes/transactions';
import type { BreadcrumbItem } from '@/types';
import { TransactionCategory } from '@/types/transaction-categories';
import { TransactionWithCategories } from '@/types/transactions';
import { Head, useForm } from '@inertiajs/vue3';
import { computed } from 'vue';

defineOptions({
    layout: AppMainLayout,
});

const props = defineProps<{
    transaction: TransactionWithCategories;
    options: {
        select: {
            categories: SelectOption<TransactionCategory['id']>[];
        };
    };
}>();

const breadcrumbs = computed<BreadcrumbItem[]>(() => [
    {
        title: 'Dashboard',
        href: dashboard().url,
    },
    {
        title: 'Transactions',
        href: index().url,
    },
    {
        title: props.transaction.name,
        href: '#',
    },
    {
        title: 'Edit',
        href: edit({ transaction: props.transaction.id }).url,
    },
]);

const isNegativeAmount = computed<boolean>(
    () => +props.transaction.amount <= 0,
);

const form = useForm({
    name: props.transaction.name,
    categories: props.transaction.categories.map((category) => category.id),
    amount: isNegativeAmount.value
        ? (+props.transaction.amount * -1).toFixed(2)
        : props.transaction.amount,
    remark: props.transaction.remark ?? undefined,
    expense: isNegativeAmount.value,
    transactioned_at: new Date(props.transaction.transactioned_at),
});

const submit = () =>
    form.put(update({ transaction: props.transaction.id }).url, {
        preserveScroll: true,
        preserveState: true,
    });
</script>

<template>
    <Head :title="transaction.name" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <Layout>
            <form @submit.prevent="submit">
                <Card :title="`Edit ${transaction.name}`">
                    <FormInput
                        label="Name"
                        :error="form.errors.name"
                        v-model:model-value="form.name"
                    />
                    <FormCombobox
                        label="Categories"
                        :options="options.select.categories"
                        placeholder="Select Categories"
                        error-key="categories"
                        v-model:model-value="form.categories"
                        multiple
                    />
                    <FormInput
                        label="Amount"
                        :error="form.errors.amount"
                        v-model:model-value="form.amount"
                    />
                    <FormTextarea
                        label="Remark"
                        :error="form.errors.remark"
                        v-model:model-value="form.remark"
                    />
                    <FormDatetimePicker
                        label="Transactioned At"
                        :error="form.errors.transactioned_at"
                        v-model:model-value="form.transactioned_at"
                    />
                    <div class="flex items-center space-x-2">
                        <Switch
                            name="expense"
                            v-model:model-value="form.expense"
                        />
                        <Label for="expense">Expense</Label>
                    </div>
                    <FormButton
                        type="submit"
                        :disabled="form.processing"
                        label="Update"
                        :loading="form.processing"
                    />
                </Card>
            </form>
        </Layout>
    </AppLayout>
</template>
