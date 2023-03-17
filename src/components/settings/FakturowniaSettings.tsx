import { FC } from 'react';
import { FakturowniaSettingsProps } from './Settings.types';
import { FormFields } from '../../modules/core/components/Form/FormFields';
import { useFormData } from '../../modules/hooks/useFormData';

export const FakturowniaSettings: FC<FakturowniaSettingsProps> = ({ settings }) => {
    const [fields] = useFormData(settings);


    return (<FormFields fields={fields} />);
}