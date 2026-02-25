import axios from "axios";
import {AxiosService} from '../utils/Service';

export default {
    getTutorials() {
        return AxiosService.get('/tutorials');
    }
};
