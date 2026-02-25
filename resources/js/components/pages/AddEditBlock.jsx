import {
    Box,
    Button,
    Icon,  
    Layout,
    LegacyStack,
    Modal,
    Page,
    Text,
} from '@shopify/polaris';
import {MobileChevronMajor} from '@shopify/polaris-icons';
/*const BlockSection = lazy(()=>  import('../IconBlock/BlockSection.jsx'));
const IconsSection = lazy(()=>  import('../IconBlock/IconsSection.jsx'));
const StyleSection = lazy(()=>  import('../IconBlock/StyleSection.jsx'));*/
import $ from 'jquery';
import {useContext, useEffect, useState} from 'react';
import {useLocation, useNavigate} from 'react-router-dom';
import {toast} from 'react-toastify';
import {AppContext} from '../../context/AppProvider';
import BlockService from '../../services/BlockService';
import IconService from '../../services/IconService';
import {getStyle, showToast} from '../../utils/constant';
import AppExtensionBanner from '../Banner/AppExtensionBanner';
import PageViewsLimitCrossedBanner from '../Banner/PageViewsLimitCrossedBanner';
import UpgradePlanBanner from '../Banner/UpgradePlanBanner';
import BlockSection from '../IconBlock/BlockSection';
import IconsSection from '../IconBlock/IconsSection';
import StyleSection from '../IconBlock/StyleSection';

