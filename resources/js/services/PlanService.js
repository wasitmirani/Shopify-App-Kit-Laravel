import axios from "axios";
import {AxiosService} from '../utils/Service';

export default {
    getPlans() {
        return AxiosService.get('/plans');
    },
    chooseFreePlan(){
        return AxiosService.post('/plans/choose-plan/free');
    },
};
