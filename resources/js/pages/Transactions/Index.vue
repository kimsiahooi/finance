<script setup lang="ts">
import { Combobox } from '@/components/shared/combobox';
import { ActionButton } from '@/components/shared/custom/action';
import {
    FilterCard,
    FilterInput,
    FilterRangeCalendar,
} from '@/components/shared/custom/filter';
import { DeleteDialog } from '@/components/shared/dialog';
import { PaginateData } from '@/components/shared/pagination';
import { SelectOption } from '@/components/shared/select';
import { DataTable } from '@/components/shared/table';
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogClose,
    DialogContent,
    DialogDescription,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Switch } from '@/components/ui/switch';
import { Textarea } from '@/components/ui/textarea';
import { useDecimal } from '@/composables/useDecimal';
import { useFormatDateTime } from '@/composables/useFormatDateTime';
import { useRouteParams } from '@/composables/useRouteParams';
import AppLayout from '@/layouts/AppLayout.vue';
import AppMainLayout from '@/layouts/AppMainLayout.vue';
import { dashboard } from '@/routes';
import { destroy, edit, index, store } from '@/routes/transactions';
import { BreadcrumbItem } from '@/types';
import { Filter } from '@/types/shared';
import { TransactionCategory } from '@/types/transaction-categories';
import { TransactionWithCategories } from '@/types/transactions';
import { Head, router, useForm } from '@inertiajs/vue3';
import { ColumnDef, VisibilityState } from '@tanstack/vue-table';
import { pickBy } from 'lodash-es';
import { Loader, Pencil, Trash2 } from 'lucide-vue-next';
import { computed, h, reactive, ref, watch } from 'vue';

type DataType = TransactionWithCategories;

defineOptions({
    layout: AppMainLayout,
});

const props = defineProps<{
    transactions: PaginateData<DataType[]>;
    report: {
        total_amount: number;
    };
    options: {
        select: {
            categories: SelectOption<TransactionCategory['id']>[];
        };
    };
}>();

const { params } = useRouteParams();
const { formatDateTime } = useFormatDateTime();
const { formatDecimal } = useDecimal();

const breadcrumbs = computed<BreadcrumbItem[]>(() => [
    {
        title: 'Dashboard',
        href: dashboard().url,
    },
    {
        title: 'Transactions',
        href: index().url,
    },
]);

