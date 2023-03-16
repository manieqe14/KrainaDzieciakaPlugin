import { OrderStatus } from '../store.types';
import { Page } from './UIStore.types';

export const OrderColours: Record<OrderStatus, string> = {
    processing: 'rgba(255, 0, 0, 0.3)',
    completed: 'rgba(60, 179, 113, 0.3)',
    'on-hold': 'rgba(255, 165, 0, 0.3)'
}

export const Pages: Record<string, Page> = {
    orders: {
        title: "Orders",
        path: "/orders",
    },
    settings: {
        title: "Settings",
        path: "/settings",
    }
};