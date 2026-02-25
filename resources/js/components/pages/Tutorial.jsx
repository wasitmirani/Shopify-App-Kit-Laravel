import {
    Box,
    Button,
    Icon,
     Layout, Link, List,
    MediaCard, Modal,
    Page,
    Text,
    VideoThumbnail,
} from '@shopify/polaris';
import {ExternalMinor, MobileChevronMajor} from '@shopify/polaris-icons';
import {useContext, useEffect, useState} from 'react';
import {useNavigate} from 'react-router-dom';
import {AppContext} from '../../context/AppProvider';
import SegmentService from '../../services/SegmentService';
import TutorialService from '../../services/TutorialService';
import AppExtensionBanner from '../Banner/AppExtensionBanner';
import PageViewsLimitCrossedBanner from '../Banner/PageViewsLimitCrossedBanner';
import Footer from '../layout/Footer';

const Tutorial = () => {
    const { tutorials, user, setUser } = useContext(AppContext);
    // const [tutorials, setTutorials] = useState([]);
    const [link, setLink] = useState('');
    const navigate = useNavigate();
    const frequentlyAskedQuestions = [
        'HOW TO INSTALL?',
        'How many icons am I allowed to use with my plan?',
        'Is the app working with all theme on Shopify?',
        'Do I need to add any code to my theme?',
        'I want to cancel my subscription, what should I do ?',
        'Do I need to do anything after removing this app?'
    ];

    useEffect(() => {
        if(user.segment_events.click_on_tutorial === 0 ){
            SegmentService.sendSegmentEvent('click_on_tutorial')
            .then((response) =>{
                if (response.data.success){
                    user.segment_events.click_on_tutorial = 1;
                    setUser(user);
                }
            }).catch((error)=>{
            })
        }
    },[user]);

    return (
        <Page fullWidth>
            <AppExtensionBanner />
            <PageViewsLimitCrossedBanner />
            <div id={'block-breadcrumb'}>
                <div blockAlign={'center'}>
                    <Button onClick={() => navigate('/')}>
                        <Icon source={MobileChevronMajor}></Icon>
                    </Button>
                    <Text variant={'headingMd'} as={'h5'}> Video Tutorials </Text>
                </div>
            </div>

            <div id={'tutorials-wrapper'}>
                <Layout>
                    <Layout.Section oneHalf>
                        {
                            tutorials.map((tutorial,index) => {
                                return (
                                    <MediaCard key={index}
                                        title={<Text variant={'headingLg'}> {tutorial.title} </Text>}
                                        primaryAction={{
                                            content: 'Watch more',
                                            onAction: () => setLink(tutorial.link),
                                        }}
                                        description={<div><Text variant={'bodyMd'}> {tutorial.description} <img src={'/images/rock_.png'} /> </Text> </div>}
                                    >
                                        <VideoThumbnail
                                            thumbnailUrl={"/images/tutorials/" + tutorial.thumbnail}
                                            onClick={() => setLink(tutorial.link)}
                                        />
                                    </MediaCard>
                                );
                            })
                        }

                    </Layout.Section>

                    <Layout.Section oneThird secondary>
                        <Box id={'frequently-asked-questions'} background="action-secondary">
                            <Text variant={'headingXl'} alignment={'center'}
                                  as={'h3'}> Frequently Asked Questions </Text>
                            <List>
                                {
                                    frequentlyAskedQuestions.map((q,index) =>{
                                        return (
                                            <List.Item key={index}>
                                                <Text variant={'bodyMd'} color={'base'}>
                                                    <Link monochrome removeUnderline onClick={() => navigate('/help-center')}> <div blockAlign={'center'}>  {q}  <Icon color="base"  source={ExternalMinor}/> </div>  </Link>
                                                </Text>
                                            </List.Item>
                                        );
                                    })
                                }
                            </List>
                        </Box>

                        <Button onClick={() => navigate('/help-center')} fullWidth> View More </Button>
                    </Layout.Section>
                </Layout>
            </div>

            <div id={"tutorial-modal-wrapper"}>
                <Modal
                    titleHidden={true}
                    open={link}
                    fullScreen
                    onClose={() => setLink('')}
                >
                    <div className={'embed-video-wrapper'}>
                        <iframe width="1000" height="560" allowFullScreen={true} src={link} />
                    </div>
                </Modal>
            </div>
            <Footer />
        </Page>
    );
}

export default Tutorial;
