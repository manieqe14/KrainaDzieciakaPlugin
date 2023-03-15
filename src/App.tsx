import React from 'react';
import './App.css';
import { observer } from 'mobx-react-lite'
import { useStore } from './store/store.context';

function App() {

    const { orders } = useStore();

    return (<>
        <div>Tu bedzie react APP</div>
        {orders?.map(item => (<div>{item.id} {item.status}</div>))}
    </>);
}

export default observer(App);
