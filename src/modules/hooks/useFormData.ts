import { useState } from 'react';
import { FormFieldProps } from '../core/components/Form/FormFIelds.types';
import { SettingField } from './hooks.types';


export function useFormData<T extends string>(config: SettingField<T>[]) {

    const [values, setValues] = useState(config);

    const data: FormFieldProps[] = values.map(value => {
        const setValue = (newValue: typeof value.value) => {
            setValues((prev) => prev.map(item => item.name === value.name ? ({...item, value: newValue }) : item));
        }

        return {
            ...value,
            type: typeof value.value === 'boolean' ? 'input' : 'select',
            onChange: setValue
        }
    });


    return [data];
}