import { styled } from '@mui/material/styles';
import { AccordionSummaryProps } from '@mui/material/AccordionSummary/AccordionSummary';
import { AccordionSummary, Grid } from '@mui/material';
import ArrowForwardIosSharpIcon from '@mui/icons-material/ArrowForwardIosSharp';
import { OrderStatus } from '../../../store/store.types';
import { OrderColours } from '../../../store/ui/uiConstans';


export const OrderItemSummary = styled((props: AccordionSummaryProps & { status: OrderStatus }) => (
    <AccordionSummary expandIcon={<ArrowForwardIosSharpIcon sx={{
        fontSize: '0.9rem'
    }} />} sx={
        { backgroundColor: OrderColours[props.status] }} {...props}><Grid container spacing={2}>{props.children}</Grid></AccordionSummary>))(
    () => ({
        display: 'flex',
        flexDirection: 'row',
    }));