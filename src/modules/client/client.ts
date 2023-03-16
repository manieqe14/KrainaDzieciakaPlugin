import { ClientInterface, GeneralSettings } from './client.types';
import axios from 'axios';
import { WP_ACTIONS } from '../constants';
import { Order } from '../../store/store.types';

export class Client implements ClientInterface {
    url: string | undefined;
    generalSettings?: GeneralSettings;

    constructor(ajaxUrl: string) {
        this.url = ajaxUrl;
    }

    async fetchOrders(): Promise<Order[]> {
        const { data } = await axios.post<Order[]>(`${this.url}?action=${WP_ACTIONS.FETCH_ORDERS}`);
        return data;
    }

    async fetchGeneralSettings(): Promise<void> {
        const { data } = await axios.get<GeneralSettings>(`${this.url}?action=${WP_ACTIONS.GENERAL_CONFIG}`);
        this.generalSettings = data;
    }
}