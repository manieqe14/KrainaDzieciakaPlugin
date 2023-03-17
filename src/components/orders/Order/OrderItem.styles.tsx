import { styled } from '@mui/material/styles';
import { AccordionSummaryProps } from '@mui/material/AccordionSummary/AccordionSummary';
import { ChipProps } from '@mui/material/Chip/Chip';
import { AccordionSummary, Chip, Grid } from '@mui/material';
import ArrowForwardIosSharpIcon from '@mui/icons-material/ArrowForwardIosSharp';
import { OrderStatus } from '../../../store/store.types';
import { OrderColours } from '../../../store/ui/uiConstans';


export const OrderItemSummary = styled((props: AccordionSummaryProps & { status: OrderStatus }) => (
    <AccordionSummary expandIcon={<ArrowForwardIosSharpIcon sx={{
        fontSize: '0.9rem'
    }} />} sx={
        { backgroundColor: OrderColours[props.status] }} {...props}><Grid container sx={{ alignItems: 'center' }}
                                                                          spacing={2}>{props.children}</Grid></AccordionSummary>))(
    () => ({
        display: 'flex',
        flexDirection: 'row'
    }));

export const StyledChip = styled((props: ChipProps) => (<Chip {...props} />))({
    fontSize: '0.65rem',
    fontWeight: 'bold',
    textTransform: 'uppercase',
    minWidth: '100px'
});