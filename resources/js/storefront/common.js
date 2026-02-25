import $ from 'jquery';
import {getStyle} from '../utils/constant';
let cloudfront_icon_host = process.env.MIX_CLOUDFRONT_ICON_HOST

export const setColorAndFontStyle = (prefix, block) => {
    if(block?.layout === 'horizontal' && block.icons.length > block.icons_per_row_desktop){
        $(`${prefix} .tb_horizontal .tb-icon-block-icon`).css('justify-content', 'left');
    }

    let offset = $(`${prefix} .tb-icon-img-container`)[0]?.offsetLeft;
    let textOffset = $(`${prefix} .tb-icon-content`)[0]?.offsetLeft;
    if(offset > textOffset){
        offset = textOffset;
    }

    let index = ( isDesktopDevice()
        ? block?.icons_per_row_desktop
        : block?.icons_per_row_mobile)
    if(index >  block.icons.length){
        index = block.icons.length
    }

    let off = $(`${prefix} .tb-icon-block-wrapper`)[0]?.offsetLeft;
    let rightOffsetOuter = $(`${prefix} .tb-icon-block-icon`)[index -1]?.offsetLeft;
    let rightOffset = $(`${prefix} .tb-icon-block-icon .tb-icon-img-container`)[index -1]?.offsetLeft;
    let rightTextOffset = $(`${prefix} .tb-icon-content`)[index -1]?.offsetLeft;

    rightOffset = rightOffset-rightOffsetOuter;
    rightTextOffset = rightTextOffset-rightOffsetOuter;

    if(rightOffset > rightTextOffset){
        rightOffset = rightTextOffset;
    }

    $(`${prefix} .tb-header-title`).css('margin-left',`${offset}px`)
    if(rightOffset){
        $(`${prefix} .tb-header-title`).css('margin-right',`${rightOffset + off}px`)
    }

    $(`${prefix} .preview-image-wrapper > svg`).
        css('fill', block.color_settings.icon_color);
    $(`${prefix} .preview-image-wrapper > svg > g`).
        css('fill', block.color_settings.icon_color);

    $(`${prefix} .preview-image-wrapper > svg path`).
        css('fill', `${block.color_settings.icon_color}`);

  /*  $(`${prefix} .preview-image-wrapper > svg > g > g > path`).
        css('fill', `${block.color_settings.icon_color} !important`);*/

    let font = block?.header_text_settings?.font;

    let title_style = block.typography_settings.title_font_style;
    let subtitle_style = block.typography_settings.subtitle_font_style;

    $("#load-font").append(`<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=${font}:wght@200;300;400;500;600;700;800;900&amp;display=swap">`);

    $(`${prefix} .tb-icon-block-icon-title`).map((i, e) => {
        $(e).removeAttr('style');
        $(e).css(getStyle(title_style));
    });

    $(`${prefix} .tb-icon-block-icon-subtitle`).map((i, e) => {
        $(e).removeAttr('style');
        $(e).css(getStyle(subtitle_style));
    });

    let bg = block.color_settings.is_transparent ? 'transparent' : block.color_settings.block_background_color
    if(prefix.includes('.icon_preview')){
        $(`${prefix}`).css('background-color',bg);
    }else{
        $(`${prefix} .icon_preview`).css('background-color',bg);
    }

    $(`${prefix} .tb-icon-block-icon-title`).map((i, e) => {
        $(e).removeAttr('style');
        $(e).css(getStyle(title_style));
        $(e).
            css({
                'font-size': `${block.typography_settings.title_font_size}px`,
                color: `${block.color_settings.title_color}`,
            });
    });

    $(`${prefix} .tb-icon-block-icon-subtitle`).map((i, e) => {
        $(e).removeAttr('style');
        $(e).css(getStyle(subtitle_style));
        $(e).
            css({
                'font-size': `${block.typography_settings.subtitle_font_size}px`,
                color: `${block.color_settings.subtitle_color}`,
            });
    });
}


export function prepareIconLink(link){
    if(!link){
        return 'javascript:void(0);';
    }
    return link?.startsWith('http://') || link?.startsWith('https://')
        ? link
        : `https://${link}`
}

export function compareTags(product_tags, icon_tags){
    const lowercaseArr1 = product_tags.map(item => item.toLowerCase());
    const lowercaseArr2 = icon_tags.split(',').map(item => item.toLowerCase());

    return lowercaseArr1.some(item => lowercaseArr2.includes(item));
}

