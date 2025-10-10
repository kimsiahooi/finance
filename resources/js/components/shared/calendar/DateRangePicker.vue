<script setup lang="ts">
import { Button } from '@/components/ui/button';
import {
    Popover,
    PopoverContent,
    PopoverTrigger,
} from '@/components/ui/popover';
import { RangeCalendar } from '@/components/ui/range-calendar';
import { useCalendarDate } from '@/composables/useCalendarDate';
import { cn } from '@/lib/utils';
import { DateFormatter, getLocalTimeZone } from '@internationalized/date';
import { CalendarIcon } from 'lucide-vue-next';
import { DateRange } from 'reka-ui';
import { Ref, ref, watch } from 'vue';

const df = new DateFormatter('en-US', {
    dateStyle: 'medium',
});

const model = defineModel<{ start?: Date; end?: Date }>();

const { formatCalendarDate } = useCalendarDate();

const calendarDate = ref({
    start: formatCalendarDate(model.value?.start),
    end: formatCalendarDate(model.value?.end),
}) as Ref<DateRange>;

const formateDateRange = (
    dateRange: DateRange,
): { start?: Date; end?: Date } => ({
    start: dateRange.start?.toDate(getLocalTimeZone()),
    end: dateRange.end?.toDate(getLocalTimeZone()),
});

watch(
    calendarDate,
    (newDate) => {
        if (model.value) {
            model.value = formateDateRange(newDate);
        }
    },
    { deep: true },
);
</script>

<template>
    <Popover>
        <PopoverTrigger as-child>
            <Button
                variant="outline"
                :class="
                    cn(
                        'w-full justify-start text-left font-normal',
                        (!calendarDate.start || !calendarDate.end) &&
                            'text-muted-foreground',
                    )
                "
            >
                <CalendarIcon class="mr-2 h-4 w-4" />
                <template v-if="calendarDate.start">
                    <template v-if="calendarDate.end">
                        {{
                            df.format(
                                calendarDate.start.toDate(getLocalTimeZone()),
                            )
                        }}
                        -
                        {{
                            df.format(
                                calendarDate.end.toDate(getLocalTimeZone()),
                            )
                        }}
                    </template>

                    <template v-else>
                        {{
                            df.format(
                                calendarDate.start.toDate(getLocalTimeZone()),
                            )
                        }}
                    </template>
                </template>
                <template v-else> Pick a date </template>
            </Button>
        </PopoverTrigger>
        <PopoverContent class="w-auto p-0">
            <RangeCalendar
                v-model:model-value="calendarDate"
                initial-focus
                :number-of-months="2"
            />
        </PopoverContent>
    </Popover>
</template>
