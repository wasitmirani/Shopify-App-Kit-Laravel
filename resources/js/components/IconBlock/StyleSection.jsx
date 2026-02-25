import {
    Box,
    Button,
    Checkbox,
    Divider,
    LegacyStack,
    RangeSlider,
    Select,
    Text,
    TextField,
} from '@shopify/polaris';
import React, {useContext, useEffect, useState} from 'react';
import ColorPickerPopover from '../../commons/ColorPickerPopover';
import {AppContext} from '../../context/AppProvider';
import {fontStyleOptions} from '../../utils/constant';
import Footer from '../layout/Footer';

export default function StyleSection({
    icon_settings,
    editId,
    iconsSettingChange,
    handleBlockSave,
    handleBlockDelete,
}) {
    const [iconSetting, setIconSetting] = useState(icon_settings);
    const {discard_flag, increaseDiscardFlag} = useContext(AppContext);

    useEffect(() => {
        if (discard_flag !== 1) {
            iconsSettingChange(iconSetting);
        }
    }, [iconSetting]);

    const handleIconSettingChange = (id, value) => {
        increaseDiscardFlag(discard_flag + 1);
        setIconSetting({...iconSetting, [id]: value});
    };

    const handleIconColorSettingChange = (id, value) => {
        increaseDiscardFlag(discard_flag + 1);
        let color_setting = {...iconSetting.color_settings, [id]: value};
        setIconSetting(
            {...iconSetting, ['color_settings']: color_setting});
    };

    const handleIconTypographySettingChange = (id, value) => {
        increaseDiscardFlag(discard_flag + 1);
        let typography_setting = {
            ...iconSetting.typography_settings,
            [id]: value,
        };
        setIconSetting(
            {...iconSetting, ['typography_settings']: typography_setting});
    };

    return (
        <>
            <div className={'block-card'}>
                <Box background="action-secondary" borderRadius={2}
                     paddingBlockEnd={'1'} title="Block">
                    <Text variant={'headingSm'} as={'h6'}> ICON SIZE </Text>
                    <div className={'range-wrapper'}>
                        <LegacyStack distribution={'fill'} alignment={'center'}>
                            <LegacyStack.Item>
                                <RangeSlider
                                    label={''}
                                    id={'size'}
                                    value={iconSetting.size}
                                    onChange={(
                                        value, id) => handleIconSettingChange(
                                        id, value)}
                                    output
                                    min={20}
                                    max={200}
                                />


                            </LegacyStack.Item>
                            <LegacyStack.Item>
                                <TextField
                                    label={''}
                                    id={'size'}
                                    value={iconSetting.size}
                                    onChange={(
                                        value, id) => handleIconSettingChange(
                                        id, value)}
                                    type={'number'}
                                    max={200}
                                    min={20}
                                    suffix={'PX'}
                                />
                            </LegacyStack.Item>
                        </LegacyStack>
                    </div>
                </Box>

                <div className={'divider'}>
                    <Divider/>
                </div>

                {/* COLORS */}

                <div className={'color-settings'}>
                    <Text variant={'headingMd'} as={'h1'}> COLORS </Text>
                </div>
                <div className={`style-color-settings`}>
                <div className="parent">
                    <div className="parent_child">
                        <div>
                            <div>
                                Background Color
                            </div>
                            <TextField
                                label={''}
                                type="text"
                                id={'block_background_color'}
                                value={iconSetting.color_settings.block_background_color}
                                prefix={
                                    <ColorPickerPopover
                                        value={iconSetting.color_settings.block_background_color}
                                        onChange={(value) => handleIconColorSettingChange(
                                            'block_background_color', value.hex)}
                                    />
                                }
                                onChange={(
                                    value, id) => handleIconColorSettingChange(id,
                                    value)}
                            />
                        </div>
                        <div>
                            <div>
                                Title Color
                            </div>
                            <TextField
                                label={''}
                                type="text"
                                id={'title_color'}
                                value={iconSetting.color_settings.title_color}
                                prefix={
                                    <ColorPickerPopover
                                        value={iconSetting.color_settings.title_color}
                                        onChange={(value) => handleIconColorSettingChange(
                                            'title_color', value.hex)}
                                    />
                                }
                                onChange={(
                                    value, id) => handleIconColorSettingChange(
                                    id, value)}
                            />
                        </div>
                    </div>
                </div>

                <div className="parent">
                    <div className="parent_child">
                        <div>
                            <div>
                                Icon Color
                            </div>
                            <TextField
                                label={''}
                                type="text"
                                id={'icon_color'}
                                value={iconSetting.color_settings.icon_color}
                                prefix={
                                    <ColorPickerPopover
                                        value={iconSetting.color_settings.icon_color}
                                        onChange={(value) => handleIconColorSettingChange(
                                            'icon_color', value.hex)}
                                    />
                                }
                                onChange={(
                                    value, id) => handleIconColorSettingChange(id,
                                    value)}
                            />
                        </div>
                        <div>
                            <div>
                                Subtitle Color
                            </div>

                            <TextField
                                label={''}
                                type="text"
                                id={'subtitle_color'}
                                value={iconSetting.color_settings.subtitle_color}
                                prefix={
                                    <ColorPickerPopover
                                        value={iconSetting.color_settings.subtitle_color}
                                        onChange={(value) => handleIconColorSettingChange(
                                            'subtitle_color', value.hex)}
                                    />
                                }
                                onChange={(
                                    value, id) => handleIconColorSettingChange(
                                    id, value)}
                            />
                        </div>
                    </div>
                </div>

                </div>
                <div className={"is_transparent_parent"}>
                    <Checkbox id={'is_transparent'}
                              checked={iconSetting.color_settings.is_transparent}
                              value={iconSetting.color_settings.is_transparent}
                              onChange={(value, id) => handleIconColorSettingChange(
                                  id, value)}
                              label={'Transparent Background'}></Checkbox>
                </div>

                <div className={'divider'}>
                    <Divider/>
                </div>

                {/* TYPOGRAPHY */}

                <div className={'color-settings'}>
                    <Text variant={'headingMd'} as={'h1'}> TYPOGRAPHY </Text>
                </div>

                <div className="parent">
                    <div className="parent_child">
                        <div>
                            <div>
                                <Text variant={'headingSm'} as={'h6'}> Title font size </Text>
                            </div>
                            <TextField
                                label={''}
                                type="number"
                                id={'title_font_size'}
                                suffix={'PX'}
                                min={1}
                                max={50}
                                value={iconSetting.typography_settings.title_font_size}
                                onChange={(
                                    value, id) => handleIconTypographySettingChange(
                                    id, value)}
                            />
                        </div>
                        <div>
                            <div>
                                <Text variant={'headingSm'} as={'h6'}> Title font style </Text>
                            </div>
                            <Select
                                label={''}
                                options={fontStyleOptions}
                                id={'title_font_style'}
                                value={iconSetting.typography_settings.title_font_style}
                                onChange={(
                                    value,
                                    id) => handleIconTypographySettingChange(id,
                                    value)}
                            />
                        </div>
                    </div>
                </div>

                <div className="parent">
                    <div className="parent_child">
                        <div>
                            <div>
                                <Text variant={'headingSm'} as={'h6'}> Subtitle font size </Text>
                            </div>
                            <TextField
                                label={''}
                                type="number"
                                id={'subtitle_font_size'}
                                suffix={'PX'}
                                min={1}
                                max={50}
                                value={iconSetting.typography_settings.subtitle_font_size}
                                onChange={(
                                    value, id) => handleIconTypographySettingChange(
                                    id, value)}
                            />
                        </div>
                        <div>
                            <div>
                                <Text variant={'headingSm'} as={'h6'}> Subtitle font style </Text>
                            </div>

                            <Select
                                label={''}
                                options={fontStyleOptions}
                                id={'subtitle_font_style'}
                                value={iconSetting.typography_settings.subtitle_font_style}
                                onChange={(
                                    value,
                                    id) => handleIconTypographySettingChange(id,
                                    value)}
                            />
                        </div>
                    </div>
                </div>

                <div className={'divider'}>
                    <Divider/>
                </div>

                <Box background="action-secondary" borderRadius={2}
                     paddingBlockEnd={'1'} title="Block">
                    <Text variant={'headingMd'} as={'h1'}> SPACING </Text>
                    <div className={'range-wrapper spacing'}>
                        <LegacyStack distribution={'fill'}
                                     alignment={'trailing'}>
                            <LegacyStack.Item>
                                <RangeSlider
                                    label={'Block Size'}
                                    id={'block_size'}
                                    value={iconSetting.block_size}
                                    onChange={(
                                        value, id) => handleIconSettingChange(
                                        id, value)}
                                    output
                                    min={0}
                                    max={150}
                                ></RangeSlider>


                            </LegacyStack.Item>
                            <LegacyStack.Item>
                                <TextField
                                    label={''}
                                    id={'block_size'}
                                    value={iconSetting.block_size}
                                    onChange={(
                                        value, id) => handleIconSettingChange(
                                        id, value)}
                                    type={'number'}
                                    max={150}
                                    min={0}
                                    suffix={'PX'}
                                />
                            </LegacyStack.Item>
                        </LegacyStack>
                    </div>

                    <div className={'border-location-header'}>
                        <Text variant={'headingMd'} as={'h1'}> BORDER
                            LOCATION </Text>
                    </div>
                    <div className={"border-location-content"}>
                    <LegacyStack spacing={'extraLoose'}
                                 distribution={'fillEvenly'}>
                        <TextField
                            label={<Text variant={'headingSm'} as={'h6'}> Goes
                                up </Text>}
                            type="number"
                            id={'goes_up'}
                            suffix={'PX'}
                            value={iconSetting.goes_up}
                            max={150}
                            min={0}
                            onChange={(value, id) => handleIconSettingChange(id,
                                value)}

                        />

                        <LegacyStack.Item>
                            <TextField
                                label={<Text variant={'headingSm'}
                                             as={'h6'}> Goes down </Text>}
                                type="number"
                                id={'goes_down'}
                                suffix={'PX'}
                                max={150}
                                min={0}
                                value={iconSetting.goes_down}
                                onChange={(
                                    value, id) => handleIconSettingChange(id,
                                    value)}
                            />
                        </LegacyStack.Item>
                    </LegacyStack>
                    </div>
                    <div className={'range-wrapper spacing'}>
                        <LegacyStack distribution={'fill'}
                                     alignment={'trailing'}>
                            <LegacyStack.Item>
                                <RangeSlider
                                    label={'Space in between Block'}
                                    id={'space_between_blocks'}
                                    value={iconSetting.space_between_blocks}
                                    onChange={(
                                        value, id) => handleIconSettingChange(
                                        id, value)}
                                    output
                                    min={0}
                                    max={100}
                                ></RangeSlider>


                            </LegacyStack.Item>
                            <LegacyStack.Item>
                                <TextField
                                    label={''}
                                    id={'space_between_blocks'}
                                    value={iconSetting.space_between_blocks}
                                    onChange={(
                                        value, id) => handleIconSettingChange(
                                        id, value)}
                                    type={'number'}
                                    max={100}
                                    min={0}
                                    suffix={'%'}
                                />
                            </LegacyStack.Item>
                        </LegacyStack>
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
