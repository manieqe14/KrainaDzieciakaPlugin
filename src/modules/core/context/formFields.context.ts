import { createContext, useContext } from 'react';
import { FormFieldProps } from '../components/Form/FormFIelds.types';

const context = createContext([] as FormFieldProps[]);

export const useFormFields = () => {
    const formContext = useContext(context);

    if(formContext === null) {
        throw new Error('useFromFields should be used inside wrapped component!');
    }

    return formContext;
}

export default context.Provider;