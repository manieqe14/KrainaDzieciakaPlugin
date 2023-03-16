import { FC, MouseEventHandler, useState } from 'react';
import { Button, Menu, MenuItem } from '@mui/material';
import { useStore } from '../../store/store.context';
import { Pages } from '../../store/ui/uiConstans';

export const Navigation: FC = () => {

    const { ui } = useStore();

    const [anchorEl, setAnchorEl] = useState<null | HTMLElement>(null);
    const open = Boolean(anchorEl);
    const handleClick: MouseEventHandler<HTMLButtonElement> = (event) => {
        setAnchorEl(event.currentTarget);
    };
    const handleClose = (item: string) => {
        console.info(item);
        ui.currentPage = item;
        setAnchorEl(null);
    };

    return (<div>
        <Button
            id="basic-button"
            aria-controls={open ? 'basic-menu' : undefined}
            aria-haspopup="true"
            aria-expanded={open ? 'true' : undefined}
            onClick={handleClick}
        >
            {ui.currentPage}
        </Button>
        <Menu
            id="basic-menu"
            anchorEl={anchorEl}
            open={open}
            onClose={handleClose}
            MenuListProps={{
                'aria-labelledby': 'basic-button',
            }}
        >
            {Object.entries(Pages).map(([key, page]) => <MenuItem onClick={() => handleClose(key)}>{page.title}</MenuItem>)}
        </Menu>
    </div>);
}