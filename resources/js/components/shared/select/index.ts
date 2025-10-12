export { default as Select } from './Select.vue';
import type { AcceptableValue } from 'reka-ui';

export interface SelectOption<T extends AcceptableValue> {
    name: string;
    value: T;
    is_default?: boolean;
}
