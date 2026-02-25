import * as actions from "./Actions"


//Reducer to Handle Actions
const reducer = (state, action) => {
    switch (action.type) {
        case actions.SET_USER:
            return {
                ...state, user: action.query
            };

        case actions.SET_PLAN:
            return {
                ...state, plan: action.query
            };

        case actions.SET_PLANS:
            return {
                ...state, plans: action.query
            };

        case actions.SET_CHARGE:
            return {
                ...state, charge: action.query
            };

        case actions.INCREASE_DISCARD_FLAG:
            return {
                ...state, discard_flag: action.query
            };

        case actions.SET_TUTORIALS:
            return {
                ...state, tutorials: action.query
            };

        case actions.SET_ICON_BLOCKS:
            return {
                ...state, icon_blocks: action.query
            };

        case actions.SET_FAQS:
            return {
                ...state, faqs: action.query
            };

        case actions.SET_INTEGRATIONS:
            return {
                ...state, integrations: action.query
            };

        default:
            return state;
    }
};

export default reducer;
