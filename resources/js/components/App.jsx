import React from 'react';
import { AppProvider } from '@shopify/polaris';
import {Provider} from '@shopify/app-bridge-react';
import {BrowserRouter} from 'react-router-dom';
import {Frame} from '@shopify/polaris';
import enTranslations from '@shopify/polaris/locales/en.json'
import '@shopify/polaris/build/esm/styles.css';
import 'react-toastify/dist/ReactToastify.css';
import "../../css/app.css"
import {toast, ToastContainer} from 'react-toastify';
import Header from './layout/Header';
import Routes from './layout/Routes';
import { Provider as AppContext } from '../context/AppProvider';

const App = () => {
    const config = {
        apiKey : document.getElementById("apiKey").value,
        shopOrigin : document.getElementById("shopOrigin").value,
        host: document.getElementById("reqHost").value,
        planId : document.getElementById("planId").value,
        forceRedirect : true,
    };
    const clearWaitingQueue = () => {
        // Easy, right 😎
        // toast.clearWaitingQueue();
    }

    return (
        <AppProvider
            i18n={enTranslations}
            features={{newDesignLanguage: true}}
        >
            <Provider config={config}>
                <AppContext>
                    <Frame>
                        <ToastContainer containerId={'con1'} pauseOnFocusLoss={false} />
                        <BrowserRouter>
                            <Header />
                            <Routes />
                        </BrowserRouter>
                    </Frame>
                </AppContext>
            </Provider>
        </AppProvider>
    );
};

export default App;
