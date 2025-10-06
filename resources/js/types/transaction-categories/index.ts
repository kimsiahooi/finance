import type { User } from '@/types/users';

export interface TransactionCategory {
    readonly id: number;
    user_id: User['id'];
    name: string;
    description: string | null;
    created_at: Date | null;
    updated_at: Date | null;
    deleted_at: Date | null;
}