export const getBlockHTML = (block, product_tags = []) => {
    return (
        <div className={'icon_preview ' +
            (isDesktopDevice()
                ? 'desktop_view'
                : 'mobile_view')}>


            <div className={'tb-icon-block-container ' +
                (block?.layout ===
                'horizontal'
                    ? 'tb_horizontal'
                    : 'tb_vertical')} style={{
                maxWidth: '100% !important',
                backgroundColor: `${block.color_settings.is_transparent
                    ? 'transparent !important'
                    : block.color_settings.block_background_color}`,
            }}>

                <div className="tb-icon-block-wrapper"
                     style={{
                         margin: '0px auto',
                         background: `${block.color_settings.is_transparent
                             ? 'transparent'
                             : block.color_settings.block_background_color} !important`,
                         padding: `${block.block_size}px 0px !important`,
                         marginTop: `${block.goes_up}px !important`,
                         marginBottom: `${block.goes_down}px !important`,
                         maxWidth: `${block.space_between_blocks}% !important`,
                     }}>
                    <div className="tb-header-title"
                         style={{
                             margin: '0px 50px',
                             fontFamily: `${block?.header_text_settings?.font} !important`,
                             fontSize: `${block?.header_text_settings?.size}px !important`,
                             fontWeight: `${block?.header_text_settings?.weight} !important`,
                             width:'100%',
                             textAlign: `${block?.header_text_settings?.alignment} !important`,
                             color: `${block?.header_text_settings?.color} !important`,
                             backgroundColor: `${block.color_settings.is_transparent
                                 ? 'transparent !important'
                                 : block.color_settings.block_background_color}`,
                         }}>
                        {block?.header_text}
                    </div>
                    {
                        block.icons?.map((icon, index) => {
                            return (
                                ((icon.tags == null || icon.tags == '' || product_tags.length == 0) || (icon.tags != null && icon.show_condition && compareTags(product_tags, icon.tags))) ?
                                <div
                                    className="tb-icon-block"
                                    key={index}
                                    style={{
                                        flexBasis: `${100 /
                                        ( isDesktopDevice()
                                            ? block?.icons_per_row_desktop
                                            : block?.icons_per_row_mobile)}% !important`,
                                    }}>
                                    <div
                                        className="tb-icon-block-icon">
                                        <div
                                            className="tb-icon-img-container"
                                            style={{
                                                width: `${block.size}px !important`,
                                                height: `${block.size}px !important`,
                                                cursor: `${icon.show_link ? 'pointer' : 'inherit'}`
                                            }}
                                        > <a href={prepareIconLink(icon.link)} target={icon.link ? (icon.open_to_new_tab ? "_blank" : "_self") : ''}>
                                            {
                                                icon.icon_type == 'custom' ?
                                                    (icon.custom_icon?.svg?.startsWith(
                                                            '<svg') ||
                                                        icon.custom_icon?.svg?.startsWith(
                                                            '<?xml'))
                                                        ?
                                                        <div
                                                            className={'preview-image-wrapper'}
                                                            dangerouslySetInnerHTML={{__html: icon.custom_icon?.svg}}/>
                                                        :
                                                        <img
                                                            className={'preview-image-wrapper my-image'}
                                                            src={ cloudfront_icon_host + icon.custom_icon?.url.split(".com")[1]}/>
                                                    :
                                                (icon.app_icon?.svg?.startsWith(
                                                        '<svg') ||
                                                    icon.app_icon?.svg?.startsWith(
                                                        '<?xml'))
                                                    ?
                                                    <div
                                                        className={'preview-image-wrapper'}
                                                        dangerouslySetInnerHTML={{__html: icon.app_icon?.svg}}/>
                                                    :
                                                    <img
                                                        className={'preview-image-wrapper my-image'}
                                                        src={ cloudfront_icon_host + icon.app_icon?.url.split(".com")[1]}/>
                                            }
                                        </a>
                                        </div>
                                        <div
                                            className="tb-icon-content">
                                                <span
                                                    className="tb-icon-block-icon-title"
                                                    style={{
                                                        fontSize: `${block.typography_settings.title_font_size}px !important`,
                                                        color: `${block.color_settings.title_color} !important`,
                                                    }}> {icon.title} </span>
                                            <span
                                                className="tb-icon-block-icon-subtitle"
                                                style={{
                                                    fontSize: `${block.typography_settings.subtitle_font_size}px !important`,
                                                    color: `${block.color_settings.subtitle_color} !important`,
                                                }}> {icon.subtitle}</span>
                                        </div>
                                    </div>
                                </div>
                                    :null
                            );
                        })
                    }
                </div>
            </div>
        </div>
    )
}


function isDesktopDevice() {
    const userAgent = navigator.userAgent;
    const mobileKeywords = ['android', 'avantgo', 'blackberry', 'bolt', 'boost', 'cricket', 'docomo',
        'fone', 'hiptop', 'mini', 'mobi', 'palm', 'phone', 'pie', 'tablet',
        'up.browser', 'up.link', 'webos', 'wos'];

    // Check if any mobile keywords are present in the user agent string
    for (let i = 0; i < mobileKeywords.length; i++) {
        if (userAgent.indexOf(mobileKeywords[i]) !== -1) {
            return false;
        }
    }

    // Check if the viewport width matches a mobile device media query
    if (window.matchMedia && window.matchMedia('(max-width: 768px)').matches) {
        return false;
    }

    // Default to assuming it's a desktop device
    return true;
}
