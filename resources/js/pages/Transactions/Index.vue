<script setup lang="ts">
import { DeleteDialog } from '@/components/shared/dialog';
import type { DeleteDialogType } from '@/components/shared/dialog/types';
import type { PaginateData } from '@/components/shared/pagination/types';
import { DataTable, Dropdown } from '@/components/shared/table';
import type { DropdownAction, Filter, SearchConfig, VisibilityState } from '@/components/shared/table/types';
import { Button } from '@/components/ui/button';
import { useFormatDateTime } from '@/composables/useFormatDateTime';
import { entryOptions } from '@/constants/entries/options';
import AppLayout from '@/layouts/AppLayout.vue';
import AppMainLayout from '@/layouts/AppMainLayout.vue';
import type { BreadcrumbItem } from '@/types';
import type { Transaction } from '@/types/transactions';
import { Head, Link, router } from '@inertiajs/vue3';
import type { ColumnDef } from '@tanstack/vue-table';
import { computed, h, reactive, watch } from 'vue';

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

const deleteDialog = reactive<DeleteDialogType<Transaction>>({
    isDeleting: false,
    isOpen: false,
    title: '',
    data: null,
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

const setDeleteDialog = (transaction: Transaction) => {
    deleteDialog.data = transaction;
};

const resetHandler = () => {
    deleteDialog.data = null;
};

const deleteHandler = () => {
    if (deleteDialog.data && !deleteDialog.isDeleting) {
        deleteDialog.isDeleting = true;
        router.visit(route('transactions.destroy', { transaction: deleteDialog.data.id }), {
            method: 'delete',
            onFinish: () => {
                deleteDialog.isDeleting = false;
                deleteDialog.data = null;
            },
        });
    }
};

const columnVisibility = <VisibilityState<Transaction>>{
    remark: false,
};

const columns: ColumnDef<Transaction>[] = [
    {
        accessorKey: 'name',
        header: () => h('div', null, 'Name'),
        cell: ({ row }) => h('div', null, row.getValue('name')),
    },
    {
        accessorKey: 'remark',
        header: () => h('div', null, 'Remark'),
        cell: ({ row }) => h('div', null, row.getValue('remark')),
    },
    {
        accessorKey: 'type',
        header: () => h('div', null, 'Type'),
        cell: ({ row }) => {
            const { type_display } = row.original;

            return h('div', null, type_display);
        },
    },
    {
        accessorKey: 'amount',
        header: () => h('div', { class: 'text-center' }, 'Amount'),
        cell: ({ row }) => {
            const { type_display } = row.original;

            return h('div', { class: ['text-center', { 'text-destructive': type_display !== 'Income' }] }, row.getValue('amount'));
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
                            name: 'Edit',
                            type: 'link',
                            url: route('transactions.edit', { transaction: transaction.id }),
                        },
                        {
                            name: 'Delete',
                            type: 'button',
                            onClick: () => setDeleteDialog(transaction),
                        },
                    ],
                }),
            );
        },
    },
];

watch(
    () => deleteDialog.data,
    (newDeleteData) => {
        if (newDeleteData) {
            deleteDialog.title = newDeleteData.name;
            deleteDialog.isOpen = true;
        } else {
            deleteDialog.title = '';
            deleteDialog.isOpen = false;
        }
    },
);
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
            <DeleteDialog :delete-dialog="deleteDialog" @reset="resetHandler" @delete="deleteHandler" />
        </div>
    </AppLayout>
</template>
