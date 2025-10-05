export interface User {
    readonly id: number;
    name: string;
    email: string;
    two_factor_secret: string | null;
    two_factor_recovery_codes: string | null;
    two_factor_confirmed_at: Date | null;
    created_at: Date | null;
    updated_at: Date | null;
    deleted_at: Date | null;
}
