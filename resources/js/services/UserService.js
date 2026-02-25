import {AxiosService} from '../utils/Service';

export default {
    getUserWithPlan() {
        return AxiosService.get('/user-plan');
    },
    getPageViewsCount() {
        return AxiosService.get('/user-page-views-count');
    },
};
