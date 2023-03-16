import { OrderStatus } from './store.types';

export const OrderColours: Record<OrderStatus, string> = {
    processing: 'rgba(255, 0, 0, 0.3)',
    completed: 'rgba(60, 179, 113, 0.3)',
    'on-hold': 'rgba(255, 165, 0, 0.3)'
}