import { Children, FC, cloneElement, isValidElement } from "react"
import { AccordionsListProps } from "./AccordionsList.types";
import { List } from '@mui/material';

export const AccordionsList: FC<AccordionsListProps> = ({ children }) => {
    return (<List>{Children.map(children, (child, i) => {
        if(!isValidElement(child)){
            return child;
        }
        return cloneElement(child, { key: i });
      })}</List>);
}