import React, { FC } from 'react';
import { useStore } from '../../store/store.context';
import { OrderItem } from './Order/OrderItem';
import { AccordionsList } from '../../modules/components/AccordionsList';

export const OrdersList: FC = () => {
    const { orders } = useStore();

    return (<AccordionsList>
            {orders.map(order => <OrderItem data={order} />)}
        </AccordionsList>);
}