import {
    ActionList,
    Button,
    Icon,
    LegacyStack,
    Popover,
    Text,
} from '@shopify/polaris';
import {
    HomeMajor,
    PlanMajor,
    PlayCircleMajor,
    QuestionMarkMajor,
    ToolsMajor,
} from '@shopify/polaris-icons';
import {Crisp} from 'crisp-sdk-web';
import {useCallback, useContext, useEffect, useState} from 'react';
import {useNavigate} from 'react-router-dom';
import {AppContext} from '../../context/AppProvider';
import UserService from '../../services/UserService';

const Header = () => {
    const {setUser, setPlan, setCharge, setPlans, setTutorials, setFaqs, setIntegrations, setIconBlocks } = useContext(AppContext);
    const [active, setActive] = useState(false);

    const toggleActive = useCallback(() => setActive((active) => !active), []);

    useEffect(async () => {
        await UserService.getUserWithPlan().then((response) => {
            if (response.data.data) {
                let data = response.data.data;

                setPlan(data.subscribed_plan);
                setCharge(data.active_charge);
                setPlans(data.plans);
                setIconBlocks(data.icon_blocks);
                setTutorials(data.tutorials);
                setFaqs(data.faqs);
                setIntegrations(data.integrations);

                if(data.subscribed_plan?.live_chat){
                    let id = import.meta.env.VITE_CRISP_WEBSITE_ID
                    Crisp.configure(id);
                    Crisp.setSafeMode(true);
                    Crisp.user.setEmail(data.owner_email);
                    Crisp.user.setNickname(data.name);
                }

                delete data.subscribed_plan;
                delete data.active_charge;
                delete data.icon_blocks;
                delete data.tutorials;
                delete data.faqs;
                delete data.integrations;

                setUser(data);
            }
        }).catch((error) => {
        });
    }, []);

    const navigate = useNavigate();

    const activator = (
        <Button onClick={toggleActive} disclosure>
            More actions
        </Button>
    );

    return (
        <div className="navbar">
            <LegacyStack
                wrap={false}
                alignment="center"
                distribution="equalSpacing"
                backgroundColor="white"
            >
                <LegacyStack.Item>
                    <div
                        className="brand"
                        onClick={() => {
                            navigate('/');
                        }}
                    >
                        <img src={'/images/iconito-1200.png'} alt="Logo"
                             style={{height: '25px'}}/>
                        <h1
                            style={{
                                marginLeft: '8px',
                                marginTop: '2px',
                            }}
                        >
                            Iconito <span className={'app-subtitle'}> - Trust badges & icons </span>
                        </h1>
                    </div>
                </LegacyStack.Item>
                <LegacyStack.Item>
                    <div className="topButtons">
                        <Button
                            onClick={() => {
                                navigate('/');
                            }}
                        >
                            <LegacyStack>
                                <LegacyStack.Item>
                                    <Icon
                                        source={HomeMajor}
                                        color="base"
                                    />
                                </LegacyStack.Item>
                                <LegacyStack.Item>
                                    <Text variant={'bodyMd'}
                                          as={'p'}> Dashboard </Text>
                                </LegacyStack.Item>
                            </LegacyStack>
                        </Button>
                        <Button
                            onClick={() => {
                                navigate('/tutorial');
                            }}
                        >
                            <LegacyStack>
                                <LegacyStack.Item>
                                    <Icon
                                        source={PlayCircleMajor}
                                        color="base"
                                    />
                                </LegacyStack.Item>
                                <LegacyStack.Item>
                                    <Text variant={'bodyMd'}
                                          as={'p'}> Tutorial </Text>
                                </LegacyStack.Item>
                            </LegacyStack>
                        </Button>
                        <Button
                            onClick={() => {
                                navigate('/plan-pricing');
                            }}
                        >
                            <LegacyStack>
                                <LegacyStack.Item>
                                    <Icon
                                        source={PlanMajor}
                                        color="base"
                                    />
                                </LegacyStack.Item>
                                <LegacyStack.Item>
                                    <Text variant={'bodyMd'} as={'p'}> Plan List </Text>
                                </LegacyStack.Item>
                            </LegacyStack>
                        </Button>
                        <Button
                            onClick={() => {
                                navigate('/integrations');
                            }}
                        >
                            <LegacyStack>
                                <LegacyStack.Item>
                                    <Icon
                                        source={ToolsMajor}
                                        color="base"
                                    />
                                </LegacyStack.Item>
                                <LegacyStack.Item>
                                    <Text variant={'bodyMd'}
                                          as={'p'}> Integrations </Text>
                                </LegacyStack.Item>
                            </LegacyStack>
                        </Button>
                        <Button
                            onClick={() => {
                                navigate('/help-center');
                            }}
                        >
                            <LegacyStack>
                                <LegacyStack.Item>
                                    <Icon
                                        source={QuestionMarkMajor}
                                        color="base"
                                    />
                                </LegacyStack.Item>
                                <LegacyStack.Item>
                                    <Text variant={'bodyMd'} as={'p'}> Help Center </Text>
                                </LegacyStack.Item>
                            </LegacyStack>
                        </Button>
                    </div>

                    <div className={'mobile-navbar'}>
                            <Popover
                                active={active}
                                activator={activator}
                                autofocusTarget="first-node"
                                onClose={toggleActive}
                            >
                                <ActionList
                                    actionRole="menuitem"
                                    items={[
                                        {content: 'Dashboard', icon: HomeMajor, onAction() { navigate('/')}},
                                        {content: 'Tutorial', icon: PlayCircleMajor, onAction() { navigate('/tutorial')}},
                                        {content: 'Plan List', icon: PlanMajor,onAction() { navigate('/plan-pricing')}},
                                        {content: 'Integrations', icon: ToolsMajor,onAction() { navigate('/integrations')}},
                                        {content: 'Help Center', icon: QuestionMarkMajor,onAction() { navigate('/help-center')}},
                                    ]}
                                />
                            </Popover>
                    </div>
                </LegacyStack.Item>
            </LegacyStack>
        </div>
    );
};

export default Header;
