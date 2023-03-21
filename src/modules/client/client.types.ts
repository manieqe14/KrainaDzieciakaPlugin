import { Order } from '../../store/store.types';
import FakturowniaClient from '../fakturownia/FakturowniaClient';
import { SettingField } from '../hooks/hooks.types';

export type ClientConfig = {
    ajaxUrl: string,
    ajaxPost: string,
    inpost_token: string
}

export type StoreGeneralDeps = {
    fakturownia: FakturowniaClient;
}

type FakturowniaFields = 'token' | 'sandbox';

export type GeneralSettings = {
    fakturownia?: SettingField<FakturowniaFields>[]
}

export interface ClientInterface {
   url?: string;
   fetchOrders: () => Promise<Order[]>;
   fetchGeneralSettings: () => Promise<StoreGeneralDeps>;
   saveGeneralSettings: (items: Partial<GeneralSettings>) => Promise<void>;
   isEqual: (items: Partial<GeneralSettings>) => boolean;
}

