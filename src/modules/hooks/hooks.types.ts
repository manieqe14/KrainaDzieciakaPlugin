export type SelectField<T extends string = string> = { name: T, value: boolean }
export type InputField<T extends string = string> = { name: T, value: string };

export type SettingField<T extends string = string> = SelectField<T> | InputField<T>;

export type SettingsValues = (SelectField | InputField)['value']