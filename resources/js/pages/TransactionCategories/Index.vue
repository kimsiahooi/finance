<script setup lang="ts">
import { ActionButton } from '@/components/shared/custom/action';
import { FilterCard, FilterInput } from '@/components/shared/custom/filter';
import { DeleteDialog } from '@/components/shared/dialog';
import { PaginateData } from '@/components/shared/pagination';
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
import { Textarea } from '@/components/ui/textarea';
import { useDecimal } from '@/composables/useDecimal';
import { useRouteParams } from '@/composables/useRouteParams';
import AppLayout from '@/layouts/AppLayout.vue';
import AppMainLayout from '@/layouts/AppMainLayout.vue';
import { dashboard } from '@/routes';
import { destroy, edit, index, store } from '@/routes/transaction-categories';
import { BreadcrumbItem } from '@/types';
import { Filter } from '@/types/shared';
import { Form, Head, router } from '@inertiajs/vue3';
import { ColumnDef, VisibilityState } from '@tanstack/vue-table';
import { pickBy } from 'lodash-es';
import { Loader, Pencil, Trash2 } from 'lucide-vue-next';
import { computed, h, reactive, ref } from 'vue';
import { TransactionCategoryWithSum } from '.';

type DataType = TransactionCategoryWithSum;

defineOptions({
    layout: AppMainLayout,
});

const props = defineProps<{
    categories: PaginateData<DataType[]>;
    report: {
        total_amount: number;
    };
}>();

const { params } = useRouteParams();
const { formatDecimal } = useDecimal();

const breadcrumbs = computed<BreadcrumbItem[]>(() => [
    {
        title: 'Dashboard',
        href: dashboard().url,
    },
    {
        title: 'Transaction Categories',
        href: index().url,
    },
]);

const columns = computed<ColumnDef<DataType>[]>(() => [
    {
        accessorKey: 'actions',
        header: () => 'Actions',
        cell: ({ row }) => {
            const category = row.original;

            return h('div', { class: 'flex items-center gap-2' }, [
                h(ActionButton, {
                    text: 'Edit',
                    href: edit({ category: category.id }).url,
                    icon: Pencil,
                }),
                h(
                    DeleteDialog,
                    {
                        title: `Delete ${category.name}`,
                        route: destroy({ category: category.id }).url,
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
        header: () => 'Name',
        cell: ({ row }) => h('div', null, row.getValue('name')),
    },
    {
        accessorKey: 'description',
        header: () => 'Description',
        cell: ({ row }) => h('div', null, row.getValue('description')),
    },
    {
        accessorKey: 'transactions_sum_amount',
        header: () => h('div', { class: 'text-right' }, 'Total Amount'),
        cell: ({ row }) => {
            const { transactions_sum_amount } = row.original;
            return h(
                'div',
                {
                    class: [
                        'text-right',
                        transactions_sum_amount &&
                            +transactions_sum_amount < 0 &&
                            'text-destructive',
                    ],
                },
                formatDecimal(row.getValue('transactions_sum_amount')),
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
]);

const columnVisibility = computed<VisibilityState>(() => ({}));

const filter = ref<Filter>({
    entries: params.get('entries') ?? '10',
    search: params.get('search') ?? undefined,
});

const state = reactive({
    open: false,
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

const handleSuccess = () => {
    state.open = false;
};
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
                    placeholder="Search ID, Name, Description"
                    v-model:model-value="filter.search"
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
                            <DialogTitle>
                                Create Transaction Category
                            </DialogTitle>
                            <DialogDescription> </DialogDescription>
                        </DialogHeader>
                        <Form
                            v-bind="store.form()"
                            #default="{ errors, processing }"
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
                                    name="name"
                                    type="text"
                                    placeholder="Enter Name"
                                />
                                <p v-if="errors.name" class="text-destructive">
                                    {{ errors.name }}
                                </p>
                            </div>
                            <div class="grid w-full items-center gap-1.5">
                                <Label for="description">Description</Label>
                                <Textarea
                                    name="description"
                                    placeholder="Enter Description"
                                />
                                <p
                                    v-if="errors.description"
                                    class="text-destructive"
                                >
                                    {{ errors.description }}
                                </p>
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
                                    :disabled="processing"
                                >
                                    <Loader
                                        v-if="processing"
                                        class="animate-spin"
                                    />
                                    Create
                                </Button>
                            </div>
                        </Form>
                    </DialogContent>
                </Dialog>
            </div>
            <DataTable
                v-model:model-value="filter"
                :columns="columns"
                :paginate-data="categories"
                :column-visibility="columnVisibility"
                @search="search"
            />
        </div>
    </AppLayout>
</template>
