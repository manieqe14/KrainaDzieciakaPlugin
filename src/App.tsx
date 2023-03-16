import React from 'react';
import './App.css';
import { Navigation } from './components/navigation/Navigation';
import { observer } from 'mobx-react-lite';
import { OrdersList } from './components/orders/OrdersList';
import { useStore } from './store/store.context';
import Settings from './components/settings/Settings';

function App() {
    const { ui } = useStore();

    return (<><Navigation />{ui.currentPage === 'orders' ? <OrdersList/> : <Settings />}</>);
}

export default observer(App);
