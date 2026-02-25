import {Redirect} from '@shopify/app-bridge/actions';
import {Banner} from '@shopify/polaris';
import {useContext, useEffect, useState} from 'react';
import {toast} from 'react-toastify';
import {AppContext} from '../../context/AppProvider';
import ThemeService from '../../services/ThemeService';
import {showToast} from '../../utils/constant';

const APP_DISPLAY_NAME = import.meta.env.VITE_APP_DISPLAY_NAME || 'App';

const AppExtensionBanner = () => {
    const {user, setUser} = useContext(AppContext);
    const [showBanner, setShowBanner] = useState(false);
    const [showActivateBanner, setShowActivateBanner] = useState(false);

    useEffect(() => {
        setShowBanner(user != null && !user.is_extension_enabled);
    }, [user?.is_extension_enabled, user]);

    const handleActivateExtension = () => {
        ThemeService.activateAppExtension().then((response) => {
            if (response.data?.data?.enabled === false) {
                const redirect = Redirect.create(app);
                const normalizeUuid = (val) => {
                    if (!val) return '';
                    const m = val.match(/[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}/i);
                    return m ? m[0] : val;
                };
                let uuid = normalizeUuid(import.meta.env.VITE_SHOPIFY_ICONITO_APP_EXTENSION_ID);
                let handle = import.meta.env.VITE_SHOPIFY_ICONITO_APP_EXTENSION_NAME;

                let url = `/themes/${response.data.data.theme_id}/editor?context=apps&activateAppId=${uuid}/${handle}`;
                redirect.dispatch(Redirect.Action.ADMIN_PATH, {
                    path: url,
                    newContext: true,
                });
            }
            if (response.data?.data?.enabled === true) {
                setUser(response.data?.data?.user);
                setShowActivateBanner(true);
                setShowBanner(false);
            }
        }).catch((error) => {
            showToast(error.response.data.message, {type: 'error'});
        });
    };

    return (
        <>
            {
                showActivateBanner ?
                    <Banner
                        title="Congrats"
                        status="success"
                        onDismiss={() => setShowActivateBanner(false)}
                    >
                        <p> {APP_DISPLAY_NAME} is defined as your theme app extension </p>
                    </Banner>
                    : null
            }

            {

                showBanner ?
                    <Banner
                        title={`${APP_DISPLAY_NAME} is not defined as your theme app extension`}
                        action={{
                            content: `Activate ${APP_DISPLAY_NAME} Extension`,
                            onAction: () => handleActivateExtension(),
                        }}
                        status="critical"
                    >
                        <p> {APP_DISPLAY_NAME} will not be displayed properly to your customers until you set it up. </p>
                    </Banner>
                    : null
            }
        </>
    );
};

export default AppExtensionBanner;
