import { FC } from 'react';
import { FormFields } from '../../modules/core/components/Form/FormFields';
import { Form } from '../../modules/core/components/Form/Form';
import { SettingsItemProps } from './Settings.types';

export const SettingsItem: FC<SettingsItemProps> = ({ settings, handleChange }) => {

    return (<Form handleChange={handleChange} controls={settings}>
        <FormFields />
    </Form>);
}