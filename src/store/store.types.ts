import { ClientInterface } from '../modules/client/client.types';

export interface StoreDependencies {
    client: ClientInterface;
}

type Shipping = {
    address_1: string,
    address_2: string,
}
export type Order = {
    id: number,
    date_created: string,
    shipping: Shipping,
    status: "processing" | "completed" | "on-hold",
    phone: string
}