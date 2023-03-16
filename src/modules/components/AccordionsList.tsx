import { Children, FC, PropsWithChildren, cloneElement } from "react"

export const AccordionsList = ({ children }) => {
    if(Children.count(children) === 0){
        return null;
    }

    return (Children.map(children, (child, i) => {
        return cloneElement(child, { key: i })
      }))
}