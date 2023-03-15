import React from 'react';
import './App.css';
import { observer } from 'mobx-react-lite'
import { useStore } from './store/store.context';
import { OrderItem } from './components/orders/order/OrderItem';

function App() {

    const { orders } = useStore();

    return (<>
        <div>Tu bedzie react APP</div>
        {orders?.map(order => <OrderItem data={order}/>)}
    </>);
}

export default observer(App);
