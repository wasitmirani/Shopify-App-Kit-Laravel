import reducer from "./reducer";
import {createContext,  useReducer} from 'react';
import * as actions from "./Actions"

// Context and Provider
export const AppContext = createContext();

const Provider = ({ children }) => {
    const initialState = {
        user: null,
        plan: null,
        plans: null,
        charge: null,
        discard_flag : 1,
        icon_blocks : [],
        tutorials : [],
        faqs : [],
        integrations : [],
    };

    const [state, dispatch] = useReducer(reducer, initialState);

    const value = {
        user: state.user,
        plan: state.plan,
        plans: state.plans,
        charge: state.charge,
        discard_flag : state.discard_flag,
        icon_blocks : state.icon_blocks,
        tutorials : state.tutorials,
        faqs : state.faqs,
        integrations : state.integrations,

        setUser: (query) => {
            dispatch({ type: actions.SET_USER, query });
        },
        setPlan: (query) => {
            dispatch({ type: actions.SET_PLAN, query });
        },
        setPlans: (query) => {
            dispatch({ type: actions.SET_PLANS, query });
        },
        setCharge: (query) => {
            dispatch({ type: actions.SET_CHARGE, query });
        },
        increaseDiscardFlag: (query) => {
            dispatch({ type: actions.INCREASE_DISCARD_FLAG, query });
        },
        setTutorials: (query) => {
            dispatch({ type: actions.SET_TUTORIALS, query });
        },
        setIconBlocks: (query) => {
            dispatch({ type: actions.SET_ICON_BLOCKS, query });
        },
        setFaqs: (query) => {
            dispatch({ type: actions.SET_FAQS, query });
        },
        setIntegrations: (query) => {
            dispatch({ type: actions.SET_INTEGRATIONS, query });
        },
    };

    return (
        <AppContext.Provider value={value}>
            { children }
        </AppContext.Provider>
    );
};


export { Provider };
