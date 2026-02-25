import axios from "axios";
import {AxiosService} from '../utils/Service';

export default {
    getFAQs() {
        return AxiosService.get('/faqs');
    }
};
