import axios from "axios";
import {AxiosService} from '../utils/Service';

export default {
    getCollections(query = '') {
        return AxiosService.get(`/collections?search=${query}`);
    }
};
