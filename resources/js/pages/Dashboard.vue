<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { ApexOptions } from 'apexcharts';
import { computed, ref } from 'vue';
import VueApexCharts from 'vue3-apexcharts';

interface Transaction {
    transactioned_date: string;
    total_amount: string;
    total_expenses: string;
    total_incomes: string;
}

const props = defineProps<{
    transactions: Transaction[];
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: dashboard().url,
    },
];

const series = computed<ApexOptions['series']>(() => [
    {
        name: 'Expenses',
        data: props.transactions.map(
            (transaction) => +transaction.total_expenses,
        ),
        color: '#f87171',
    },
    {
        name: 'Incomes',
        data: props.transactions.map(
            (transaction) => +transaction.total_incomes,
        ),
    },
]);

const chartOptions = ref<ApexOptions>({
    chart: {
        height: 350,
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
});
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div
            class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4"
        >
            <div class="text-black">
                <VueApexCharts :options="chartOptions" :series="series" />
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