const columns = computed<ColumnDef<DataType>[]>(() => [
    {
        accessorKey: 'actions',
        header: () => 'Actions',
        cell: ({ row }) => {
            const transaction = row.original;

            return h('div', { class: 'flex items-center gap-2' }, [
                h(ActionButton, {
                    text: 'Edit',
                    href: edit({ transaction: transaction.id }).url,
                    icon: Pencil,
                }),
                h(
                    DeleteDialog,
                    {
                        title: `Delete ${transaction.name}`,
                        route: destroy({ transaction: transaction.id }).url,
                        asChild: false,
                    },
                    () =>
                        h(ActionButton, {
                            variant: 'destructive',
                            text: 'Delete',
                            icon: Trash2,
                        }),
                ),
            ]);
        },
    },
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
        accessorKey: 'categories',
        header: () => h('div', null, 'Categories'),
        cell: ({ row }) => {
            const { categories } = row.original;

            return h(
                'div',
                null,
                categories.map((category) => category.name).join(', '),
            );
        },
    },
    {
        accessorKey: 'amount',
        header: () => h('div', { class: 'text-right' }, 'Amount'),
        cell: ({ row }) => {
            const { amount } = row.original;

            return h(
                'div',
                { class: ['text-right', +amount < 0 && 'text-destructive'] },
                formatDecimal(amount),
            );
        },
        footer: () =>
            h(
                'div',
                {
                    class: [
                        'text-right',
                        props.report.total_amount < 0 && 'text-destructive',
                    ],
                },
                formatDecimal(props.report.total_amount),
            ),
    },
    {
        accessorKey: 'remark',
        header: () => h('div', null, 'Remark'),
        cell: ({ row }) => h('div', null, row.getValue('remark')),
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

const columnVisibility = computed<VisibilityState>(() => ({}));

const filter = ref<Filter>({
    entries: params.get('entries') ?? '10',
    search: params.get('search') ?? undefined,
    start_date: params.get('start_date') ?? undefined,
    end_date: params.get('end_date') ?? undefined,
});

const state = reactive<{
    open: boolean;
    date: {
        start?: Date;
        end?: Date;
    };
}>({
    open: false,
    date: {
        start: filter.value.start_date
            ? new Date(filter.value.start_date)
            : undefined,
        end: filter.value.end_date
            ? new Date(filter.value.end_date)
            : undefined,
    },
});

const search = () =>
    router.visit(
        index({
            query: pickBy(filter.value),
        }),
        {
            preserveScroll: true,
            preserveState: true,
        },
    );

const reset = () => router.visit(index());

const form = useForm<{
    name: string;
    categories: TransactionCategory['id'][];
    amount?: number;
    remark: string;
    expense: boolean;
}>({
    name: '',
    categories: [],
    amount: undefined,
    remark: '',
    expense: true,
});

const submit = () =>
    form.post(store().url, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: handleSuccess,
    });

const handleSuccess = () => {
    state.open = false;
    form.reset();
};

watch(
    () => state.date,
    (newDate) => {
        filter.value.start_date = newDate.start?.toISOString();
        filter.value.end_date = newDate.end?.toISOString();
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
            <FilterCard @search="search" @reset="reset">
                <FilterInput
                    label="Name"
                    placeholder="Search ID, Name, Remark"
                    v-model:model-value="filter.search"
                />
                <FilterRangeCalendar
                    label="Transaction Date"
                    v-model:model-value="state.date"
                />
            </FilterCard>
            <div class="flex flex-wrap items-center justify-end gap-2">
                <Dialog v-model:open="state.open">
                    <DialogTrigger as-child>
                        <Button type="button" class="cursor-pointer">
                            Create
                        </Button>
                    </DialogTrigger>

                    <DialogContent class="sm:max-w-[425px]">
                        <DialogHeader>
                            <DialogTitle>Create Transaction</DialogTitle>
                            <DialogDescription> </DialogDescription>
                        </DialogHeader>
                        <form
                            @submit.prevent="submit"
                            class="space-y-3"
                            disable-while-processing
                            reset-on-success
                            :options="{
                                preserveScroll: true,
                                preserveState: true,
                            }"
                            @success="handleSuccess"
                        >
                            <div class="grid w-full items-center gap-1.5">
                                <Label for="name">Name</Label>
                                <Input
                                    v-model:model-value="form.name"
                                    name="name"
                                    type="text"
                                    placeholder="Enter Name"
                                />
                                <p
                                    v-if="form.errors.name"
                                    class="text-destructive"
                                >
                                    {{ form.errors.name }}
                                </p>
                            </div>
                            <div class="grid w-full items-center gap-1.5">
                                <Label for="categories">Categories</Label>
                                <Combobox
                                    v-model:model-value="form.categories"
                                    name="categories"
                                    :options="options.select.categories"
                                    placeholder="Select Categories"
                                    command-placeholder="Select Categories"
                                    multiple
                                />
                                <p
                                    v-if="form.errors.categories"
                                    class="text-destructive"
                                >
                                    {{ form.errors.categories }}
                                </p>
                            </div>
                            <div class="grid w-full items-center gap-1.5">
                                <Label for="amount">Amount</Label>
                                <Input
                                    v-model:model-value="form.amount"
                                    name="amount"
                                    type="number"
                                    step=".01"
                                    placeholder="Enter Amount"
                                />
                                <p
                                    v-if="form.errors.amount"
                                    class="text-destructive"
                                >
                                    {{ form.errors.amount }}
                                </p>
                            </div>
                            <div class="grid w-full items-center gap-1.5">
                                <Label for="remark">Remark</Label>
                                <Textarea
                                    v-model:model-value="form.remark"
                                    name="remark"
                                    placeholder="Enter Remark"
                                />
                                <p
                                    v-if="form.errors.remark"
                                    class="text-destructive"
                                >
                                    {{ form.errors.remark }}
                                </p>
                            </div>
                            <div class="flex items-center space-x-2">
                                <Switch
                                    name="expense"
                                    v-model:model-value="form.expense"
                                />
                                <Label for="expense">Expense</Label>
                            </div>
                            <div
                                class="flex flex-wrap items-center justify-end gap-2"
                            >
                                <DialogClose as-child>
                                    <Button
                                        class="cursor-pointer"
                                        variant="secondary"
                                    >
                                        Cancel
                                    </Button>
                                </DialogClose>
                                <Button
                                    type="submit"
                                    class="cursor-pointer"
                                    :disabled="form.processing"
                                >
                                    <Loader
                                        v-if="form.processing"
                                        class="animate-spin"
                                    />
                                    Create
                                </Button>
                            </div>
                        </form>
                    </DialogContent>
                </Dialog>
            </div>
            <DataTable
                v-model:model-value="filter"
                :columns="columns"
                :paginate-data="transactions"
                :column-visibility="columnVisibility"
                @search="search"
            />
        </div>
    </AppLayout>
</template>
