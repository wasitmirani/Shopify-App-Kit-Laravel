import axios from "axios";
import {isStorefrontRequest} from './constant';

axios.defaults.baseURL = process.env.MIX_APP_URL + "/api/";

export default class AxiosClass {
    async get(url) {
        const headers = {
            'X-Iconito-Storefront': isStorefrontRequest(),
        };

        return axios.get(url,{headers : headers });
    }

    async post(url, body) {
        return axios.post(url, body);
    }

    async delete(url) {
        return axios.delete(url);
    }
}

export const StorefrontAxiosService = new AxiosClass();
