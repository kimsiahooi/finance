export interface Timezone {
    readonly id: number;
    label: string;
    name: string;
    code: string;
    utc: string;
    created_at: Date | null;
    updated_at: Date | null;
    deleted_at: Date | null;
}
