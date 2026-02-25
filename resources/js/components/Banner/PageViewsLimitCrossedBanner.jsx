import {Banner} from '@shopify/polaris';
import {useContext, useEffect, useState} from 'react';
import {useNavigate} from 'react-router-dom';
import {AppContext} from '../../context/AppProvider';

const PageViewsLimitCrossedBanner = () => {
    const navigate = useNavigate();
    const {user} = useContext(AppContext);
    const [showBanner, setShowBanner] = useState(false);

    useEffect(() => {
        if (user?.page_views_limit_crossed === 1) {
            setShowBanner(true);
        }
    }, [user?.page_views_limit_crossed]);

    return (
        <>
            {
                showBanner ?
                    <Banner
                        title="Plan usage limit reached."
                        action={{
                            content: 'Upgrade My Plan',
                            onAction: () => navigate('/plan-pricing'),
                        }}
                        status="warning"
                    >
                        <p> You reached your usage based limit on your current plan. Your icons are not visible anymore until they reset on next billing plan. Please upgrade your plan or wait until the next reset.</p>
                    </Banner>
                    : null
            }
        </>
    );
};

export default PageViewsLimitCrossedBanner;
