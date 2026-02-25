import axios from "axios";
import {AxiosService} from '../utils/Service';

export default {
    submitReview(rate, description = '') {
        return AxiosService.post('/submit-review',{rate:rate, description: description});
    }
};
