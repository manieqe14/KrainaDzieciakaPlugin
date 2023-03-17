import React, { FC } from 'react';
import { useStore } from '../../store/store.context';
import { OrderItem } from './Order/OrderItem';
import { AccordionsList } from '../../modules/core/components/AccordionsList/AccordionsList';

export const OrdersList: FC = () => {
    const { orders } = useStore();

    return (<AccordionsList>
            { orders.map(order => <OrderItem key={order.id} data={order} />) }
        </AccordionsList>);
}