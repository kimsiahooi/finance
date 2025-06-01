<script setup lang="ts">
import { DateTimePicker } from '@/components/shared/calendar';
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
import type { Transaction, TransactionType } from '@/types/transactions';
import { Head, useForm } from '@inertiajs/vue3';

defineOptions({
    layout: AppMainLayout,
});

const props = defineProps<{
    transaction: Transaction;
    types: SelectOption[];
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
        title: props.transaction.name,
        href: '#',
    },
    {
        title: 'Edit',
        href: route('transactions.edit', { transaction: props.transaction.id }),
    },
];

const form = useForm<{
    name: string;
    remark: string;
    type: TransactionType;
    amount: string | number;
    transaction_at: Date;
}>({
    name: props.transaction.name,
    remark: props.transaction.remark || '',
    type: props.transaction.type,
    amount: props.transaction.amount,
    transaction_at: new Date(props.transaction.transaction_at),
});

const submit = () => form.put(route('transactions.update', { transaction: props.transaction.id }));
</script>

<template>
    <Head title="Edit Transaction" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <div>
                <form @submit.prevent="submit">
                    <Card class="w-full">
                        <CardHeader>
                            <CardTitle>Edit Transaction</CardTitle>
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
                                <div class="flex flex-col space-y-1.5">
                                    <Label>Type</Label>
                                    <Select :options="types" placeholder="Select type" v-model="form.type" trigger-class="w-full" />
                                    <p v-if="form.errors.type" class="text-destructive">{{ form.errors.type }}</p>
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
                            <Button type="submit" class="cursor-pointer" :disabled="form.processing">Update</Button>
                        </CardFooter>
                    </Card>
                </form>
            </div>
        </div>
    </AppLayout>
</template>
