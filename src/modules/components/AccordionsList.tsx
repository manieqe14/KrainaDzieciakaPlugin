import { Children, FC, cloneElement, isValidElement } from "react"
import { AccordionsListProps } from "./AccordionsList.types";

export const AccordionsList: FC<AccordionsListProps> = ({ children }) => {
    return (<>{Children.map(children, (child, i) => {
        if(!isValidElement(child)){
            return child;
        }
        return cloneElement(child, { key: i });
      })}</>);
}