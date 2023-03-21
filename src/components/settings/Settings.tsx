import { SettingsItem } from './SettingsItem';
import { Box, Button, Tab, Tabs, Typography } from '@mui/material';
import { FC, SyntheticEvent, useState } from 'react';
import { observer } from 'mobx-react-lite';
import { useStore } from '../../store/store.context';
import { GeneralSettingItem } from './Settings.types';

interface TabPanelProps {
    children?: React.ReactNode;
    index: number;
    value: number;
}

function TabPanel(props: TabPanelProps) {
    const { children, value, index, ...other } = props;

    return (
        <div
            role="tabpanel"
            hidden={value !== index}
            id={`simple-tabpanel-${index}`}
            aria-labelledby={`simple-tab-${index}`}
            {...other}
        >
            {value === index && (
                <Box sx={{ p: 3 }}>
                    <Typography>{children}</Typography>
                </Box>
            )}
        </div>
    );
}

const Settings: FC = () => {
    const [activeTab, setActiveTab] = useState(0);
    const [save, setSave] = useState(false);
    const { fakturowniaSettings, saveSettings, setFakturowniaSettings, saveEnabled } = useStore();

    const handleChange = (event: SyntheticEvent, newValue: number) => {
        setActiveTab(newValue);
    };

    const handleSettingChange = (item: GeneralSettingItem) => {
        setFakturowniaSettings(item);
        setSave(() => saveEnabled());
    };

    const onSubmit = () => {
        saveSettings();
    }

    return (<Box>
            <Box>
                <Tabs value={activeTab} onChange={handleChange}>
                    <Tab label="Fakturownia" />
                    <Tab label="General Settings" />
                </Tabs>
            </Box>
            {fakturowniaSettings && (<TabPanel value={activeTab} index={0}><SettingsItem handleChange={handleSettingChange} settings={fakturowniaSettings} /></TabPanel>)}
            <TabPanel value={activeTab} index={1}><SettingsItem handleChange={handleSettingChange} settings={[]} /></TabPanel>
            <Button sx={{ float: 'right', mr: '10px'}} disabled={save} variant="contained" onClick={onSubmit}>SAVE</Button>
        </Box>
    )
}

export default observer(Settings);