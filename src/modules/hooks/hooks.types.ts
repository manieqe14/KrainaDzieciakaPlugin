type WithValue<T, K> = { name: K, value: T }

export type SelectField<T extends string> = WithValue<boolean, T>
export type InputField<T extends string> = WithValue<string, T>;

export type SettingField<T extends string = string> = SelectField<T> | InputField<T>;

export type SettingFields<T extends string> = SelectField<T>[];

export type SettingsValues = (SelectField | InputField)['value']