const AddEditBlock = (buttons) => {
    const {icon_blocks, setIconBlocks} = useContext(AppContext)
    const [blockEditId, setBlockEditId] = useState('');
    const [isDirty, setIsDirty] = useState(false);
    const [showDiscardModal, setShowDiscardModal] = useState(false);
    const [showDeleteBlockConfirmation, setShowDeleteBlockConfirmation] = useState(
        false);
    const [block, setBlock] = useState({
        block_settings: {
            name: '',
            layout: 'vertical',
            header_text: '',
            headerTextSettings: {
                font: 'Poppins',
                size: 25,
                weight: '600',
                alignment: 'center',
                color: '#000000',
            },
            position: 'all-products',
            icons_per_row_desktop: '4',
            icons_per_row_mobile: '2',
            manual_placement_id: null,
            selected_products: [],
            selected_collections: [],
        },
        icons: [
            {
                icon: {
                    name: '',
                    id: '',
                    type: '',
                    src: 'https://iconito.s3.us-west-2.amazonaws.com/default_icons/ecommerce/ecommerce-add-cart.svg',
                    svg: 'https://iconito.s3.us-west-2.amazonaws.com/default_icons/ecommerce/ecommerce-add-cart.svg',
                },
                position: 1,
                title: 'Title 1',
                subtitle: 'Subtitle 1',
                show_link: false,
                show_condition: false,
                link: '',
                open_to_new_tab: false,
                tags: '',
            }, {
                icon: {
                    name: '',
                    id: '',
                    type: '',
                    src: 'https://iconito.s3.us-west-2.amazonaws.com/default_icons/ecommerce/ecommerce-add-cart.svg',
                    svg: 'https://iconito.s3.us-west-2.amazonaws.com/default_icons/ecommerce/ecommerce-add-cart.svg',
                },
                position: 2,
                title: 'Title 2',
                subtitle: 'Subtitle 2',
                show_link: false,
                show_condition: false,
                link: '',
                open_to_new_tab: false,
                tags: '',
            }, {
                icon: {
                    name: '',
                    id: '',
                    type: '',
                    src: 'https://iconito.s3.us-west-2.amazonaws.com/default_icons/ecommerce/ecommerce-add-cart.svg',
                    svg: 'https://iconito.s3.us-west-2.amazonaws.com/default_icons/ecommerce/ecommerce-add-cart.svg',
                },
                position: 3,
                title: 'Title 3',
                subtitle: 'Subtitle 3',
                show_link: false,
                show_condition: false,
                link: '',
                open_to_new_tab: false,
                tags: '',
            },
        ],
        icon_settings: {
            size: 50,
            color_settings: {
                block_background_color: '#ffffff',
                icon_color: '#000000',
                title_color: '#000000',
                subtitle_color: '#000000',
                is_transparent: false,
            },
            typography_settings: {
                title_font_size: 14,
                subtitle_font_size: 12,
                title_font_style: 'bold',
                subtitle_font_style: 'regular',
            },
            block_size: 0,
            goes_up: 0,
            goes_down: 0,
            space_between_blocks: 100,
        },
        add_more: {
            name: '',
            svg: '',
        },
    });
    const [pageTab, setPageTab] = useState('block');
    const [preview, setPreview] = useState('desktop');
    const [showUpgradePlanBanner, setShowUpgradePlanBanner] = useState(false);
    const location = useLocation();
    const navigate = useNavigate();

    useEffect(() => {
        if (block.block_settings?.layout === 'horizontal' && block.icons.length > block.block_settings.icons_per_row_desktop) {
            $('.tb_horizontal .tb-icon-block-icon').css('justify-content', 'left');
        }else{
            $('.tb_horizontal .tb-icon-block-icon').css('justify-content', 'center');
        }
    }, [block.block_settings?.layout,block.block_settings?.icons_per_row_desktop]);

    useEffect(()=>{
        let offset = $(".tb-icon-img-container")[0]?.offsetLeft;
        let textOffset = $(`.tb-icon-content`)[0]?.offsetLeft;

        if(offset > textOffset){
            offset = textOffset;
        }
        let index = (preview === 'desktop'
            ? block.block_settings?.icons_per_row_desktop
            : block.block_settings?.icons_per_row_mobile)
        if(index >  block.icons.length){
            index = block.icons.length
        }

        let off = $(".tb-icon-block-wrapper")[0]?.offsetLeft;
        let rightOffsetOuter = $(".tb-icon-block-icon")[index -1]?.offsetLeft;
        let rightOffset = $(".tb-icon-block-icon .tb-icon-img-container")[index-1]?.offsetLeft;
        let rightTextOffset = $(`.tb-icon-content`)[index-1 ]?.offsetLeft;

        rightOffset = rightOffset-rightOffsetOuter;
        rightTextOffset = rightTextOffset-rightOffsetOuter;

        if(rightOffset > rightTextOffset){
            rightOffset =  rightTextOffset
        }

        $(".tb-header-title").css('margin-left',`${offset}px`)
        if(rightOffset){
            $(".tb-header-title").css('margin-right',`${rightOffset + off}px`)
        }

        let bg = block.icon_settings.color_settings.is_transparent ? 'transparent' : block.icon_settings.color_settings.block_background_color
        $(`.icon_preview`).css('background-color',bg);
    });

    useEffect(() => {
        let title_style = block.icon_settings.typography_settings.title_font_style;
        let subtitle_style = block.icon_settings.typography_settings.subtitle_font_style;

        $('.tb-icon-block-icon-title').map((i, e) => {
            $(e).removeAttr('style');
            $(e).css(getStyle(title_style));
            $(e).
                css({
                    'font-size': `${block.icon_settings.typography_settings.title_font_size}px`,
                    color: `${block.icon_settings.color_settings.title_color}`,
                });
        });

        $('.tb-icon-block-icon-subtitle').map((i, e) => {
            $(e).removeAttr('style');
            $(e).css(getStyle(subtitle_style));
            $(e).
                css({
                    'font-size': `${block.icon_settings.typography_settings.subtitle_font_size}px`,
                    color: `${block.icon_settings.color_settings.subtitle_color}`,
                });
        });
    }, [
        block.icon_settings.typography_settings.title_font_style,
        block.icon_settings.typography_settings.subtitle_font_style,
        block.icons]);

    useEffect(() => {
        let block_id = location.state?.block_id;
        if (block_id != undefined && block_id != null) {
            setBlockEditId(block_id);
            retriveSingleIconBlock(block_id);
        } else {
            retriveDefaultIcons();
        }
    }, []);

    useEffect(()=>{
        $('.preview-image-wrapper').css('width', `${block.icon_settings.size}px`);
        $('.preview-image-wrapper').css('height', `${block.icon_settings.size}px`);
    },[block.icon_settings.size]);

    const retriveDefaultIcons = () => {
        IconService.getDefaultIcons().then((response) => {
            if (response.data?.data) {
                let data = response.data.data;
                let icons = [...block.icons];
                data?.icons.map((icon, index) => {
                    icons[index].icon.svg = icon.svg;
                    icons[index].icon.id = icon.id;
                    icons[index].icon.type = icon.type;
                    icons[index].icon.name = icon.name;
                });
                let add_more = data.add_more;

                setBlock({...block, ['icons']: icons, ['add_more']: add_more});
            }
        }).catch((error) => {
            showToast(error.response.data.message, {type: 'error'});
        }).finally(() => {

        });
    };

    const retriveSingleIconBlock = (block_id) => {
        BlockService.getSingleIconBlock(block_id).then((response) => {
            if (response.data?.data) {
                let _block = response.data.data;

                let block_settings = {
                    id: _block.id,
                    name: _block.name,
                    layout: _block.layout,
                    header_text: _block.header_text,
                    headerTextSettings: _block.header_text_settings,
                    position: _block.position,
                    icons_per_row_desktop: _block.icons_per_row_desktop,
                    icons_per_row_mobile: _block.icons_per_row_mobile,
                    manual_placement_id: _block.manual_placement_id,
                    selected_products: _block.selected_products?.map(a => a.id),
                    selected_collections: _block.selected_collections?.map(
                        a => a.id),
                };

                let icon_settings = {
                    size: _block.size,
                    color_settings: _block.color_settings,
                    typography_settings: _block.typography_settings,
                    block_size: _block.block_size,
                    goes_up: _block.goes_up,
                    goes_down: _block.goes_down,
                    space_between_blocks: _block.space_between_blocks,
                };

                let prepare_icons = _block.app_icons.map((icon, index) => {
                    return {
                        icon: {
                            name: icon.app_icon.name,
                            id: icon.app_icon.id,
                            type: 'app-icon',
                            src: icon.app_icon.url,
                            svg: icon?.svg ? icon.svg : icon.app_icon.url,
                        },
                        id: icon.id,
                        title: icon.title,
                        subtitle: icon.subtitle,
                        show_link: icon.show_link === 1,
                        show_condition: icon.show_condition === 1,
                        link: icon.link,
                        open_to_new_tab: icon.open_to_new_tab === 1,
                        tags: icon.tags,
                        position: icon.position,
                    };
                });

                _block.custom_icons.map((icon, index) => {
                    prepare_icons.push({
                        icon: {
                            name: icon.custom_icon.name,
                            id: icon.custom_icon.id,
                            type: 'custom',
                            src: icon.custom_icon.url,
                            svg: icon.custom_icon.url,
                        },
                        id: icon.id,
                        title: icon.title,
                        subtitle: icon.subtitle,
                        show_link: icon.show_link === 1,
                        show_condition: icon.show_condition === 1,
                        link: icon.link,
                        open_to_new_tab: icon.open_to_new_tab === 1,
                        tags: icon.tags,
                        position: icon.position,
                    });
                });

                let temp = prepare_icons.sort(
                    (a, b) => a.position - b.position);

                setBlock({
                    ...block,
                    ['block_settings']: block_settings,
                    ['icon_settings']: icon_settings,
                    ['icons']: temp,
                    ['add_more']: _block.add_more,
                });

            }
        }).catch((error) => {
            showToast(error.response.data.message, {type: 'error'});
        }).finally(() => {

        });
    };

    useEffect(() => {
        $('.preview-image-wrapper > svg').
            css('fill', block.icon_settings.color_settings.icon_color);
        $('.preview-image-wrapper > svg  g').
            css('fill', block.icon_settings.color_settings.icon_color);
        $('.preview-image-wrapper > svg  path').
            css('fill', block.icon_settings.color_settings.icon_color);
    }, [block.icon_settings.color_settings.icon_color, block.icons]);

    const handleBlockChange = (block_settings) => {
        setBlock({...block, ['block_settings']: block_settings});
        if (!isDirty) {
            setIsDirty(true);
        }
    };
    const handleIconsChange = (icons) => {
        setBlock({...block, ['icons']: icons});
        if (!isDirty) {
            setIsDirty(true);
        }
    };

    const handleIconSettingChange = (settings) => {
        setBlock({...block, ['icon_settings']: settings});
        if (!isDirty) {
            setIsDirty(true);
        }
    };

    const handleBlockSave = () => {
        let action = 'Save';

        if (blockEditId) {
            block.edit_id = blockEditId;
            action = 'Update';
        }

        if (block.icons.length == 0) {
            showToast(`Need At Least One Icon To ${action} Block`, {type: 'error'});
            return;
        }
        BlockService.saveBlock(block).then((response) => {
            if (response.data.data) {
                // let product_page_blocks = response.data?.data.product_page_blocks;
                let action = blockEditId ? 'Updated' : 'Created';
                showToast(`Icon Block ${action} Successfully`, {type: 'success'});
                if (blockEditId) {
                    let bs = icon_blocks;
                    bs = bs.map((b,i) => {
                        if(b.id == blockEditId){
                            return response.data.data;
                        }else{
                            return b;
                        }
                    })

                    setIconBlocks([...bs]);
                }else{
                    let bs = icon_blocks;
                    bs.push(response.data.data);

                    setIconBlocks([...bs]);
                }

               /* if(product_page_blocks){
                    toast('You have other icon blocks for product page so to display this icon block make sure to disabled them',{type:'info'});
                }*/
                navigate('/');
            }
        }).catch((error) => {
            if(error.response.status === 422 && error.response.data.message.includes('block settings.name')){
                let block_settings = block.block_settings;
                block_settings.block_required = true;
                setBlock({...block,['block_settings'] :block_settings});
            }else{
                showToast(error.response.data.message, {type: 'error'});
            }
        });
    };

    const handleBlockDelete = () => {
        setShowDeleteBlockConfirmation(false);
        BlockService.deleteBlock(blockEditId).then((response) => {
            if (response.data.success) {
                let _blocks = icon_blocks.filter((b) => b.id !== blockEditId);
                // setBlocks(_blocks);
                setIconBlocks([..._blocks]);
                showToast(`Icon Block Deleted Successfully`, {type: 'success'});
                navigate('/');
            }
        }).catch((error) => {
            showToast(error.response.data.message, {type: 'error'});
        });
    };

    const renderTabContent = () => {
        switch (pageTab) {
            case 'block':
                return <BlockSection
                    block={block.block_settings}
                    blockChange={handleBlockChange}
                    editId={blockEditId}
                    handleBlockSave={handleBlockSave}
                    handleBlockDelete={() => setShowDeleteBlockConfirmation(
                        true)}/>;
            case 'icons':
                return <IconsSection
                    _icons={block.icons}
                    add_more={block.add_more}
                    iconsChange={handleIconsChange}
                    editId={blockEditId}
                    handleBlockSave={handleBlockSave}
                    handleBlockDelete={() => setShowDeleteBlockConfirmation(
                        true)}
                    showUpgradePlanBanner={() => setShowUpgradePlanBanner(
                        true)}/>;
            case 'style':
                return <StyleSection
                    icon_settings={block.icon_settings}
                    iconsSettingChange={handleIconSettingChange}
                    editId={blockEditId}
                    handleBlockSave={handleBlockSave}
                    handleBlockDelete={() => setShowDeleteBlockConfirmation(
                        true)}/>;
            default:
                return null;
        }
    };

    const handleNavigateToHome = () => {
        if (isDirty) {
            setShowDiscardModal(true);
            return;
        }
        navigate('/');
    };

    const handleDiscard = () => {
        setShowDiscardModal(false);
        navigate('/');
    };

    return (
        <Page fullWidth>
            <AppExtensionBanner />
            <PageViewsLimitCrossedBanner />
            <Modal open={showDiscardModal} title={'Discard Change'}
                   onClose={() => setShowDiscardModal(false)}>
                <Modal.Section>
                    <Text variant={'bodyLg'}> Do you want to discard the
                        changes? </Text>
                </Modal.Section>
                <Modal.Section>
                    <LegacyStack distribution={'trailing'}
                                 spacing={'extraTight'}>
                        <Button onClick={() => setShowDiscardModal(
                            false)}> Close </Button>
                        <Button onClick={() => handleDiscard()}
                                destructive> Discard </Button>
                    </LegacyStack>
                </Modal.Section>
            </Modal>

            <Modal open={showDeleteBlockConfirmation} title={'Delete Block'}
                   onClose={() => setShowDeleteBlockConfirmation(false)}>
                <Modal.Section>
                    <Text variant={'bodyLg'}> Do you want to delete icon
                        block? </Text>
                </Modal.Section>
                <Modal.Section>
                    <LegacyStack distribution={'trailing'}
                                 spacing={'extraTight'}>
                        <Button onClick={() => setShowDeleteBlockConfirmation(
                            false)}> Close </Button>
                        <Button onClick={() => handleBlockDelete()}
                                destructive> Delete </Button>
                    </LegacyStack>
                </Modal.Section>
            </Modal>

            <div id={'block-breadcrumb'}>
                <div blockAlign={'center'}>
                    <Button onClick={() => handleNavigateToHome()}>
                        <Icon source={MobileChevronMajor}></Icon>
                    </Button>
                    <Text variant={'headingMd'} as={'h5'}>Icon Blocks</Text>
                </div>
            </div>
            <div className="add-icon-blocks">
                <Layout>
                    <Layout.Section oneHalf>
                        <div id={'block-tab-buttons'}>
                            <Button primary={pageTab === 'block'}
                                    onClick={() => setPageTab('block')}>
                                <Text variant={'headingLg'}
                                      as={'h5'}> Block </Text>
                            </Button>
                            <Button primary={pageTab === 'icons'}
                                    onClick={() => setPageTab('icons')}>
                                <Text variant={'headingLg'}
                                      as={'h5'}> Icons </Text>
                            </Button>
                            <Button primary={pageTab === 'style'}
                                    onClick={() => setPageTab('style')}>
                                <Text variant={'headingLg'}
                                      as={'h5'}> Style </Text>
                            </Button>
                        </div>

                        <div>
                            {renderTabContent()}
                        </div>

                    </Layout.Section>
                    <Layout.Section oneHalf>
                        <Box background="action-secondary"
                             id={'preview-section'}>
                            <Text variant={'headingXl'} alignment={'center'}
                                  as={'h3'}>Live Preview</Text>
                            <div id={'block-tab-buttons'}
                                 className={'preview-tab'}>
                                <Button primary={preview === 'desktop'}
                                        onClick={() => setPreview('desktop')}>
                                    <Text variant={'headingMd'}
                                          as={'h5'}> Desktop </Text>
                                </Button>
                                <Button primary={preview === 'mobile'}
                                        onClick={() => setPreview('mobile')}>
                                    <Text variant={'headingMd'}
                                          as={'h5'}> Mobile </Text>
                                </Button>
                            </div>
                            <div className={'icon_preview ' +
                                (preview === 'desktop'
                                    ? 'desktop_view'
                                    : 'mobile_view')}>


                                <div className={'tb-icon-block-container ' +
                                    (block.block_settings?.layout ===
                                    'horizontal'
                                        ? 'tb_horizontal'
                                        : 'tb_vertical')} style={{
                                    maxWidth: '100%',
                                    backgroundColor: `${block.icon_settings.color_settings.is_transparent
                                        ? 'transparent'
                                        : block.icon_settings.color_settings.block_background_color}`,
                                }}>
                                    <div className="tb-header-title"
                                         style={{
                                             margin: '0px 50px',
                                             fontFamily: `${block.block_settings?.headerTextSettings?.font}`,
                                             fontSize: `${block.block_settings?.headerTextSettings?.size}px`,
                                             fontWeight: `${block.block_settings?.headerTextSettings?.weight}`,
                                             textAlign: `${block.block_settings?.headerTextSettings?.alignment}`,
                                             color: `${block.block_settings?.headerTextSettings?.color}`,
                                             backgroundColor: `${block.icon_settings.color_settings.is_transparent
                                                 ? 'transparent'
                                                 : block.icon_settings.color_settings.block_background_color}`,
                                         }}>
                                        {block.block_settings?.header_text}
                                    </div>
                                    <div className="tb-icon-block-wrapper"
                                         style={{
                                             margin: '0px auto',
                                             background: `${block.icon_settings.color_settings.is_transparent
                                                 ? 'transparent'
                                                 : block.icon_settings.color_settings.block_background_color}`,
                                             padding: `${block.icon_settings.block_size}px 0px`,
                                             marginTop: `${block.icon_settings.goes_up}px`,
                                             marginBottom: `${block.icon_settings.goes_down}px`,
                                             maxWidth: `${block.icon_settings.space_between_blocks}%`,
                                         }}>
                                        {
                                            block.icons?.map((icon, index) => {
                                                return (
                                                    <div
                                                        className="tb-icon-block"
                                                        key={index}
                                                        style={{
                                                            flexBasis: `${100 /
                                                            (preview ===
                                                            'desktop'
                                                                ? block.block_settings?.icons_per_row_desktop
                                                                : block.block_settings?.icons_per_row_mobile)}%`,
                                                        }}>
                                                        <div
                                                            className="tb-icon-block-icon">
                                                            <div
                                                                className="tb-icon-img-container"
                                                                style={{
                                                                    width: `${block.icon_settings.size}px`,
                                                                    height: `${block.icon_settings.size}px`,
                                                                }}>
                                                                {
                                                                    (icon.icon.svg?.startsWith(
                                                                            '<svg') ||
                                                                        icon.icon.svg?.startsWith(
                                                                            '<?xml'))
                                                                        ?
                                                                        <div

                                                                            className={'preview-image-wrapper'}
                                                                            dangerouslySetInnerHTML={{__html: icon.icon.svg}}/>
                                                                        :
                                                                        <img
                                                                            className={'preview-image-wrapper my-image'}
                                                                            src={icon.icon.svg}/>
                                                                }
                                                            </div>
                                                            <div
                                                                className="tb-icon-content">
                                                <span
                                                    className="tb-icon-block-icon-title"
                                                    style={{
                                                        fontSize: `${block.icon_settings.typography_settings.title_font_size}px`,
                                                        color: `${block.icon_settings.color_settings.title_color}`,
                                                    }}> {icon.title} </span>
                                                                <span
                                                                    className="tb-icon-block-icon-subtitle"
                                                                    style={{
                                                                        fontSize: `${block.icon_settings.typography_settings.subtitle_font_size}px`,
                                                                        color: `${block.icon_settings.color_settings.subtitle_color}`,
                                                                    }}> {icon.subtitle}</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                );
                                            })
                                        }
                                    </div>
                                </div>
                            </div>
                        </Box>
                        {
                            showUpgradePlanBanner ?
                                <div className={'add-icon-upgrade-banner'}>
                                    <UpgradePlanBanner
                                        hideBanner={() => setShowUpgradePlanBanner(
                                            false)}/>
                                </div>
                                : null
                        }
                    </Layout.Section>
                </Layout>
            </div>
        </Page>
    );
};

export default AddEditBlock;
