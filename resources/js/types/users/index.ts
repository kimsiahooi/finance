import type { Transaction } from '@/types/transactions';

export interface User {
    readonly id: number;
    name: string;
    email: string;
    email_verified_at: Date | null;
    created_at: Date | null;
    updated_at: Date | null;
    deleted_at: Date | null;
}

export interface UserTransactions extends User {
    transactions: Transaction[];
}
