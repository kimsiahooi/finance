export interface Transaction {
    readonly id: number;
    name: string;
    description: string | null;
    amount: string;
    transaction_at: Date;
    user_id: number;
    created_at: Date | null;
    updated_at: Date | null;
    deleted_at: Date | null;
}
