import {useEffect} from 'react';
import StorefrontService from '../services/StorefrontService';
import $ from 'jquery'
import ReactDOMServer from "react-dom/server"
import { setColorAndFontStyle, getBlockHTML } from './common';

const Index = ({data}) => {
    useEffect(() => {
        StorefrontService.getIndexPageIconBlocks(data.shop_domain)
        .then(async (response) => {

            if(response.data.data?.header) {
                let block = response.data.data.header
                let html = await getBlockHTML(block)
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

            if(response.data.data?.homepage) {
                let block = response.data.data.homepage
                let html = await getBlockHTML(block)
                html = ReactDOMServer.renderToString(html);
                if($("#render_iconito_block_index").length){
                    $("#render_iconito_block_index").html(html);
                    setColorAndFontStyle('#render_iconito_block_index',block)
                }else{
                    var homeBannerSectionId = $("main").find(".shopify-section").first().prop('id');
                    var homeBannerSectionClass = $("main").find(".shopify-section").first().prop('class');
                    var homeBannerSectionTagId = $("main").find("section").first().prop('id');
                    var homeBannerSectionTagClass = $("main").find("section").first().prop('class');

                    if(homeBannerSectionId != undefined && homeBannerSectionId != ''){
                        $("#"+homeBannerSectionId).after(html);
                        setColorAndFontStyle("#"+homeBannerSectionId + " + .icon_preview",block)
                    }else if(homeBannerSectionClass != undefined && homeBannerSectionClass != ''){
                        homeBannerSectionClass = homeBannerSectionClass.replace(/ /g, ".");
                        $("."+homeBannerSectionClass).after(html);
                        setColorAndFontStyle("."+homeBannerSectionClass + " + .icon_preview",block)
                    }else if(homeBannerSectionTagId != undefined && homeBannerSectionTagId != ''){
                        $("#"+homeBannerSectionTagId).after(html);
                        setColorAndFontStyle("#"+homeBannerSectionTagId + " + .icon_preview",block)
                    }else{
                        homeBannerSectionTagClass = homeBannerSectionTagClass.replace(/ /g, ".");
                        $("."+homeBannerSectionTagClass).after(html);
                        setColorAndFontStyle("."+homeBannerSectionTagClass + " + .icon_preview",block)
                    }
                }
            }

            if(response.data.data?.footer) {
                let block = response.data.data.footer
                let html =await getBlockHTML(block)
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
                    if(block.selected_products.length == 0 && block.selected_collections.length == 0){
                        let html = await getBlockHTML(block)
                        html = ReactDOMServer.renderToString(html);
                        $('[data-id="' + block.manual_placement_id + '"]').html(html);
                        setColorAndFontStyle('[data-id="' + block.manual_placement_id + '"]',block)
                    }
                });
            }
        })
        .catch((error) => {
        })
    },[])

    return ("");
}

export default Index;
