import { CurrentPage } from './UIStore.types';

export default class UIStore {
    currentPage: CurrentPage;
    constructor(page?: CurrentPage) {
        this.currentPage = page === undefined ? "Orders" : page;
    }
}