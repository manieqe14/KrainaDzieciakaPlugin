import { InputField, SelectField, SettingField } from '../hooks/hooks.types';

export const isSelectField = (field: SettingField): field is SelectField => typeof(field.value) === 'boolean'

export const isInputField = (field: SettingField): field is InputField => typeof(field.value) === 'string'