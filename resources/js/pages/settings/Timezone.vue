<script setup lang="ts">
import HeadingSmall from '@/components/HeadingSmall.vue';
import { FormButton, FormCombobox } from '@/components/shared/custom/form';
import { SelectOption } from '@/components/shared/select';
import { Alert, AlertDescription, AlertTitle } from '@/components/ui/alert';
import { usePage } from '@/composables/usePage';
import AppLayout from '@/layouts/AppLayout.vue';
import AppMainLayout from '@/layouts/AppMainLayout.vue';
import SettingsLayout from '@/layouts/settings/Layout.vue';
import { edit, update } from '@/routes/timezone';
import { type BreadcrumbItem } from '@/types';
import { Head, useForm } from '@inertiajs/vue3';
import { AlarmClockPlus } from 'lucide-vue-next';
import Timezones, { TimeZone } from 'timezones-list';
import { computed, reactive, watch } from 'vue';

defineOptions({ layout: AppMainLayout });

const { page } = usePage();

const breadcrumbItems: BreadcrumbItem[] = [
    {
        title: 'Timezone',
        href: edit().url,
    },
];

const userTimezone = computed<string>(
    () =>
        page.props.auth.user.timezone?.code ??
        Intl.DateTimeFormat().resolvedOptions().timeZone,
);

const options = computed<SelectOption<TimeZone['tzCode']>[]>(() =>
    Timezones.map((timezone) => ({
        name: timezone.label,
        value: timezone.tzCode,
        is_default: timezone.tzCode === userTimezone.value,
    })),
);

const mapOptions = computed<Record<TimeZone['tzCode'], TimeZone>>(() =>
    Object.fromEntries(
        Timezones.map((timezone) => [timezone.tzCode, timezone]),
    ),
);

const state = reactive({
    timezone: options.value.find((timezone) => timezone.is_default)?.value,
});

const form = useForm({
    timezone: mapOptions.value[state.timezone ?? ''] ?? undefined,
});

const submit = () =>
    form.put(update().url, {
        preserveScroll: true,
        preserveState: true,
    });

watch(
    () => state.timezone,
    (newTimezone) => {
        if (newTimezone) {
            form.timezone = mapOptions.value[newTimezone];
        }
    },
);
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbItems">
        <Head title="Appearance settings" />

        <SettingsLayout>
            <form @submit.prevent="submit" class="space-y-6">
                <HeadingSmall
                    title="Timezone"
                    description="update your timezone"
                />
                <FormCombobox
                    label="Timezone"
                    :options="options"
                    placeholder="Select timezone"
                    v-model:model-value="state.timezone"
                    error-key="timezone"
                />
                <FormButton
                    label="Update"
                    :disabled="form.processing"
                    :loading="form.processing"
                    type="submit"
                />
                <Alert v-if="form.timezone && !page.props.auth.user.timezone">
                    <AlarmClockPlus class="h-4 w-4" />
                    <AlertTitle>Update Timezone Now !</AlertTitle>
                    <AlertDescription>
                        You can update timezone by clicking the Update button
                        above.
                    </AlertDescription>
                </Alert>
            </form>
        </SettingsLayout>
    </AppLayout>
</template>
