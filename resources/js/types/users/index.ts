import type { Timezone } from '@/types/settings/timzones';

export interface User {
    readonly id: number;
    name: string;
    email: string;
    avatar?: string;
    email_verified_at: string | null;
    two_factor_secret: string | null;
    two_factor_recovery_codes: string | null;
    two_factor_confirmed_at: Date | null;
    timezone: Timezone | null;
    timezone_id: Timezone['id'] | null;
    created_at: Date | null;
    updated_at: Date | null;
    deleted_at: Date | null;
}
