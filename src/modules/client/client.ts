import { ClientInterface } from './client.types';
import axios from 'axios';
import { WP_ACTIONS } from '../constants';
import { Order } from '../../store/store.types';

export class Client implements ClientInterface {
    url: string | undefined;

    constructor(ajaxUrl: string) {
        this.url = ajaxUrl;
    }

    async fetchOrders(): Promise<Order[]> {
        const { data } = await axios.post<Order[]>(`${this.url}?action=${WP_ACTIONS.FETCH_ORDERS}`)
        console.info(data);
        return data;
    }
}