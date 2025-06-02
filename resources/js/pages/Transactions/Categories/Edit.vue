<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardFooter, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import AppLayout from '@/layouts/AppLayout.vue';
import AppMainLayout from '@/layouts/AppMainLayout.vue';
import type { BreadcrumbItem } from '@/types';
import type { TransactionCategory } from '@/types/transactions';
import { Head, useForm } from '@inertiajs/vue3';

defineOptions({
    layout: AppMainLayout,
});

const props = defineProps<{
    category: TransactionCategory;
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
        title: 'Categories',
        href: route('transactions.categories.index'),
    },
    {
        title: props.category.name,
        href: '#',
    },
    {
        title: 'Edit',
        href: route('transactions.categories.edit', { category: props.category.id }),
    },
];

const form = useForm({
    name: props.category.name,
    description: props.category.description || '',
});

const submit = () => form.put(route('transactions.categories.update', { category: props.category.id }));
</script>

<template>
    <Head title="Edit Transaction Category" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <div>
                <form @submit.prevent="submit">
                    <Card class="w-full">
                        <CardHeader>
                            <CardTitle>Edit Transaction Category</CardTitle>
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
                                    <Label>Description</Label>
                                    <Textarea placeholder="Enter Description" v-model:model-value="form.description" />
                                    <p v-if="form.errors.description" class="text-destructive">{{ form.errors.description }}</p>
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
