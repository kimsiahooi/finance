import type { User } from '@/types';

export interface Transaction {
    readonly id: number;
    user_id: User['id'];
    amount: string;
    transactioned_at: Date;
    created_at: Date | null;
    updated_at: Date | null;
    deleted_at: Date | null;
}
