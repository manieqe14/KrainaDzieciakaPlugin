import { FormFieldProps } from './FormFIelds.types';
import { isInputField, isSelectField } from '../../../utils/formUtils';
import { Checkbox, FormControlLabel, TextField } from '@mui/material';

export function FormField<T extends string>({ field }: FormFieldProps<T>){
    const { name, value, onChange } = field;
    return (<>
            {isSelectField(field) && (
                <FormControlLabel control={<Checkbox value={value} />} label={name} />)}
            {isInputField(field) && <TextField id={name} type="text" label={name} value={value} onChange={() => onChange(value)} />}
        </>
    );
}