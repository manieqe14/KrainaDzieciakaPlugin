import { FakturowniaSettings } from './FakturowniaSettings';
import { Box, Tab, Tabs, Typography } from '@mui/material';
import { FC, SyntheticEvent, useState } from 'react';
import { GeneralSettings } from './GeneralSettings';
import { observer } from 'mobx-react-lite';
import { useStore } from '../../store/store.context';

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
    const { fakturowniaSettings } = useStore();

    const handleChange = (event: SyntheticEvent, newValue: number) => {
        setActiveTab(newValue);
    };

    return (<Box>
            <Box>
                <Tabs value={activeTab} onChange={handleChange}>
                    <Tab label="Fakturownia" />
                    <Tab label="General Settings" />
                </Tabs>
            </Box>
            <TabPanel value={activeTab} index={0}><FakturowniaSettings settings={fakturowniaSettings} /></TabPanel>
            <TabPanel value={activeTab} index={1}><GeneralSettings /></TabPanel>
        </Box>
    )
}

export default observer(Settings);