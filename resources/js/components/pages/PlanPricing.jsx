import {
    Button,
    Layout,
    LegacyCard,
    LegacyStack,
    List,
    Page,
    Text,
} from '@shopify/polaris';
import {useContext, useEffect, useState} from 'react';
import {useNavigate} from 'react-router-dom';
import {toast} from 'react-toastify';
import {AppContext} from '../../context/AppProvider';
import PlanService from '../../services/PlanService';
import {showToast} from '../../utils/constant';
import AppExtensionBanner from '../Banner/AppExtensionBanner';
import PageViewsLimitCrossedBanner from '../Banner/PageViewsLimitCrossedBanner';

const PlanPricing = () => {
    const {plans} = useContext(AppContext);
    // const [plans, setPlans] = useState([]);
    const navigate = useNavigate();
    const {user, setUser, setPlan, setCharge} = useContext(AppContext);

    useEffect(() => {
        // getPlans();
    }, []);

    const getPlans = () => {
        PlanService.getPlans().then((response) => {
            if (response.data?.data) {
                setPlans(response.data.data);
            }
        }).catch((error) => {
            showToast(error.response.data.message, {type: 'error'});
        });
    };

    const handleSubscribePlan = (plan) => {
        if (plan.price == 0 && false) {
            PlanService.chooseFreePlan().then((response) => {
                if (response.data?.data) {
                    let data = response.data.data;

                    setPlan(data.subscribed_plan);
                    setCharge(data.active_charge);

                    delete data.subscribed_plan;
                    delete data.active_charge;

                    setUser(data);

                    navigate('/');
                }
            }).catch((error) => {
                showToast(error.response.data.message, {type: 'error'});
            });
        } else {
            let host = document.getElementById('reqHost').value;
            window.location.href = `/billing/${plan.id}?shop=${user.name}&host=${host}`;
        }
    };

    return (
        <Page fullWidth>
            <AppExtensionBanner />
            {/*<PageViewsLimitCrossedBanner />*/}
            <Text variant="heading2xl" id="plan-price-title" as="h2"
                  alignment="center">
                CHOOSE YOUR PLAN
            </Text>
            <div className={'plan-page-wrapper'}>


                <Layout>
                    {
                        plans.map((plan, index) => {
                            return (
                                <Layout.Section key={index} oneThird>
                                    <div className={'plan-card' +
                                        (user.plan_id == plan.id
                                            ? ' current-plan'
                                            : '')}>
                                        <LegacyCard>
                                            <LegacyCard.Section>
                                                <div className="plan-name">
                                                    <Text color={'success'}
                                                          variant="heading2xl"
                                                          as="h1"
                                                          alignment="center">
                                                        {plan.name}
                                                    </Text>
                                                </div>
                                                {
                                                    plan.price == 0 ?
                                                        <Text
                                                            variant="heading4xl"
                                                            as="h1"
                                                            alignment="center"> FREE
                                                            FOREVER </Text>

                                                        :

                                                        <div
                                                            className={'plan-price'}>
                                                            <Text
                                                                variant="heading4xl"
                                                                as="h1"
                                                                alignment="center"> ${plan.price} </Text>

                                                            <Text
                                                                variant="headingLg"
                                                                as={'span'}> /
                                                                Month </Text>
                                                        </div>

                                                }
                                            </LegacyCard.Section>
                                            <LegacyCard.Section>
                                                <div
                                                    className="plan-price-features">
                                                    <List type="bullet">
                                                        {
                                                            plan.plan_features?.map(
                                                                (
                                                                    feature,
                                                                    index) => {
                                                                    return (
                                                                        <List.Item
                                                                            key={index}>
                                                                            <LegacyStack>
                                                                                <LegacyStack.Item>
                                                                                    {
                                                                                        feature.is_included
                                                                                            ?
                                                                                            <img
                                                                                                src={'/images/right_img.png'}
                                                                                                className={'feature-icon-contains'}
                                                                                                alt={'Plan has feature'}/>
                                                                                            :
                                                                                            <img
                                                                                                src={'/images/cross_img.png'}
                                                                                                className={'feature-icon-not-contains'}
                                                                                                alt={'Plan does not have a feature'}/>
                                                                                    }
                                                                                </LegacyStack.Item>
                                                                                <LegacyStack.Item>
                                                                                    <Text
                                                                                        variant="heading1xl"
                                                                                        as="span"
                                                                                        alignment="center"> {feature.feature} </Text>
                                                                                </LegacyStack.Item>
                                                                            </LegacyStack>
                                                                        </List.Item>
                                                                    );
                                                                })
                                                        }

                                                    </List>
                                                </div>
                                            </LegacyCard.Section>
                                            <LegacyCard.Section>
                                                <div className={'plan-btn'}>
                                                    <Button
                                                        primary
                                                        fullWidth
                                                        disabled={user.plan_id ==
                                                            plan.id}
                                                        onClick={() => handleSubscribePlan(
                                                            plan)}
                                                        size={'large'}>
                                                        {user.plan_id == plan.id
                                                            ? 'Current Plan'
                                                            : plan.price == 0
                                                                ? 'Free Plan'
                                                                : `Start ${plan.trial_days}-day trial`}
                                                    </Button>
                                                </div>
                                            </LegacyCard.Section>

                                        </LegacyCard>
                                    </div>
                                </Layout.Section>
                            );
                        })
                    }
                </Layout>
            </div>
        </Page>
    );
};

export default PlanPricing;
