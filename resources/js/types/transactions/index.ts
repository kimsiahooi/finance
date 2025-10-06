import type { TransactionCategory } from '@/types/transaction-categories';
import type { User } from '@/types/users';

export interface Transaction {
    readonly id: number;
    user_id: User['id'];
    transaction_category_id: TransactionCategory['id'] | null;
    amount: string;
    remark: string | null;
    transactioned_at: Date;
    created_at: Date | null;
    updated_at: Date | null;
    deleted_at: Date | null;
}
