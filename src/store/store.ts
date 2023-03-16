import { Order, StoreDependencies } from './store.types';
import { ClientInterface } from '../modules/client/client.types';
import { makeAutoObservable } from 'mobx';

class Store {
    orders: Order[] = [];
    private client: ClientInterface;

    constructor(dependencies: StoreDependencies) {
        makeAutoObservable(this, {}, { autoBind: true })

        this.client = dependencies.client;
    }

    public* fetchOrders(): Generator<Promise<Order[]>, void, Order[]> {
        try {
            this.orders = yield this.client.fetchOrders();
        } catch (error) {
            console.info(error);
        }
    }
}

export default Store;