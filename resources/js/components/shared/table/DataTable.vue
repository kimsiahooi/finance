<script setup lang="ts" generic="TData, TValue">
import { Pagination, type PaginateData } from '@/components/shared/pagination';
import { Select } from '@/components/shared/select';
import {
    Table,
    TableBody,
    TableCell,
    TableFooter,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import { entryOptions } from '@/constants/entries/options';
import { valueUpdater } from '@/lib/utils';
import type { Filter } from '@/types/shared';
import type {
    ColumnDef,
    SortingState,
    VisibilityState,
} from '@tanstack/vue-table';
import {
    FlexRender,
    getCoreRowModel,
    getSortedRowModel,
    useVueTable,
} from '@tanstack/vue-table';
import { ref, watch } from 'vue';

const props = withDefaults(
    defineProps<{
        columns: ColumnDef<TData, TValue>[];
        paginateData: PaginateData<TData[]>;
        columnVisibility?: VisibilityState;
    }>(),
    {
        columnVisibility: () => ({}),
    },
);

const sorting = ref<SortingState>([]);
const columnVisibility = ref<VisibilityState>(props.columnVisibility);

const filterModel = defineModel<Filter>();

const emits = defineEmits<{
    search: [];
}>();

const filter = ref<Filter>(filterModel.value || {});

const table = useVueTable({
    get data() {
        return props.paginateData.data;
    },
    get columns() {
        return props.columns;
    },
    getCoreRowModel: getCoreRowModel(),
    getSortedRowModel: getSortedRowModel(),
    onSortingChange: (updaterOrValue) => valueUpdater(updaterOrValue, sorting),
    onColumnVisibilityChange: (updaterOrValue) =>
        valueUpdater(updaterOrValue, columnVisibility),
    state: {
        get sorting() {
            return sorting.value;
        },
        get columnVisibility() {
            return columnVisibility.value;
        },
    },
});

const entryHandler = () => {
    searchHandler();
};

const searchHandler = () => {
    emits('search');
};

watch(filter, (newFilter) => {
    if (filterModel.value) {
        filterModel.value = newFilter;
    }
});

watch(filterModel, (newFilter) => {
    if (newFilter) {
        filter.value = newFilter;
    }
});
</script>

<template>
    <div class="space-y-3">
        <div class="rounded-md border">
            <Table>
                <TableHeader>
                    <TableRow
                        v-for="headerGroup in table.getHeaderGroups()"
                        :key="headerGroup.id"
                    >
                        <TableHead
                            v-for="header in headerGroup.headers"
                            :key="header.id"
                        >
                            <FlexRender
                                v-if="!header.isPlaceholder"
                                :render="header.column.columnDef.header"
                                :props="header.getContext()"
                            />
                        </TableHead>
                    </TableRow>
                </TableHeader>
                <TableBody>
                    <template v-if="table.getRowModel().rows?.length">
                        <TableRow
                            v-for="row in table.getRowModel().rows"
                            :key="row.id"
                            :data-state="
                                row.getIsSelected() ? 'selected' : undefined
                            "
                        >
                            <TableCell
                                v-for="cell in row.getVisibleCells()"
                                :key="cell.id"
                            >
                                <FlexRender
                                    :render="cell.column.columnDef.cell"
                                    :props="cell.getContext()"
                                />
                            </TableCell>
                        </TableRow>
                    </template>
                    <template v-else>
                        <TableRow>
                            <TableCell
                                :colspan="columns.length"
                                class="h-24 text-center"
                            >
                                No results.
                            </TableCell>
                        </TableRow>
                    </template>
                </TableBody>
                <TableFooter
                    v-if="
                        table.getRowModel().rows?.length &&
                        table
                            .getFooterGroups()
                            .some((group) =>
                                group.headers.some(
                                    (header) =>
                                        !!header.column.columnDef.footer,
                                ),
                            )
                    "
                >
                    <TableRow
                        v-for="row in table.getFooterGroups()"
                        :key="row.id"
                    >
                        <TableCell
                            v-for="header in row.headers"
                            :key="header.id"
                        >
                            <FlexRender
                                :render="header.column.columnDef.footer"
                                :props="header.getContext()"
                            />
                        </TableCell>
                    </TableRow>
                </TableFooter>
            </Table>
        </div>
        <div
            class="flex flex-col items-center justify-end gap-2 space-x-2 py-2 md:flex-row"
        >
            <div class="flex-1">
                <Select
                    v-if="entryOptions"
                    :options="entryOptions"
                    placeholder="Select Entries"
                    v-model:model-value="filter.entries"
                    @update:model-value="entryHandler"
                />
            </div>
            <div
                class="flex flex-col items-center gap-x-8 gap-y-2 md:items-end xl:flex-row xl:items-center"
            >
                <div>
                    <p class="text-muted-foreground">
                        Showing
                        {{ (paginateData.from ?? 0).toLocaleString() }} to
                        {{ (paginateData.to ?? 0).toLocaleString() }} of
                        {{ paginateData.total.toLocaleString() }} entries
                    </p>
                </div>
                <div class="space-x-2">
                    <Pagination :paginate-data="paginateData" />
                </div>
            </div>
        </div>
    </div>
</template>
