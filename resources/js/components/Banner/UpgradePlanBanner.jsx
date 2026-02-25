import {Banner} from '@shopify/polaris';
import {useContext, useEffect, useState} from 'react';
import {AppContext} from '../../context/AppProvider';

const UpgradePlanBanner = ({hideBanner}) => {
    const {user, plan, plans} = useContext(AppContext);
    const [details, setDetails] = useState({
        text: '',
        buttonText: '',
        redirectUrl: '',
    });

    useEffect(() => {
        let index;
        plans?.map((p, i) => {if (p.id == plan.id) { index = i + 1;}});
        if (!plans) return;
        let nextPlan = plans[index];
        let host = document.getElementById('reqHost').value;

        setDetails({
            text: `For only a cup of coffee ($${nextPlan.price}/mo) you can have almost unlimited features.`,
            buttonText: `Start ${nextPlan.trial_days}-day trial`,
            redirectUrl: `/billing/${nextPlan.id}?shop=${user.name}&host=${host}`,
        });
    }, []);

    return (
        <Banner
            title="For Premium features, please upgrade your plan."
            status="warning"
            onDismiss={() => hideBanner()}
            action={{content: details.buttonText, url: details.redirectUrl}}
        >
            <p> {details.text} <img id={'plan-upgrade-rock-icon'} src={'/images/rock_.png'}/>
            </p>
        </Banner>
    );
};

export default UpgradePlanBanner;
