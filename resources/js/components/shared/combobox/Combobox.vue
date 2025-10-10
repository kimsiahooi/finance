<script setup lang="ts" generic="T extends AcceptableValue">
import { SelectOption } from '@/components/shared/select';
import { Button } from '@/components/ui/button';
import {
    Command,
    CommandEmpty,
    CommandGroup,
    CommandInput,
    CommandItem,
    CommandList,
} from '@/components/ui/command';
import {
    Popover,
    PopoverContent,
    PopoverTrigger,
} from '@/components/ui/popover';
import { Check, ChevronDown } from 'lucide-vue-next';
import { AcceptableValue } from 'reka-ui';
import { computed, onMounted, onUnmounted, ref, useTemplateRef } from 'vue';

const props = withDefaults(
    defineProps<{
        placeholder: string;
        options: SelectOption<T>[];
        multiple?: boolean;
        showMax?: number;
    }>(),
    {
        showMax: 2,
    },
);

const model = defineModel<T | T[]>();
const openModel = defineModel<boolean>('open');
const buttonEl = useTemplateRef<{ button: { $el: HTMLButtonElement } }>(
    'buttonEl',
);
const buttonWidth = ref(300);

const resize = () => {
    const width = buttonEl.value?.button.$el.offsetWidth;

    if (width) {
        buttonWidth.value = width;
    }
};

const optionIncluded = computed(
    () => (option: SelectOption<T>) =>
        Array.isArray(model.value)
            ? model.value?.includes(option.value)
            : model.value === option.value,
);

const selectedOptionLabels = computed(() =>
    props.options
        .filter((option) =>
            Array.isArray(model.value)
                ? model.value.includes(option.value)
                : model.value === option.value,
        )
        .map((option) => option.name),
);

const computedPlaceholder = computed(
    () =>
        `${props.placeholder}${selectedOptionLabels.value.length ? ': ' : ''}${selectedOptionLabels.value.length < props.showMax ? selectedOptionLabels.value.join(', ') : `${selectedOptionLabels.value.length} selected`}`,
);

const select = () => {
    if (!props.multiple && openModel.value) {
        openModel.value = false;
    }
};

onMounted(() => {
    resize();
    window.addEventListener('resize', resize);
});

onUnmounted(() => {
    window.removeEventListener('resize', resize);
});
</script>

<template>
    <Popover v-model:open="openModel">
        <PopoverTrigger as-child>
            <Button
                variant="outline"
                class="w-full flex-1 justify-between font-normal"
                ref="buttonEl"
                :class="[
                    model ? 'text-foreground' : 'text-muted-foreground',
                    options.length ? 'cursor-pointer' : 'cursor-not-allowed',
                ]"
                :disabled="!options.length"
                v-bind="$attrs"
            >
                {{ computedPlaceholder }}
                <ChevronDown />
            </Button>
        </PopoverTrigger>
        <PopoverContent
            class="p-0"
            align="center"
            :style="{
                width: `${buttonWidth}px`,
            }"
        >
            <Command v-model:model-value="model" :multiple="multiple">
                <CommandInput placeholder="Search..." />
                <CommandList>
                    <CommandEmpty>No results found.</CommandEmpty>
                    <CommandGroup>
                        <CommandItem
                            v-for="option in options"
                            :key="option.name"
                            :value="option.value"
                            class="justify-between"
                            @select="select"
                        >
                            {{ option.name }}
                            <Check v-if="optionIncluded(option)" />
                        </CommandItem>
                    </CommandGroup>
                </CommandList>
            </Command>
        </PopoverContent>
    </Popover>
</template>
