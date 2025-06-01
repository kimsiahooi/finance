<script setup lang="ts">
import type { PaginateData } from '@/components/shared/pagination/types';
import { DataTable, Dropdown } from '@/components/shared/table';
import type { DropdownAction, Filter, SearchConfig } from '@/components/shared/table/types';
import { Button } from '@/components/ui/button';
import { useFormatDateTime } from '@/composables/useFormatDateTime';
import { entryOptions } from '@/constants/entries/options';
import AppLayout from '@/layouts/AppLayout.vue';
import AppMainLayout from '@/layouts/AppMainLayout.vue';
import type { BreadcrumbItem } from '@/types';
import type { Transaction } from '@/types/transactions';
import { Head, Link, router } from '@inertiajs/vue3';
import type { ColumnDef, VisibilityState } from '@tanstack/vue-table';
import { ArrowUpDown } from 'lucide-vue-next';
import { computed, h, reactive } from 'vue';

defineOptions({
    layout: AppMainLayout,
});

defineProps<{
    transactions: PaginateData<Transaction[]>;
}>();

const { formatDateTime } = useFormatDateTime();

const routeParams = computed(() => route().params);

const filter = reactive<Filter>({
    search: routeParams.value.search,
    entries: routeParams.value.entries || '10',
});

const searchConfig: SearchConfig = {
    placeholder: 'Search name...',
};

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: route('dashboard'),
    },
    {
        title: 'Transactions',
        href: route('transactions.index'),
    },
];

const filterChangeHandler = (filter: Filter) => {
    router.visit(route('transactions.index', { ...filter }));
};

const deleteHandler = (transaction: Transaction) => {
    console.log(transaction);
};

const columnVisibility = <VisibilityState>{
    description: false,
};

const columns: ColumnDef<Transaction>[] = [
    {
        accessorKey: 'id',
        header: ({ column }) => {
            return h(
                Button,
                {
                    variant: 'ghost',
                    class: 'cursor-pointer',
                    onClick: () => column.toggleSorting(column.getIsSorted() === 'asc'),
                },
                () => ['ID', h(ArrowUpDown, { class: 'ml-2 h-4 w-4' })],
            );
        },
        cell: ({ row }) => h('div', null, row.getValue('id')),
    },
    {
        accessorKey: 'name',
        header: () => h('div', null, 'Name'),
        cell: ({ row }) => h('div', null, row.getValue('name')),
    },
    {
        accessorKey: 'description',
        header: () => h('div', null, 'Description'),
        cell: ({ row }) => h('div', null, row.getValue('description')),
    },
    {
        accessorKey: 'type',
        header: () => h('div', null, 'Type'),
        cell: ({ row }) => h('div', null, row.getValue('type')),
    },
    {
        accessorKey: 'amount',
        header: () => h('div', { class: 'text-center' }, 'Amount'),
        cell: ({ row }) => {
            const { type } = row.original;

            return h('div', { class: ['text-center', { 'text-destructive': type !== 'Income' }] }, row.getValue('amount'));
        },
    },
    {
        accessorKey: 'transaction_at',
        header: () => h('div', { class: 'text-center' }, 'Transaction At'),
        cell: ({ row }) => h('div', { class: 'text-center' }, formatDateTime(row.getValue('transaction_at')) || undefined),
    },
    {
        accessorKey: 'created_at',
        header: () => h('div', { class: 'text-center' }, 'Created At'),
        cell: ({ row }) => h('div', { class: 'text-center' }, formatDateTime(row.getValue('created_at')) || undefined),
    },
    {
        id: 'actions',
        enableHiding: false,
        cell: ({ row }) => {
            const transaction = row.original;

            return h(
                'div',
                { class: 'relative' },
                h(Dropdown, {
                    actions: <DropdownAction[]>[
                        {
                            name: 'Delete',
                            type: 'button',
                            onClick: () => deleteHandler(transaction),
                        },
                    ],
                }),
            );
        },
    },
];
</script>

<template>
    <Head title="Transaction List" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <div class="space-y-3">
                <div class="flex flex-wrap items-center justify-end gap-2">
                    <Link :href="route('transactions.create')" as-child>
                        <Button class="cursor-pointer">Create</Button>
                    </Link>
                </div>
                <div>
                    <DataTable
                        :columns="columns"
                        :paginate-data="transactions"
                        :column-visibility="columnVisibility"
                        :filter="filter"
                        :entry-options="entryOptions"
                        :search-config="searchConfig"
                        @filter-change="filterChangeHandler"
                    />
                </div>
            </div>
        </div>
    </AppLayout>
</template>
