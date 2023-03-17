import { FormField } from './FormField';
import { Form } from './FomrFields.styles';
import { FormFieldsProps } from './FormFIelds.types';

export function FormFields<T extends string>({ fields }: FormFieldsProps<T>){
    return(<>
        <Form>
        { fields.map(field => (<FormField field={field} />))}
        </Form>
    </>)
}