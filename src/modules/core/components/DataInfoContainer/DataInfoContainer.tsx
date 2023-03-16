import { Box, Container } from '@mui/material';
import { FC, PropsWithChildren } from 'react';
import { DataInfoContainerProps } from './DataInfoContainer.types';
import { DataInfoContainerTitle } from './DataInfoContainer.styles';

export const DataInfoContainer: FC<PropsWithChildren & DataInfoContainerProps> = ({ children, title  }) => {
    return(
            <Container>
                { title && <DataInfoContainerTitle>{title}</DataInfoContainerTitle>}
                <Box>
                    {children}
                </Box>
            </Container>
    );
}