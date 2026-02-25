import {
    Box,
    Button,
    Icon,
    LegacyStack,
    Page,
    Text,
} from '@shopify/polaris';
import {FavoriteMajor, MobileChevronMajor} from '@shopify/polaris-icons';
import {useContext, useEffect, useState} from 'react';
import {useNavigate} from 'react-router-dom';
import {toast} from 'react-toastify';
import {AppContext} from '../../context/AppProvider';
import IntegrationService from '../../services/IntegrationService';
import AppExtensionBanner from '../Banner/AppExtensionBanner';
import PageViewsLimitCrossedBanner from '../Banner/PageViewsLimitCrossedBanner';
import Footer from '../layout/Footer';


const Integration = () => {
    const {integrations} = useContext(AppContext);
    // const [integrations, setIntegrations] = useState([]);
    const navigate = useNavigate();

    /*useEffect(()=>{
        IntegrationService.getIntegrations()
        .then((response) => {
            setIntegrations(response.data.data);
        }).catch((error) => {
            toast(error.response.data.message);
        })
    },[])*/

    return (
        <Page fullWidth>
            <AppExtensionBanner />
            <PageViewsLimitCrossedBanner />
            <div id={'block-breadcrumb'}>
                <div blockAlign={'center'}>
                    <Button onClick={() => navigate('/')}>
                        <Icon source={MobileChevronMajor}></Icon>
                    </Button>
                    <Text variant={'headingMd'} as={'h5'}> Integrations </Text>
                </div>
            </div>

            <div className="block-wrapper">
                {
                    integrations.map((integration, index) => {
                        return (
                                <div className={'box-inner-wrapper'}>
                                    <div wrap={false}>
                                        <div>
                                            <img width={50} height={50}
                                                 src={'/images/integrations/' + integration.logo }/>
                                        </div>

                                        <div className={'app-details'}>
                                            <LegacyStack vertical={true}>
                                                <Text variant={'headingMd'}> {integration.title} </Text>
                                                <Text variant={'bodyMd'}> {integration.description} </Text>
                                                <LegacyStack spacing={'extraTight'} alignment={"trailing"}
                                                             distribution={'leading'}>
                                                    <Icon
                                                        source={FavoriteMajor}
                                                        color="warning"
                                                    />
                                                    <Text
                                                        variant={'headingSm'}> 5.0 </Text>
                                                    <Text
                                                        variant={'bodyMd'}>({integration.review_count})</Text>
                                                    <Text variant={'bodyMd'}> {integration.plan_availability_text} </Text>
                                                </LegacyStack>

                                                <Button external url={integration.link} size={'medium'} primary> Try
                                                    it for Free </Button>
                                            </LegacyStack>
                                        </div>
                                    </div>
                                </div>
                        );
                    })
                }
            </div>
            <Footer />
        </Page>
    );
};

export default Integration;
