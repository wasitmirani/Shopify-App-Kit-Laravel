import axios from "axios";
import {AxiosService} from '../utils/Service';

export default {
    getDefaultIcons() {
        return AxiosService.get('/icons/default');
    },
    getRegularIconsByCategory(category, type = 'regular', search ='') {
        return AxiosService.get(`/icons/regular?category=${category}&type=${type}&search=${search}`);
    },
    getSearchIconsByCategory( type = 'regular', search= '') {
        return AxiosService.get(`/icons/search?type=${type}&search=${search}`);
    },
    getCustomIcons() {
        return AxiosService.get(`/icons/custom`);
    },
    uploadIcon(data) {
        return AxiosService.post(`/icons/upload`,data);
    },
    getSingleIcon(id, type) {
        return AxiosService.get(`/icons/${id}?type=${type}`);
    },
};
