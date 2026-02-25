import axios from "axios";
import {AxiosService} from '../utils/Service';

export default {
    getProducts(query = '') {
        return AxiosService.get(`/products?search=${query}`);
    }
};
