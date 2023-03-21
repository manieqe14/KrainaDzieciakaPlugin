import { GeneralSettings } from '../../modules/client/client.types';

export type GeneralSettingItem = GeneralSettings[keyof GeneralSettings];

export type SettingsItemProps = {
    settings: GeneralSettingItem;
    handleChange: (item: GeneralSettingItem) => void;
}