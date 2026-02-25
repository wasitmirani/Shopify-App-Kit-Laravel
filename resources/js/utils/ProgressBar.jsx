import {
    Icon,
    ProgressBar as PolarisProgressBar,
    Text,
    Tooltip,
} from '@shopify/polaris';
import {QuestionMarkInverseMajor} from '@shopify/polaris-icons';
import {useContext, useEffect, useState} from 'react';
import {toast} from 'react-toastify';
import {AppContext} from '../context/AppProvider';
import UserService from '../services/UserService';
import {showToast} from './constant';

const ProgressBar = () => {
    const [count, setCount] = useState(0);
    const {plan} = useContext(AppContext);

    useEffect(() =>{
        UserService.getPageViewsCount()
        .then((response) => {
            setCount(response.data?.data?.page_views)
        })
        .catch((error) => {
            showToast(error.response.data.message, {type:'error'});
        })
    },[]);
    return (
        <div id={'progressbar-section'}>
            <div align={'end'}>
                <div id={'progress-bar'}>
                    <PolarisProgressBar progress={(count * 100 / plan?.page_views_threshold)}/>
                    <div className={'uses-count'}>
                        <span>{count}</span>/{plan?.page_views_threshold} page views used
                    </div>
                </div>
                <Tooltip
                    content={<Text color={'white'}>
                        Monthly icons page views are any page views on your
                        website that contain a block of icons from our app. As
                        long as there is at least one icon in the block, that
                        page view will be counted towards your plan’s limit.
                    </Text>}>
                    <Icon
                        source={QuestionMarkInverseMajor}
                        color="base"
                    />
                </Tooltip>
            </div>

        </div>
    );
};

export default ProgressBar;
