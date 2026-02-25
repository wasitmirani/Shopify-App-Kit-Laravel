import axios from "axios";
import {AxiosService} from '../utils/Service';

export default {
    getIntegrations() {
        return AxiosService.get('/integrations');
    }
};
