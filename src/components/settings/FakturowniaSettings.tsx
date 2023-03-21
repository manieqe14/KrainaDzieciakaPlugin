import { FC } from 'react';
import { FakturowniaSettingsProps } from './Settings.types';
import { FormFields } from '../../modules/core/components/Form/FormFields';
import { Form } from '../../modules/core/components/Form/Form';

export const FakturowniaSettings: FC<FakturowniaSettingsProps> = ({ settings }) => {

    return (<Form controls={settings}>
        <FormFields />
    </Form>);
}