import { Order } from '../../store/store.types';
import FakturowniaClient from '../fakturownia/FakturowniaClient';
import { SettingFields } from '../hooks/hooks.types';

export type ClientConfig = {
    ajaxUrl: string,
    ajaxPost: string,
    inpost_token: string
}

export type StoreGeneralDeps = {
    fakturownia: FakturowniaClient;
}

type FakturowniaFields = 'token';

export type GeneralSettings = {
    fakturownia: SettingFields<FakturowniaFields>
}

export interface ClientInterface {
   url?: string;
   fetchOrders: () => Promise<Order[]>;
   fetchGeneralSettings: () => Promise<StoreGeneralDeps>;
}

