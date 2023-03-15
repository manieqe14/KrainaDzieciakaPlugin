import { createContext, useContext } from 'react';
import Store from './store';

const context = createContext({} as Store);

export const useStore = () => {
    const storeContext = useContext(context);

    if (!storeContext) {
        throw new Error('useStore should be wrapped with context provider');
    }

    return storeContext;
}

export default context.Provider;