<script setup lang="ts">
import {
    FilterCalendar,
    FilterCard,
    FilterInput,
} from '@/components/shared/custom/filter';
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
import { pickBy } from 'lodash-es';
import { Loader } from 'lucide-vue-next';
import { computed, h, reactive, ref, watch } from 'vue';

defineOptions({
    layout: AppMainLayout,
});

const props = defineProps<{
    transactions: PaginateData<Transaction[]>;
    report: {
        total_amount: number;
    };
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
                { class: props.report.total_amount < 0 && 'text-destructive' },
                props.report.total_amount.toLocaleString(undefined, {
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

const columnVisibility = computed<VisibilityState>(() => ({}));

const filter = ref<Filter>({
    entries: params.get('entries') ?? '10',
    search: params.get('search') ?? undefined,
    start_date: params.get('start_date') ?? undefined,
    end_date: params.get('end_date') ?? undefined,
});

const state = reactive<{
    open: boolean;
    start_date?: Date;
    end_date?: Date;
}>({
    open: false,
    start_date: filter.value.start_date
        ? new Date(filter.value.start_date)
        : undefined,
    end_date: filter.value.end_date
        ? new Date(filter.value.end_date)
        : undefined,
});

const search = () =>
    router.visit(
        TransactionRoute.index({
            query: pickBy(filter.value),
        }),
        {
            preserveScroll: true,
            preserveState: true,
        },
    );

const reset = () =>
    router.visit(TransactionRoute.index(), {
        preserveScroll: true,
        preserveState: true,
    });

const handleSuccess = () => {
    state.open = false;
};

watch(
    () => state.start_date,
    (newStartDate) => {
        filter.value.start_date = newStartDate?.toISOString();
    },
);

watch(
    () => state.end_date,
    (newEndDate) => {
        filter.value.end_date = newEndDate?.toISOString();
    },
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
                <FilterCalendar
                    label="Transaction Start Date"
                    placeholder="Select Transaction Start Date"
                    v-model:model-value="state.start_date"
                />
                <FilterCalendar
                    label="Transaction End Date"
                    placeholder="Select Transaction End Date"
                    v-model:model-value="state.end_date"
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
                @search="search"
            />
        </div>
    </AppLayout>
</template>
