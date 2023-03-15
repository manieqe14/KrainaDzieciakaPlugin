import { Accordion, AccordionDetails, AccordionSummary } from "@mui/material";
import { FC, PropsWithChildren } from "react";
import { Order } from "../../../store/store.types";

export const OrderItem: FC<PropsWithChildren & { data: Order}> = ({ data }) => {
    return (
        <Accordion>
            <AccordionSummary>{data.id}</AccordionSummary>
            <AccordionDetails>{"test"}</AccordionDetails>
        </Accordion>
    );
}