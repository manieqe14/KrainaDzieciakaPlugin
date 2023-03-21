import { StyledForm } from './FormFields.styles';
import { FC } from 'react';
import { Checkbox, FormControlLabel, TextField } from '@mui/material';
import { useFormFields } from '../../context/formFields.context';

export const FormFields: FC = () => {
    const fields = useFormFields();

    return (
        <StyledForm>
            {Object.values(fields).map(({ name, value, onChange, type }) => {
                    switch (type) {
                        case 'input':
                            return <TextField id={name} type="text" label={name} value={value}
                                              onChange={(event) => onChange(event.target.value)} />
                        case 'select':
                            return <FormControlLabel control={<Checkbox value={value} onChange={(event) => onChange(event.target.checked)}/>} label={name} />
                    }
                }
            )
            }
        </StyledForm>
    )
}