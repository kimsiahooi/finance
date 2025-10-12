<script setup lang="ts">
import { Select, SelectOption } from '@/components/shared/select';
import { useRouteParams } from '@/composables/useRouteParams';
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import { type BreadcrumbItem } from '@/types';
import { Filter } from '@/types/shared';
import { Head, router } from '@inertiajs/vue3';
import { ApexOptions } from 'apexcharts';
import { pickBy } from 'lodash-es';
import { computed, ref, watch } from 'vue';
import VueApexCharts from 'vue3-apexcharts';

interface Transaction {
    transactioned_date: string;
    total_amount: string;
    total_expenses: string;
    total_incomes: string;
}

const props = defineProps<{
    transactions: Transaction[];
    options: {
        select: {
            periods: SelectOption<string>[];
        };
    };
}>();

const { params } = useRouteParams();

const breadcrumbs = computed<BreadcrumbItem[]>(() => [
    {
        title: 'Dashboard',
        href: dashboard().url,
    },
]);

const series = computed<ApexOptions['series']>(() => [
    {
        name: 'Incomes',
        data: props.transactions.map(
            (transaction) => +transaction.total_incomes,
        ),
        color: '#4ade80',
    },
    {
        name: 'Expenses',
        data: props.transactions.map(
            (transaction) => +transaction.total_expenses,
        ),
        color: '#f87171',
    },
]);

const chartOptions = computed<ApexOptions>(() => ({
    chart: {
        type: 'area',
        zoom: {
            allowMouseWheelZoom: false,
            enabled: false,
        },
        toolbar: {
            show: true,
            tools: {
                download: true,
                selection: false,
                zoom: false,
                zoomin: false,
                zoomout: false,
                pan: false,
            },
            export: {
                scale: undefined,
                width: undefined,
                // csv: {
                //     categoryFormatter: (timestamp) =>
                //         timestamp && formatDateTime(new Date(timestamp)),
                // },
            },
            autoSelected: 'zoom',
        },
    },
    dataLabels: {
        enabled: false,
    },
    stroke: {
        curve: 'smooth',
    },
    xaxis: {
        categories: props.transactions.map(
            (transaction) => transaction.transactioned_date,
        ),
        labels: {
            style: {
                cssClass: 'transactions-xaxis-label',
            },
        },
        // type: 'datetime',
        // labels: {
        //     formatter: (_, timestamp) =>
        //         timestamp
        //             ? (formatDateTime(new Date(timestamp), 'MM-DD hh:mma') ??
        //               '')
        //             : '',
        // },
    },
}));

const filter = ref<Filter>({
    period:
        params.period ??
        props.options.select.periods.find((period) => period.is_default)?.value,
});

watch(
    filter,
    (newFilter) =>
        router.visit(dashboard({ query: pickBy(newFilter) }).url, {
            preserveScroll: true,
            preserveState: true,
        }),
    { deep: true },
);
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div
            class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4"
        >
            <div class="space-y-4">
                <div class="flex flex-wrap items-center justify-end gap-2">
                    <Select
                        :options="options.select.periods"
                        placeholder="Select Period"
                        v-model:model-value="filter.period"
                    />
                </div>
                <div class="text-black">
                    <VueApexCharts :options="chartOptions" :series="series" />
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<style>
@reference "tailwindcss";

.transactions-xaxis-label {
    @apply fill-black/50;
}

.dark .transactions-xaxis-label {
    @apply fill-white/50;
}
</style>
