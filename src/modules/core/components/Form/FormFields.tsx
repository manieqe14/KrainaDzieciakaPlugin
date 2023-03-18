import { Form } from './FomrFields.styles';
import { FormFieldProps } from './FormFIelds.types';
import { FC } from 'react';
import { Checkbox, FormControlLabel, TextField } from '@mui/material';

export const FormFields: FC<FormFieldProps[]> = (fields) => {

    return (
        <Form>
            {Object.values(fields).map(({ name, value, onChange, type }) => {
                    switch (type) {
                        case 'input':
                            return <TextField id={name} type="text" label={name} value={value}
                                              onChange={() => onChange(value)} />
                        case 'select':
                            return <FormControlLabel control={<Checkbox value={value} />} label={name} />
                    }
                }
            )
            }
        </Form>
    )
}