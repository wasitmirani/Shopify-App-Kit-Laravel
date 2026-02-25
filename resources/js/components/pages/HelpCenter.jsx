import {
    Box,
    Button, Collapsible,
    Icon,
    LegacyStack,
    Page,
    Text,
} from '@shopify/polaris';
import {
    CircleMinusOutlineMinor,
    CirclePlusOutlineMinor,
    MobileChevronMajor,
} from '@shopify/polaris-icons';
import {useContext, useEffect, useState} from 'react';
import {useNavigate} from 'react-router-dom';
import {toast} from 'react-toastify';
import {AppContext} from '../../context/AppProvider';
import FAQService from '../../services/FAQService';
import AppExtensionBanner from '../Banner/AppExtensionBanner';
import PageViewsLimitCrossedBanner from '../Banner/PageViewsLimitCrossedBanner';
import Footer from '../layout/Footer';

const HelpCenter = () => {
    const {faqs} = useContext(AppContext);
    const [collapseId, setCollapseID] = useState('');
    const navigate = useNavigate();

    /*useEffect(()=>{
        FAQService.getFAQs()
        .then((response) => {
            setFaqs(response.data.data);
        })
        .catch((error) => {
            toast(error.response.data.message);
        })
    },[]);*/

    const handleCollapseClick = (id) =>{
        setCollapseID();
        if(collapseId == id){
            setCollapseID('');
        }else {
            setCollapseID(id);
        }
    }

    return (
        <Page fullWidth>
            <AppExtensionBanner />
            <PageViewsLimitCrossedBanner />
            <div id={'block-breadcrumb'}>
                <div blockAlign={'center'}>
                    <Button onClick={() => navigate('/')}>
                        <Icon source={MobileChevronMajor}></Icon>
                    </Button>
                    <Text variant={'headingMd'} as={'h5'}> FAQ </Text>
                </div>
            </div>

            <div id={'faq-wrapper'}>
                {
                    faqs.map((faq,index) => {
                        return (
                            <Box background="action-secondary" key={index}>
                                <Button onClick={() => handleCollapseClick(faq.id)} textAlign={'start'} fullWidth monochrome plain removeUnderline>
                                    <LegacyStack distribution={'equalSpacing'}>
                                        <Text variant={'bodyLg'}> { faq.question } </Text>
                                        <Icon source={collapseId == faq.id ? CircleMinusOutlineMinor : CirclePlusOutlineMinor}> </Icon>
                                    </LegacyStack>
                                </Button>
                                <Collapsible
                                    open={collapseId == faq.id}
                                    transition={{duration: '500ms', timingFunction: 'ease-in-out'}}
                                    expandOnPrint
                                >
                                    <p style={{padding:'10px'}}>
                                        { faq.answer }
                                    </p>
                                </Collapsible>
                            </Box>
                        );
                    })
                }

            </div>

            <Footer />
        </Page>
    );
}

export default HelpCenter;
