import {AxiosService} from '../utils/Service';

export default {
    sendSegmentEvent(type) {
        return AxiosService.post('/segment-events',{event: type});
    }
};
