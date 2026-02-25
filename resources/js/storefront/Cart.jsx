import $ from 'jquery';
import {useEffect} from 'react';
import ReactDOMServer from 'react-dom/server';
import StorefrontService from '../services/StorefrontService';
import {getBlockHTML, setColorAndFontStyle} from './common';

const Product = ({data}) => {
    useEffect(() => {
        StorefrontService.getCartPageIconBlocks(data.shop_domain)
        .then((response) => {
            if(response.data.data?.header) {
                let block = response.data.data.header
                let html = getBlockHTML(block)
                html = ReactDOMServer.renderToString(html);
                if($("#render_iconito_block_header").length){
                    $("#render_iconito_block_header").html(html);
                    setColorAndFontStyle('#render_iconito_block_header',block)
                }else{
                    $("header").before(html);
                    var parent_id = $("header").parent().prop('id');
                    var parent_class = $("header").parent().prop('class');
                    if(parent_id != undefined && parent_id != ''){
                        setColorAndFontStyle("#" + parent_id + ' .icon_preview',block)
                    }else if(parent_class != undefined && parent_class != ''){
                        parent_class = parent_class.replace(/ /g, ".");
                        setColorAndFontStyle("." + parent_class + ' .icon_preview',block)
                    }
                }
            }

            if(response.data.data?.cart) {
                let block = response.data.data.cart
                let html = getBlockHTML(block)
                html = ReactDOMServer.renderToString(html);
                if($("#render_iconito_block_cart").length){
                    $("#render_iconito_block_cart").html(html);
                    setColorAndFontStyle('#render_iconito_block_cart',block)
                }else{
                    $("footer").before(html);
                    var parent_id = $("footer").parent().prop('id');
                    var parent_class = $("footer").parent().prop('class');
                    if(parent_id != undefined && parent_id != ''){
                        setColorAndFontStyle("#" + parent_id + ' .icon_preview',block)
                    }else if(parent_class != undefined && parent_class != ''){
                        parent_class = parent_class.replace(/ /g, ".");
                        setColorAndFontStyle("." + parent_class + ' .icon_preview',block)
                    }
                }
            }

            if(response.data.data?.footer) {
                let block = response.data.data.footer
                let html = getBlockHTML(block)
                html = ReactDOMServer.renderToString(html);

                if($("#render_iconito_block_footer").length){
                    $("#render_iconito_block_footer").html(html);
                    setColorAndFontStyle('#render_iconito_block_footer',block)
                }else{
                    $("footer").prepend(html);
                    setColorAndFontStyle('footer .icon_preview:first-child',block)
                }
            }

            if(response.data.data?.manual) {
                let blocks = response.data.data.manual
                blocks.map(async (block, index) => {
                    if(block.selected_products.length == 0 && block.selected_collections.length == 0) {
                        let html = await getBlockHTML(block)
                        html = ReactDOMServer.renderToString(html);
                        $('[data-id="' + block.manual_placement_id + '"]').html(html);
                        setColorAndFontStyle('[data-id="' + block.manual_placement_id + '"]', block)
                    }
                });
            }
        })
        .catch((error) => {
        })
    },[])
    return ("");
}

export default Product;
