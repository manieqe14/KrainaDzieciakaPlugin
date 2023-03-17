import { SettingFields } from './hooks.types';
import { useState } from 'react';
import { FormFieldsProps } from '../core/components/Form/FormFIelds.types';


export function useFormData(config: SettingFields<string>) {

    const [values, setValues] = useState(config);

    const data: FormFieldsProps['fields'] = values.map(value => {
        const setValue = (newValue: typeof value.value) => {
            setValues((prev) => prev.map(item => item.name === value.name ? ({...item, value: newValue }) : item));
        }

        return {
            ...value,
            onChange: setValue
        }
    });


    return [data];
}