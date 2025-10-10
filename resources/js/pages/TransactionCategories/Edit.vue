<script setup lang="ts">
import { Card } from '@/components/shared/card';
import { Layout } from '@/components/shared/custom/container';
import {
    FormButton,
    FormInput,
    FormTextarea,
} from '@/components/shared/custom/form';
import AppLayout from '@/layouts/AppLayout.vue';
import AppMainLayout from '@/layouts/AppMainLayout.vue';
import { dashboard } from '@/routes';
import { index, update } from '@/routes/transaction-categories';
import type { BreadcrumbItem } from '@/types';
import { TransactionCategory } from '@/types/transaction-categories';
import { Head, useForm } from '@inertiajs/vue3';
import { computed } from 'vue';

defineOptions({
    layout: AppMainLayout,
});

const props = defineProps<{
    category: TransactionCategory;
}>();

const breadcrumbs = computed<BreadcrumbItem[]>(() => [
    {
        title: 'Dashboard',
        href: dashboard().url,
    },
    {
        title: 'Transaction Categories',
        href: index().url,
    },
    {
        title: props.category.name,
        href: '#',
    },
]);

const form = useForm({
    name: props.category.name,
    description: props.category.description ?? undefined,
});

const submit = () =>
    form.put(update({ category: props.category.id }).url, {
        preserveScroll: true,
        preserveState: true,
    });
</script>

<template>
    <Head :title="category.name" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <Layout>
            <form @submit.prevent="submit">
                <Card :title="`Edit ${category.name}`">
                    <FormInput
                        label="Name"
                        :error="form.errors.name"
                        v-model:model-value="form.name"
                    />
                    <FormTextarea
                        label="Description"
                        :error="form.errors.description"
                        v-model:model-value="form.description"
                    />
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
