import axios from "axios";

axios.defaults.baseURL = "/api/";
let token = window.sessionToken;

setInterval(setSessionToken,3000);

export default class AxiosClass {
    async get(url) {
        if (token === undefined) {
            await setSessionToken()
        }

        const headers = {
            Authorization: `Bearer ${token}`,
        };

        return axios.get(url,{headers : headers });
    }

    async post(url, body) {
        if (token === undefined) {
            await setSessionToken();
        }

        const headers = {
            Authorization: `Bearer ${token}`,
        };

        return axios.post(url, body, { headers: headers });
    }

    async delete(url) {
        if (token === undefined) {
            await setSessionToken();
        }

        const headers = {
            Authorization: `Bearer ${token}`,
        };

        return axios.delete(url, { headers: headers });
    }
}

async function setSessionToken(){
    token = await utils.getSessionToken(app);
    axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
}

export const AxiosService = new AxiosClass();
