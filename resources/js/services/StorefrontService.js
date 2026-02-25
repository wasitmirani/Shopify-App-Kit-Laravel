import {StorefrontAxiosService as AxiosService} from '../utils/StorefrontAxiosService';

let prefix = "storefront/"
export default {
    getIndexPageIconBlocks(shop) {
        return AxiosService.get(prefix + shop + '/index/blocks');
    },
    getCartPageIconBlocks(shop) {
        return AxiosService.get(prefix + shop + '/cart/blocks');
    },
    getSiteCommonBlocks(shop) {
        return AxiosService.get(prefix + shop + '/site-common/blocks');
    },
    getProductPageIconBlocks(shop, product_id, collection_ids) {
        return AxiosService.get(prefix + shop + `/product/blocks?product_id=${product_id}&collection_ids=${collection_ids}`);
    },
};
