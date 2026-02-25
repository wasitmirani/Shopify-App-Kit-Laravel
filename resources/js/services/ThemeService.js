import {AxiosService} from '../utils/Service';

export default {
    activateAppExtension() {
        return AxiosService.post('/activate-app-extension');
    }
};
