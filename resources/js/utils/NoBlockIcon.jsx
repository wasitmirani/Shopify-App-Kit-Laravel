import {Box, Button, Image, Text} from '@shopify/polaris';
import {useNavigate} from 'react-router-dom';

const NoBlockIcon = () => {
    const navigate = useNavigate();
    return (
        <>
            <div align={'end'}>
                <Button id={'no-block-add-btn'} primary
                        onClick={() => navigate('/add-edit-block')}> Add icon
                    blocks </Button>
            </div>
            <Box id={'no-block-icon'} padding="5">
                <Image alt="No Blocks Available" source={'/images/no_data.png'}/>

                <Text variant={'heading3xl'}
                      as={'h1'} id={'no-block-icon-title'}
                >
                    Icons, Trust Badges And Guarantees Can Boost Your Conversion
                </Text>

                <Text variant={'bodyLg'} color={'subdued'}
                      as={'p'} id={'no-block-icon-subtitle'}
                >
                    Highlight your store with 1000+ trust badges & icons
                </Text>
            </Box>
        </>
    );
};

export default NoBlockIcon;
