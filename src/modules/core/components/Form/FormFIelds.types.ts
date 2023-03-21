import { SettingField } from '../../../hooks/hooks.types';

export type FormFieldProps = {
    name: string,
    value: string | boolean,
    type: "select" | "input",
    onChange: (value: string | boolean) => void;
}

export type FormProps = {
    controls: SettingField[]
}