import React from 'react';
import ReactDOM from 'react-dom/client';
import './index.css';
import App from './App';
import reportWebVitals from './reportWebVitals';
import { flowResult } from 'mobx';
import Store from './store/store';
import { Client } from './modules/client/client';
import { WP_ACTIONS } from './modules/constants';
import handlers from './modules/stubs/handlers';
import { setupWorker } from 'msw';
import StoreContext from './store/store.context';



const root = ReactDOM.createRoot(
  document.getElementById('react-spa-container') as HTMLElement
);

const init = async () => {
    if(import.meta.env.DEV) {
        const worker = setupWorker(...handlers);
        await worker.start().catch(() => console.error('Worker error'));
    }

    const client = new Client(WP_ACTIONS.AJAX_URL);
    const store = new Store({ client });
    await flowResult(store.fetchOrders());
    
    return store;
}

init().then((store ) => {
    root.render(
        <React.StrictMode>
            <StoreContext value={store}>
            <App />
            </StoreContext>
        </React.StrictMode>
    );
})

// If you want to start measuring performance in your app, pass a function
// to log results (for example: reportWebVitals(console.log))
// or send to an analytics endpoint. Learn more: https://bit.ly/CRA-vitals
reportWebVitals();
