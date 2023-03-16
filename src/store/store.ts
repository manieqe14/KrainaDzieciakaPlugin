import { Order, StoreDependencies } from './store.types';
import { ClientInterface } from '../modules/client/client.types';
import { makeAutoObservable } from 'mobx';
import FakturowniaClient from '../modules/fakturownia/FakturowniaClient';
import UIStore from './ui/UIStore';

class Store {
    orders: Order[] = [];
    ui: UIStore;
    private client: ClientInterface;
    fakturowniaClient: FakturowniaClient;

    constructor(dependencies: StoreDependencies) {
        makeAutoObservable(this, {}, { autoBind: true })

        this.client = dependencies.client;
        this.fakturowniaClient = dependencies.fakturowniaClient;
        this.ui = dependencies.uiStore;
    }

    public *fetchOrders(): Generator<Promise<Order[]>, void, Order[]> {
        try {
            this.orders = yield this.client.fetchOrders();
        } catch (error) {
            console.info(error);
        }
    }
}

export default Store;