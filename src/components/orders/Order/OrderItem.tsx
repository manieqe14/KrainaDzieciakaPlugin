import { Accordion, AccordionDetails, Grid } from "@mui/material";
import { FC, PropsWithChildren } from "react";
import { Order } from "../../../store/store.types";
import { DataInfoContainer } from '../../../modules/core/components/DataInfoContainer/DataInfoContainer';
import { OrderItemSummary, StyledChip } from './OrderItem.styles';
import { getFormattedDate } from '../../../modules/utils/dateUtils';

export const OrderItem: FC<PropsWithChildren & { data: Order }> = ({ data }) => {

    const { shipping, billing, id, status, total, currency, shipping_method, date_created } = data;

    return (
        <Accordion>
            <OrderItemSummary status={status}>
                <Grid item xs={1}>{id}</Grid>
                <Grid item xs={2}>{getFormattedDate(date_created.date)}</Grid>
                <Grid item xs={2}><StyledChip label={status} /></Grid>
                <Grid item xs={2}>{total} {currency}</Grid>
                <Grid item xs={4}>{shipping.first_name} {shipping.last_name}</Grid>
            </OrderItemSummary>
            <AccordionDetails style={{ display: 'flex', flexDirection: 'row'}}>
                <DataInfoContainer title={'Shipping'}>
                    <div>{shipping.first_name} {shipping.last_name}</div>
                    <div>{shipping.address_1} {shipping.address_2}</div>
                    <div>{shipping.postcode} {shipping.city}</div>
                </DataInfoContainer>
                <DataInfoContainer title={'Billing'}>
                    <div>{billing.first_name} {billing.last_name}</div>
                    <div>{billing.address_1} {billing.address_2}</div>
                    <div>{billing.postcode} {billing.city}</div>
                </DataInfoContainer>
                <DataInfoContainer title={"Mail"}>{billing.email}</DataInfoContainer>
                <DataInfoContainer title={"Shipping method"}>{shipping_method}</DataInfoContainer>
            </AccordionDetails>
        </Accordion>
    );
}