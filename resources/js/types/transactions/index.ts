export type TransactionTypeDisplay = 'Expense' | 'Income';
export type TransactionType = 'EXPENSE' | 'INCOME';

export interface Transaction {
    readonly id: number;
    name: string;
    remark: string | null;
    type: TransactionType;
    type_display: TransactionTypeDisplay;
    amount: string;
    transaction_at: Date;
    user_id: number;
    created_at: Date | null;
    updated_at: Date | null;
    deleted_at: Date | null;
}

export interface TransactionCategory {
    readonly id: number;
    name: string;
    description: string | null;
    user_id: number;
    created_at: Date | null;
    updated_at: Date | null;
    deleted_at: Date | null;
}

export interface TransactionWithCategories extends Transaction {
    categories: TransactionCategory[];
}
