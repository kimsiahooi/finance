<script setup lang="ts">
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
import { Switch } from '@/components/ui/switch';
import { Textarea } from '@/components/ui/textarea';
import { useFormatDateTime } from '@/composables/useFormatDateTime';
import { useRouteParams } from '@/composables/useRouteParams';
import AppLayout from '@/layouts/AppLayout.vue';
import AppMainLayout from '@/layouts/AppMainLayout.vue';
import { dashboard } from '@/routes';
import TransactionRoute from '@/routes/transactions';
import { BreadcrumbItem } from '@/types';
import { Filter } from '@/types/shared';
import { Transaction } from '@/types/transactions';
import { Form, Head, router } from '@inertiajs/vue3';
import { ColumnDef, VisibilityState } from '@tanstack/vue-table';
import { Loader } from 'lucide-vue-next';
import { computed, h, reactive, ref, watch } from 'vue';

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
        href: TransactionRoute.index.url(),
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

const columnVisibility: VisibilityState = {};

const state = reactive({
    open: false,
});

const filter = ref<Filter>({
    entries: params.get('entries') ?? '10',
});

const handleSuccess = () => {
    state.open = false;
};

watch(
    filter,
    (newFilter) => {
        router.visit(TransactionRoute.index({ query: newFilter }), {
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
                        <Form
                            v-bind="TransactionRoute.store.form()"
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
                                <Label for="amount">Amount</Label>
                                <Input
                                    name="amount"
                                    type="number"
                                    step=".01"
                                    placeholder="Enter Amount"
                                />
                                <p
                                    v-if="errors.amount"
                                    class="text-destructive"
                                >
                                    {{ errors.amount }}
                                </p>
                            </div>
                            <div class="grid w-full items-center gap-1.5">
                                <Label for="remark">Remark</Label>
                                <Textarea
                                    name="remark"
                                    placeholder="Enter Remark"
                                />
                                <p
                                    v-if="errors.remark"
                                    class="text-destructive"
                                >
                                    {{ errors.remark }}
                                </p>
                            </div>
                            <div class="flex items-center space-x-2">
                                <Switch name="expense" :default-value="true" />
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
                :paginate-data="transactions"
                :column-visibility="columnVisibility"
            />
        </div>
    </AppLayout>
</template>
