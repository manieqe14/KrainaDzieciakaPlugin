import { useEffect, useState } from 'react';
import { FormFieldProps } from '../core/components/Form/FormFIelds.types';
import { SettingField } from './hooks.types';
import { GeneralSettingItem } from '../../components/settings/Settings.types';


export function useFormData<T extends string>(config: SettingField<T>[], handleChange: (item: GeneralSettingItem) => void) {

    const [values, setValues] = useState(config);

    useEffect(() => {
        handleChange(values as GeneralSettingItem);
    }, [values]);

    const data: FormFieldProps[] = values.map(value => {
        const setValue = (newValue: typeof value.value) => {
            setValues((prev) => { 

                return prev.map(item => item.name === value.name ? ({...item, value: newValue }) : item)
            });
        }

        return {
            ...value,
            type: typeof value.value === 'boolean' ? 'select' : 'input',
            onChange: setValue
        }
    });


    return [data];
}