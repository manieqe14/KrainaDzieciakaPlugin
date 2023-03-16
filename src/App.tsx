import React from 'react';
import './App.css';
import { Navigation } from './components/navigation/Navigation';
import { Outlet } from 'react-router-dom';
import { observer } from 'mobx-react-lite';

function App() {

    return (<><Navigation /><Outlet/></>);
}

export default observer(App);
