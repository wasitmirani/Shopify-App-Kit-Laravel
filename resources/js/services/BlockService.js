import axios from "axios";
import {AxiosService} from '../utils/Service';

export default {
    getBlocks() {
        return AxiosService.get('/blocks');
    },
    getSingleIconBlock(id) {
        return AxiosService.get(`/blocks/${id}`);
    },
    saveBlock(data) {
        return AxiosService.post('/blocks',data);
    },
    deleteBlock(id) {
        return AxiosService.delete(`/blocks/${id}`);
    },
    duplicate(id) {
        return AxiosService.post(`/blocks/duplicate/${id}`);
    },
    updateBlockStatus(id, status) {
        return AxiosService.get(`/blocks/update-status/${id}/${status}`);
    }
};
