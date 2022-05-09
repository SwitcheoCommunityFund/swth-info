export declare class StdSignDoc {
    readonly chain_id: string;
    readonly account_number: string;
    readonly sequence: string;
    readonly fee: string;
    readonly msgs: string;
    readonly memo: string;
    constructor({ chainId, accountNumber, sequence, fee, msgs, memo, }: {
        chainId: any;
        accountNumber: any;
        sequence: any;
        fee: any;
        msgs: any;
        memo: any;
    });
}
export declare class Fee {
    readonly amount: string;
    readonly gas: string;
    constructor(amount: any, gas: any);
}
