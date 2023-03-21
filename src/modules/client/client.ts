import { ClientInterface, GeneralSettings, StoreGeneralDeps } from './client.types';
import axios from 'axios';
import { WP_ACTIONS } from '../constants';
import { Order } from '../../store/store.types';
import FakturowniaClient from '../fakturownia/FakturowniaClient';
import { propEq } from 'ramda';

export class Client implements ClientInterface {
    url: string | undefined;
    generalSettings: GeneralSettings = {};

    constructor(ajaxUrl: string) {
        this.url = ajaxUrl;
    }

    async fetchOrders(): Promise<Order[]> {
        const { data } = await axios.post<Order[]>(`${this.url}?action=${WP_ACTIONS.FETCH_ORDERS}`);
        return data;
    }

    async fetchGeneralSettings(): Promise<StoreGeneralDeps> {
        const { data } = await axios.get<GeneralSettings>(`${this.url}?action=${WP_ACTIONS.GENERAL_CONFIG}`);
        this.generalSettings = data;
        return ({
           fakturownia: new FakturowniaClient({ client: this })
        });
    }

    async saveGeneralSettings(items: Partial<GeneralSettings>) {
        this.generalSettings = {...items, ...this.generalSettings } as GeneralSettings;
        const respo = await axios.post<GeneralSettings>(`${this.url}?action=${WP_ACTIONS.GENERAL_CONFIG}`, this.generalSettings);
        console.info(respo);
    }

    public isEqual = (items: Partial<GeneralSettings>): boolean => {
        return !Object.entries(items).some(([key, value]) => {
            const actual = this.generalSettings[key as keyof GeneralSettings];
            if(actual === undefined){
                return false;
            }
           return value.some(setting => {
                const settingFromGeneral = actual.find(propEq('name', setting.name));
                return setting.value !== settingFromGeneral?.value
            })
        });
    }
}
