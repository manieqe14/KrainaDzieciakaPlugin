import React from 'react';
import './App.css';
import { observer } from 'mobx-react-lite'
import { OrdersList } from './components/orders/OrdersList';

function App() {
    return (<OrdersList />);
}

export default observer(App);
