import {
    Box,
    Button,
    Divider,
    LegacyStack,
    RadioButton,
    Select,
    Text,
    TextField,
} from '@shopify/polaris';
import React, {useContext, useEffect, useRef, useState} from 'react';
import ColorPickerPopover from '../../commons/ColorPickerPopover';
import {AppContext} from '../../context/AppProvider';
import {
    alignmentOptions,
    fontOptions,
    fontWeightOptions,
    uniqid,
} from '../../utils/constant';
import Footer from '../layout/Footer';
import CollectionModal from '../Modals/CollectionModal';
import ProductModal from '../Modals/ProductModal';

export default function BlockSection({
    block,
    editId,
    blockChange,
    handleBlockSave,
    handleBlockDelete,
}) {
    const codeRef = useRef(null);
    const [showCopyMessage, setShowCopyMessage] = useState(false);
    const [showProductModal, setShowProductModal] = useState(false);
    const [showCollectionModal, setShowCollectionModal] = useState(false);
    const [blockSettings, setBlockSettings] = useState(block);
    const {discard_flag, increaseDiscardFlag} = useContext(AppContext);

    useEffect(() => {
        if (editId && !blockSettings.id) {
            setBlockSettings(block);
            setFont(block.headerTextSettings.font);
        }
        if(block.id == undefined && (!editId || editId == '') && discard_flag == 1){
            setFont(block.headerTextSettings.font)
        }
    }, [block]);
    useEffect(() => {
        if (discard_flag != 1) {
            blockChange(blockSettings);
        }
    }, [blockSettings]);

    useEffect(() => {
        if (blockSettings.position == 'manual' &&
            ( !blockSettings.manual_placement_id || (editId && !blockSettings.manual_placement_id))) {
            setBlockSettings(
                {...blockSettings, ['manual_placement_id']: uniqid()});
        }
    }, [blockSettings.position]);

    const setFont = (value) =>{
        let el = document.getElementById('font-link');
        if(!el){
            var link = document.createElement('link');
            link.rel = 'stylesheet';
            link.id = 'font-link';
            link.href = 'https://fonts.googleapis.com/css2?family=' + value + ':wght@200;300;400;500;600;700;800;900&amp;display=swap';
            document.head.appendChild(link);
        }else{
            let href = new URL('https://fonts.googleapis.com/css2?family=""');
            href.searchParams.set('family', value);
            document.getElementById('font-link').href = href.toString();
        }
    }

    const handleCopyCode = (e) => {
        let text = document.getElementById('copy_block_code').innerText;
        navigator.clipboard.writeText(text);

        setShowCopyMessage(true);
        setTimeout(() => { setShowCopyMessage(false); }, 3000);
    };

    const handleBlockInputChange = (value, id) => {
        increaseDiscardFlag(discard_flag + 1);
        setBlockSettings({...blockSettings, [id]: value});
    };

    const handleHeaderTextChange = (value, id) => {
        increaseDiscardFlag(discard_flag + 1);
        if (id == 'font') {
            setFont(value)
            /*let el = document.getElementById('font-link');
            if(!el){
                var link = document.createElement('link');
                link.rel = 'stylesheet';
                link.id = 'font-link';
                link.href = 'https://fonts.googleapis.com/css2?family=' + value + ':wght@200;300;400;500;600;700;800;900&amp;display=swap';
                document.head.appendChild(link);
            }else{
                let href = new URL('https://fonts.googleapis.com/css2?family=""');
                href.searchParams.set('family', value);
                document.getElementById('font-link').href = href.toString();
            }*/
        }
        let header_text = {...blockSettings.headerTextSettings, [id]: value};
        setBlockSettings(
            {...blockSettings, ['headerTextSettings']: header_text});
    };

    const handleSelectedItemSave = (products) => {
        increaseDiscardFlag(discard_flag + 1);
        setShowProductModal(false);
        setBlockSettings({...blockSettings, ['selected_products']: products});
    };

    const handleSelectedCollectionSave = (collections) => {
        increaseDiscardFlag(discard_flag + 1);
        setShowCollectionModal(false);
        setBlockSettings(
            {...blockSettings, ['selected_collections']: collections});
    };

    return (
        <>
            {
                showProductModal ?
                    <ProductModal selected={blockSettings.selected_products}
                                  onSave={(products) => handleSelectedItemSave(
                                      products)}
                                  onClose={() => setShowProductModal(false)}/>
                    : null
            }

            {
                showCollectionModal ?
                    <CollectionModal
                        selected={blockSettings.selected_collections}
                        onSave={(collections) => handleSelectedCollectionSave(
                            collections)}
                        onClose={() => setShowCollectionModal(false)}/>
                    : null
            }


            <div className={'block-card'}>
                <Box background="action-secondary" borderRadius={2}
                     paddingBlockEnd={'5'} padding={'3'} title="Block">
                    <TextField
                        label="Name"
                        error={ (block?.block_required ? "This field is required" : '')}
                        id={'name'}
                        placeholder="Enter Block Name"
                        value={blockSettings.name}
                        onChange={(value, id) => handleBlockInputChange(value,
                            id)}
                    />
                </Box>
            </div>

            <div className={'block-card'}>
                <Box background="action-secondary" borderRadius={2}
                     paddingBlockEnd={'5'} padding={'3'} title="Block">
                    <Text variant={'headingMd'} as={'h1'}> Layout </Text>
                    <div align={'space-between'} blockAlign={'stretch'}>
                        <div className="layout-card">
                            <div className={'layout_border' +
                                (blockSettings.layout === 'vertical'
                                    ? ' tb_checked'
                                    : '')}
                                 onClick={() => handleBlockInputChange(
                                     'vertical', 'layout')}>
                                <input className="form-check-input" type="radio"
                                       name="icon_layout" id="vertical"
                                       value="vertical" onChange={() => {}}/>
                                <div className="round_circle"></div>
                                <div className="line_"></div>
                                <div className="line_1"></div>
                                <div className="line_2"></div>
                            </div>
                        </div>
                        <div className="layout-card">
                            <div className={'layout_border' +
                                (blockSettings.layout === 'horizontal'
                                    ? ' tb_checked'
                                    : '')}
                                 onClick={() => handleBlockInputChange(
                                     'horizontal', 'layout')}>
                                <input className="form-check-input" type="radio"
                                       name="icon_layout" id="horizontal"
                                       value="horizontal"/>
                                <div
                                    className="round_box_right_center d-flex">
                                    <div className="round_box_right">
                                        <div className="round_circle"></div>
                                    </div>
                                    <div className="line_of_">
                                        <div className="line_"></div>
                                        <div className="line_1"></div>
                                        <div className="line_2"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div className={'divider'}>
                        <Divider/>
                    </div>

                    <Text variant={'headingMd'} as={'h1'}> Text Settings </Text>
                    <p className={'text-subtitle'}> Add your custom Pitch
                        Message and customize the display.</p>
                    <Text variant={'headingMd'} as={'h1'}> Header text </Text>
                    <TextField
                        label=" "
                        placeholder="Enter header name"
                        id={'header_text'}
                        value={blockSettings.header_text}
                        onChange={(value, id) => handleBlockInputChange(value,
                            id)}
                    />
                    <div className={'text-setting-gap'}>
                        <div align="space-between">
                            <Select
                                label={<Text variant={'headingMd'}
                                             as={'h1'}> Fonts </Text>}
                                options={fontOptions}
                                id={'font'}
                                onChange={(value, id) => handleHeaderTextChange(
                                    value, id)}
                                value={blockSettings.headerTextSettings.font}

                            />
                            <TextField
                                label={<Text variant={'headingMd'}
                                             as={'h1'}> Size </Text>}
                                type="number"
                                suffix={'PX'}
                                max={50}
                                min={1}
                                id={'size'}
                                onChange={(value, id) => handleHeaderTextChange(
                                    value, id)}
                                value={blockSettings.headerTextSettings.size}
                            />
                            <Select
                                label={<Text variant={'headingMd'}
                                             as={'h1'}> Weight </Text>}
                                options={fontWeightOptions}
                                id={'weight'}
                                onChange={(value, id) => handleHeaderTextChange(
                                    value, id)}
                                value={blockSettings.headerTextSettings.weight}
                            />
                        </div>
                    </div>

                    <div className={'text-setting-align-color'}>
                        <div align="space-between">



                            <Select
                                label={<Text variant={'headingMd'}
                                             as={'h1'}> Alignment </Text>}
                                options={alignmentOptions}
                                id={'alignment'}
                                onChange={(value, id) => handleHeaderTextChange(
                                    value, id)}
                                value={blockSettings.headerTextSettings.alignment}
                            />

                            <TextField
                                label={<Text variant={'headingMd'}
                                             as={'h1'}> Color </Text>}
                                type="text"
                                id={'color'}
                                value={blockSettings.headerTextSettings.color}
                                prefix={
                                    <ColorPickerPopover
                                        value={blockSettings.headerTextSettings.color}
                                        onChange={(value) => handleHeaderTextChange(
                                            value.hex, 'color')}
                                    />
                                }
                                onChange={(value, id) => handleHeaderTextChange(
                                    value, id)}
                            />

                        </div>
                    </div>

                    <div className={'divider'}>
                        <Divider/>
                    </div>
                    <Text variant={'headingMd'} as={'h1'}> POSITON </Text>
                    <p> Block Positon in Website</p>
                    <div className={'position-options'}>
                        <LegacyStack wrap={false} vertical={true}
                                     spacing={'extraTight'}>
                            <RadioButton onChange={() => handleBlockInputChange(
                                'homepage', 'position')}
                                         checked={blockSettings.position ===
                                             'homepage'} name={'position'}
                                         value={'homepage'}
                                         label="Homepage Section"/>
                            <RadioButton
                                onChange={() => handleBlockInputChange('header',
                                    'position')}
                                checked={blockSettings.position === 'header'}
                                name={'position'} value={'header'}
                                label="Site Header"/>
                            <RadioButton
                                onChange={() => handleBlockInputChange('footer',
                                    'position')}
                                checked={blockSettings.position === 'footer'}
                                name={'position'} value={'footer'}
                                label="Site Footer"/>
                            <RadioButton onChange={() => handleBlockInputChange(
                                'all-products', 'position')}
                                         checked={blockSettings.position ===
                                             'all-products'} name={'position'}
                                         value={'all-products'}
                                         label="All products (Product page)"/>
                            <RadioButton onChange={() => handleBlockInputChange(
                                'selected-products', 'position')}
                                         checked={blockSettings.position ===
                                             'selected-products'}
                                         name={'position'}
                                         value={'selected-products'}
                                         label="Specific product (Product page)"/>
                            <RadioButton onChange={() => handleBlockInputChange(
                                'collection-products', 'position')}
                                         checked={blockSettings.position ===
                                             'collection-products'}
                                         name={'position'}
                                         value={'collection-products'}
                                         label="Specific collection (Product page)"/>
                            <RadioButton
                                onChange={() => handleBlockInputChange('cart',
                                    'position')}
                                checked={blockSettings.position === 'cart'}
                                name={'position'} value={'cart'}
                                label="Cart Page"/>
                            <RadioButton
                                onChange={() => handleBlockInputChange('manual',
                                    'position')}
                                checked={blockSettings.position === 'manual'}
                                name={'position'} value={'manual'}
                                label="Manual Placement (adding code snippet to your theme)"/>
                        </LegacyStack>
                    </div>

                    <div className={'selected-products-buttons'}>
                        {
                            blockSettings.position == 'selected-products'
                                ? <Button fullWidth
                                          onClick={() => setShowProductModal(
                                              true)}>
                                    {blockSettings.selected_products.length > 0
                                        ?
                                        `${blockSettings.selected_products.length} product(s) selected`
                                        : 'Select Products'
                                    }
                                </Button>
                                : null
                        }

                        {
                            blockSettings.position == 'collection-products'
                                ? <Button fullWidth
                                          onClick={() => setShowCollectionModal(
                                              true)}>
                                    {blockSettings.selected_collections.length > 0
                                        ?
                                        `${blockSettings.selected_collections.length} collection(s) selected`
                                        : 'Select Collections'
                                    }
                                </Button>
                                : null
                        }
                    </div>
                    {
                        blockSettings.position == 'manual'
                            ? <>
                                <div className={'selected-products-buttons'}>
                                    <Button fullWidth
                                            onClick={() => setShowProductModal(
                                                true)}>
                                        {blockSettings.selected_products.length > 0
                                            ?
                                            `${blockSettings.selected_products.length} product(s) selected`
                                            : 'Select Products'
                                        }
                                    </Button>
                                    <Button fullWidth
                                            onClick={() => setShowCollectionModal(
                                                true)}>
                                        {blockSettings.selected_collections.length >
                                        0
                                            ?
                                            `${blockSettings.selected_collections.length} collection(s) selected`
                                            : 'Select Collections'
                                        }
                                    </Button>
                                </div>
                                <div className="manual_code_section">
                                    <div className="manual_code">
                                        <code ref={codeRef} id={'copy_block_code'}
                                              className="copy_block_code">
                                            &lt;div data-id="{block.manual_placement_id}"&gt;&lt;/div&gt;
                                        </code>
                                        <div>
                                            <img onClick={() => handleCopyCode()}
                                                 id="copyClipboard"
                                                 src={'/images/copy.png'}/>
                                            {showCopyMessage ?
                                                <span
                                                    className="copied_message">Copied</span>
                                                : null
                                            }
                                        </div>
                                        <input type="hidden" name="manual_code"
                                               value="64363ca7388cf"/>
                                    </div>
                                    <span
                                        className="tb-desc">Open Online store &gt; Themes &gt; Actions -&gt; Edit code and paste this shortcode in the specific file and place where you want to show.</span>
                                    <span>If you need help, Please contact us via chat </span>
                                </div>
                            </>
                            : null
                    }


                    <div className={'divider'}>
                        <Divider/>
                    </div>

                    <Text variant={'headingMd'} as={'h1'}> STACKING </Text>

                    <div className="level1">
                        <div className="level2">
                            <div>
                                <TextField
                                    label={<Text variant={'headingSm'}
                                                 as={'h1'}> Icons displayed in one
                                        row (Desktop)</Text>}
                                    type="number"
                                    value={blockSettings.icons_per_row_desktop}
                                    id={'icons_per_row_desktop'}
                                    suffix={'PX'}
                                    onChange={(value, id) => handleBlockInputChange(
                                        value, id)}
                                    max={50}
                                    min={1}
                                />
                            </div>
                            <div>
                                <TextField
                                    label={<Text variant={'headingSm'}
                                                 as={'h1'}> Icons displayed in one
                                        row (Mobile)</Text>}
                                    type="number"
                                    value={blockSettings.icons_per_row_mobile}
                                    id={'icons_per_row_mobile'}
                                    suffix={'PX'}
                                    max={50}
                                    min={1}
                                    onChange={(value, id) => handleBlockInputChange(
                                        value, id)}
                                />
                            </div>
                        </div>
                    </div>

                </Box>
            </div>
            <div className={'block-action-buttons'}>
                <Button size={'large'} onClick={() => handleBlockSave()} primary
                > {editId ? 'Update' : 'Save'} </Button>
                {/*Delete If Edit Action*/}
                {
                    editId ?
                        <Button onClick={handleBlockDelete} size={'large'}
                                destructive
                        > Delete </Button>
                        : null
                }
            </div>

            <div className={'block-footer'}>
                <Footer></Footer>
            </div>
        </>
    );
}
