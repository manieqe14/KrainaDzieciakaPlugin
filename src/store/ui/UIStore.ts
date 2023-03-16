import { CurrentPage } from './UIStore.types';
import { makeAutoObservable } from 'mobx';

export default class UIStore {
    currentPage: CurrentPage;
    constructor(page?: CurrentPage) {
        makeAutoObservable(this, {}, { autoBind: true });
        this.currentPage = page === undefined ? "orders" : page;
    }
}