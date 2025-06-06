<script setup lang="ts">
import { DateTimePicker } from '@/components/shared/calendar';
import { MultiCombobox } from '@/components/shared/combobox';
import { ErrorMessages } from '@/components/shared/error';
import { Select } from '@/components/shared/select';
import type { SelectOption } from '@/components/shared/select/types';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardFooter, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import AppLayout from '@/layouts/AppLayout.vue';
import AppMainLayout from '@/layouts/AppMainLayout.vue';
import type { BreadcrumbItem } from '@/types';
import type { TransactionCategory, TransactionType } from '@/types/transactions';
import { Head, useForm } from '@inertiajs/vue3';
import { computed } from 'vue';

defineOptions({
    layout: AppMainLayout,
});

const props = defineProps<{
    types: TransactionType[];
    categories: TransactionCategory[];
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: route('dashboard'),
    },
    {
        title: 'Transactions',
        href: route('transactions.index'),
    },
    {
        title: 'Create',
        href: route('transactions.create'),
    },
];

const form = useForm<{
    name: string;
    remark: string;
    categories: number[];
    transaction_type_id: number | '';
    amount: number | '';
    transaction_at: Date;
}>({
    name: '',
    remark: '',
    categories: [],
    transaction_type_id: '',
    amount: '',
    transaction_at: new Date(),
});

const categoryOptions = computed<SelectOption[]>(() => props.categories.map((category) => ({ name: category.name, value: category.id })));
const typeOptions = computed<SelectOption[]>(() => props.types.map((type) => ({ name: type.name, value: type.id })));

const submit = () => form.post(route('transactions.store'));
</script>

<template>
    <Head title="Create Transaction" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <div>
                <form @submit.prevent="submit">
                    <Card class="w-full">
                        <CardHeader>
                            <CardTitle>Create Transaction</CardTitle>
                            <CardDescription></CardDescription>
                        </CardHeader>
                        <CardContent>
                            <div class="grid w-full items-center gap-4">
                                <div class="flex flex-col space-y-1.5">
                                    <Label>Name</Label>
                                    <Input type="text" placeholder="Enter Name" v-model:model-value="form.name" />
                                    <p v-if="form.errors.name" class="text-destructive">{{ form.errors.name }}</p>
                                </div>
                                <div class="flex flex-col space-y-1.5">
                                    <Label>Remark</Label>
                                    <Textarea placeholder="Enter Remark" v-model:model-value="form.remark" />
                                    <p v-if="form.errors.remark" class="text-destructive">{{ form.errors.remark }}</p>
                                </div>
                                <div class="flex w-full flex-col space-y-1.5 overflow-x-hidden">
                                    <Label>Categories</Label>
                                    <MultiCombobox
                                        v-model="form.categories"
                                        :options="categoryOptions"
                                        placeholder="Select Categories"
                                        empty-placeholder="No category found."
                                    />
                                    <ErrorMessages error-key="categories" />
                                </div>
                                <div class="flex flex-col space-y-1.5">
                                    <Label>Type</Label>
                                    <Select
                                        :options="typeOptions"
                                        placeholder="Select type"
                                        v-model="form.transaction_type_id"
                                        trigger-class="w-full"
                                    />
                                    <p v-if="form.errors.transaction_type_id" class="text-destructive">{{ form.errors.transaction_type_id }}</p>
                                </div>
                                <div class="flex flex-col space-y-1.5">
                                    <Label>Amount</Label>
                                    <Input type="number" placeholder="Enter Amount" v-model:model-value.number="form.amount" step=".01" />
                                    <p v-if="form.errors.amount" class="text-destructive">{{ form.errors.amount }}</p>
                                </div>
                                <div class="flex flex-col space-y-1.5">
                                    <Label>Transaction at</Label>
                                    <DateTimePicker v-model="form.transaction_at" />
                                    <p v-if="form.errors.transaction_at" class="text-destructive">{{ form.errors.transaction_at }}</p>
                                </div>
                            </div>
                        </CardContent>
                        <CardFooter class="flex justify-between px-6">
                            <Button type="submit" class="cursor-pointer" :disabled="form.processing">Create</Button>
                        </CardFooter>
                    </Card>
                </form>
            </div>
        </div>
    </AppLayout>
</template>
