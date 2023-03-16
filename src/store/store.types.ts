import { ClientInterface } from '../modules/client/client.types';

export interface StoreDependencies {
    client: ClientInterface;
}

type Shipping = {
    first_name: string,
    last_name: string,
    company: string,
    address_1: string,
    address_2: string,
    city: string,
    state: string,
    postcode: string,
    phone: string
}

type Billing = {
    first_name: string,
    last_name: string,
    company: string,
    address_1: string,
    address_2: string,
    city: string,
    state: string,
    postcode: string,
    country: string,
    email: string,
    phone: string
}

type DateCreated = {
    date: string,
    timezone_type: number,
    timezone: string
}

export type OrderStatus = "processing" | "completed" | "on-hold";

export type Order = {
    id: number,
    total: string;
    currency: string,
    date_created: DateCreated,
    shipping: Shipping,
    status: OrderStatus,
    phone: string,
    billing: Billing,
    shipping_method: string;
}