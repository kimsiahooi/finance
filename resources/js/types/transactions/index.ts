export type TransactionTypeLabel = 'Expense' | 'Income';
export type TransactionType = 'EXPENSE' | 'INCOME';

export interface Transaction {
    readonly id: number;
    name: string;
    remark: string | null;
    type: TransactionTypeLabel;
    amount: string;
    transaction_at: Date;
    user_id: number;
    created_at: Date | null;
    updated_at: Date | null;
    deleted_at: Date | null;
}
