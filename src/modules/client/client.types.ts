import { Order } from '../../store/store.types';

export type ClientConfig = {
    ajaxUrl: string,
    ajaxPost: string,
    inpost_token: string
}

export type GeneralSettings = {
    fakturownia: {
        token: string;
    }
}

export interface ClientInterface {
   url?: string;
   fetchOrders: () => Promise<Order[]>;
}

