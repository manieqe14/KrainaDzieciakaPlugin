import React, { FC } from 'react';
import { useStore } from '../../store/store.context';
import { OrderItem } from './Order/OrderItem';

export const OrdersList: FC = () => {
    const { orders } = useStore();

    return (<>
            {orders.map(order => <OrderItem data={order} />)}
        </>);
}