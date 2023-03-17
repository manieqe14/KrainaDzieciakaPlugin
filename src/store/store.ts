import { Order, StoreDependencies } from './store.types';
import { ClientInterface, GeneralSettings, StoreGeneralDeps } from '../modules/client/client.types';
import { makeAutoObservable } from 'mobx';
import FakturowniaClient from '../modules/fakturownia/FakturowniaClient';
import UIStore from './ui/UIStore';

class Store {
    orders: Order[] = [];
    ui: UIStore;
    private client: ClientInterface;
    private fakturowniaClient?: FakturowniaClient;

    constructor(dependencies: StoreDependencies) {
        makeAutoObservable(this, {}, { autoBind: true })

        this.client = dependencies.client;
        this.ui = dependencies.uiStore;
    }

    public *init(): Generator<Generator<Promise<Order[]>, void, Order[]> | Promise<StoreGeneralDeps>, void, StoreGeneralDeps> {
        yield this.fetchOrders();
        const { fakturownia }: StoreGeneralDeps = yield this.client.fetchGeneralSettings();
        this.fakturowniaClient = fakturownia;
    }


    private *fetchOrders(): Generator<Promise<Order[]>, void, Order[]> {
        try {
            this.orders = yield this.client.fetchOrders();
        } catch (error) {
            console.info(error);
        }
    }

    get fakturowniaSettings(): GeneralSettings['fakturownia'] | null {
        return this.fakturowniaClient?.settings ?? null;
    }
}

export default Store;