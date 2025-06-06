export interface Transaction {
    readonly id: number;
    name: string;
    remark: string | null;
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

export type TransactionTypeDirection = 'PLUS' | 'MINUS';
export interface TransactionType {
    readonly id: number;
    name: string;
    description: string | null;
    direction: TransactionTypeDirection;
    user_id: number;
    created_at: Date | null;
    updated_at: Date | null;
    deleted_at: Date | null;
}
