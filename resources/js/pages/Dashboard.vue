<script setup lang="ts">
import { useFormatDateTime } from '@/composables/useFormatDateTime';
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import { type BreadcrumbItem } from '@/types';
import { TransactionCategoryWithTransactions } from '@/types/transaction-categories';
import { Head } from '@inertiajs/vue3';
import { ApexOptions } from 'apexcharts';
import { computed, ref } from 'vue';
import VueApexCharts from 'vue3-apexcharts';

const props = defineProps<{
    categories: TransactionCategoryWithTransactions[];
}>();

const { formatDateTime } = useFormatDateTime();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: dashboard().url,
    },
];

const series = computed<ApexOptions['series']>(() =>
    props.categories.map((cat) => ({
        name: cat.name,
        data: cat.transactions.map((transaction) => ({
            x: new Date(transaction.transactioned_at).getTime(),
            y: +transaction.amount,
        })),
    })),
);

const chartOptions = ref<ApexOptions>({
    chart: {
        height: 350,
        type: 'area',
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
                csv: {
                    categoryFormatter: (timestamp) =>
                        timestamp && formatDateTime(new Date(timestamp)),
                },
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
        type: 'datetime',
        labels: {
            formatter: (_, timestamp) =>
                timestamp
                    ? (formatDateTime(new Date(timestamp), 'MM-DD hh:mma') ??
                      '')
                    : '',
        },
    },
});
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div
            class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4"
        >
            <VueApexCharts :options="chartOptions" :series="series" />
        </div>
    </AppLayout>
</template>
