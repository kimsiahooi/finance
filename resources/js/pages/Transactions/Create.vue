<script setup lang="ts">
import { DateTimePicker } from '@/components/shared/calendar';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardFooter, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import AppLayout from '@/layouts/AppLayout.vue';
import AppMainLayout from '@/layouts/AppMainLayout.vue';
import type { BreadcrumbItem } from '@/types';
import { Head, router, useForm } from '@inertiajs/vue3';

defineOptions({
    layout: AppMainLayout,
});

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
    description: string;
    amount: string | number;
    transaction_at: Date | null;
}>({
    name: '',
    description: '',
    amount: '',
    transaction_at: null,
});

const datetimePickerHandler = (datetime: Date) => {
    form.transaction_at = datetime;
    console.log(form.transaction_at);
};

const submit = () =>
    form.post(route('transactions.store'), {
        onSuccess: () => {
            router.reload();
        },
    });
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
                                    <p v-if="form.errors.name">{{ form.errors.name }}</p>
                                </div>
                                <div class="flex flex-col space-y-1.5">
                                    <Label>Description</Label>
                                    <Textarea placeholder="Enter Description" v-model:model-value="form.description" />
                                    <p v-if="form.errors.description">{{ form.errors.description }}</p>
                                </div>
                                <div class="flex flex-col space-y-1.5">
                                    <Label>Amount</Label>
                                    <Input type="number" placeholder="Enter Name" v-model:model-value.number="form.amount" step=".01" />
                                    <p v-if="form.errors.amount">{{ form.errors.amount }}</p>
                                </div>
                                <div class="flex flex-col space-y-1.5">
                                    <Label>Transaction at</Label>
                                    <DateTimePicker @update:value="datetimePickerHandler" />
                                    <p v-if="form.errors.transaction_at">{{ form.errors.transaction_at }}</p>
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
