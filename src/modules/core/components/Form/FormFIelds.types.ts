import { SettingField } from '../../../hooks/hooks.types';

export type FormFieldProps<T extends string> = {
    field: SettingField<T> & { onChange: (value: string | boolean) => void };
}


export type FormFieldsProps<T extends string = string> = {
    fields: FormFieldProps<T>[]
}