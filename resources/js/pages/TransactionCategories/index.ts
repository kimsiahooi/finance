import type { TransactionCategory } from '@/types/transaction-categories';

export interface TransactionCategoryWithSum extends TransactionCategory {
    transactions_sum_amount: string | null;
}
