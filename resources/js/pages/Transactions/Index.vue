<script setup lang="ts">
import { PaginateData } from '@/components/shared/pagination';
import { DataTable } from '@/components/shared/table';
import { useFormatDateTime } from '@/composables/useFormatDateTime';
import { useRouteParams } from '@/composables/useRouteParams';
import AppLayout from '@/layouts/AppLayout.vue';
import AppMainLayout from '@/layouts/AppMainLayout.vue';
import { dashboard } from '@/routes';
import transactionsRoute from '@/routes/transactions';
import { BreadcrumbItem } from '@/types';
import { Filter } from '@/types/shared';
import { Transaction } from '@/types/transactions';
import { Head, router, useForm } from '@inertiajs/vue3';
import { ColumnDef, VisibilityState } from '@tanstack/vue-table';
import { computed, h, watch } from 'vue';

defineOptions({
    layout: AppMainLayout,
});

const props = defineProps<{
    transactions: PaginateData<Transaction[]>;
    total_amount: number;
}>();

const { params } = useRouteParams();
const { formatDateTime } = useFormatDateTime();

const breadcrumbs = computed<BreadcrumbItem[]>(() => [
    {
        title: 'Dashboard',
        href: dashboard.url(),
    },
    {
        title: 'Transactions',
        href: transactionsRoute.index.url(),
    },
]);

const columns = computed<ColumnDef<Transaction>[]>(() => [
    {
        accessorKey: 'id',
        header: 'ID',
        cell: ({ row }) => h('div', null, row.getValue('id')),
    },
    {
        accessorKey: 'name',
        header: () => h('div', null, 'Name'),
        cell: ({ row }) => h('div', null, row.getValue('name')),
    },
    {
        accessorKey: 'amount',
        header: 'Amount',
        cell: ({ row }) => {
            const { amount } = row.original;

            return h(
                'div',
                { class: +amount < 0 && 'text-destructive' },
                amount,
            );
        },
        footer: () =>
            h(
                'div',
                { class: props.total_amount < 0 && 'text-destructive' },
                props.total_amount.toLocaleString(undefined, {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2,
                }),
            ),
    },
    {
        accessorKey: 'transactioned_at',
        header: 'Transactioned At',
        cell: ({ row }) =>
            h(
                'div',
                null,
                formatDateTime(row.getValue('transactioned_at')) || '',
            ),
    },
]);

const columnVisibility: VisibilityState = {};

const filter = useForm<Filter>({
    entries: params.get('entries') ?? '10',
});

watch(
    filter,
    (newFilter) => {
        router.visit(transactionsRoute.index({ query: newFilter }), {
            preserveScroll: true,
            preserveState: true,
        });
    },
    { deep: true },
);
</script>

<template>
    <Head title="Transactions" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div
            class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4"
        >
            <DataTable
                v-model:model-value="filter"
                :columns="columns"
                :paginate-data="transactions"
                :column-visibility="columnVisibility"
            />
        </div>
    </AppLayout>
</template>
