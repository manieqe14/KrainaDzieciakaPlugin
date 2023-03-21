import { FC, PropsWithChildren } from 'react';
import FormFieldsProvider from '../../context/formFields.context'
import { useFormData } from '../../../hooks/useFormData';
import { FormProps } from './FormFIelds.types';

export const Form: FC<PropsWithChildren & FormProps> = ({ children, controls  }) => {

    const [fields] = useFormData(controls);

    return (
        <FormFieldsProvider value={fields} >
            {children}
        </FormFieldsProvider>
    );
}