import React from "react";
/*const HelpCenter = lazy(()=>  import('../components/pages/HelpCenter.jsx'));
const Home = lazy(()=>  import('../components/pages/Home.jsx'));
const Integration = lazy(()=>  import('../components/pages/Integration.jsx'));
const PlanPricing = lazy(()=>  import('../components/pages/PlanPricing.jsx'));
const Tutorial = lazy(()=>  import('../components/pages/Tutorial.jsx'));
const AddEditBlock = lazy(()=>  import('../components/pages/AddEditBlock.jsx'));*/

import HelpCenter from '../components/pages/HelpCenter';
import Home from "../components/pages/Home.jsx";
import Integration from '../components/pages/Integration';
import PlanPricing from '../components/pages/PlanPricing';
import Tutorial from '../components/pages/Tutorial';
import AddEditBlock from '../components/pages/AddEditBlock';

const HOME = '/';
const TUTORIAL = '/tutorial';
const PLAN_PRICING = '/plan-pricing';
const INTEGRATION = '/integrations';
const HELP_CENTER = '/help-center';
const ADDEDITBLOCK = '/add-edit-block';

const routes = [
    {
        path: HOME,
        exact: true,
        icon :"bi bi-gear",
        page: {
            component: Home,
            title: 'Home'
        }
    },
    {
        path: TUTORIAL,
        exact: true,
        icon:'bi bi-people-fill',
        page: {
            component: Tutorial,
            title: 'Tutorial'
        }
    },
    {
        path: PLAN_PRICING,
        exact: true,
        icon:'bi bi-people-fill',
        page: {
            component: PlanPricing,
            title: 'Plan Pricing'
        }
    },
    {
        path: INTEGRATION,
        exact: true,
        icon:'bi bi-arrow-bar-left',
        page: {
            component: Integration,
            title: 'Integration'
        }
    },
    {
        path: HELP_CENTER,
        exact: true,
        icon:'bi bi-arrow-bar-left',
        page: {
            component: HelpCenter,
            title: 'Help Center'
        }
    },
    {
        path: ADDEDITBLOCK,
        exact: true,
        icon:'bi bi-arrow-bar-left',
        page: {
            component: AddEditBlock,
            title: 'Add Edit Block'
        }
    },
];

export {routes};
