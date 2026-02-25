import {
    Box,
    Button,
    Checkbox,
    Divider,
    Icon,
    LegacyStack,
    Link,
    Text,
    TextField,
    Thumbnail,
} from '@shopify/polaris';
import {
    ArrowDownMinor,
    ArrowUpMinor,
    DeleteMajor,
} from '@shopify/polaris-icons';
import React, {useContext, useEffect, useState} from 'react';
import {toast} from 'react-toastify';
import {AppContext} from '../../context/AppProvider';
import IconService from '../../services/IconService';
import SegmentService from '../../services/SegmentService';
import {showToast} from '../../utils/constant';
import Footer from '../layout/Footer';
import IconModal from '../Modals/IconModal';

export default function IconsSection({
    _icons,
    editId,
    iconsChange,
    add_more,
    handleBlockSave,
    handleBlockDelete,
    showUpgradePlanBanner,
}) {
    const [icons, setIcons] = useState(_icons);
    const [showIconModal, setShowIconModal] = useState(false);
    const [selectedIconIndex, setSelectedIconIndex] = useState('');
    const {plan, user, setUser} = useContext(AppContext);
    const {discard_flag, increaseDiscardFlag} = useContext(AppContext);

    useEffect(() => {
        if (discard_flag !== 1) {
            iconsChange(icons);
        }
    }, [icons]);

    const handleShowCondition = (index, value) => {
        if(!plan.trigger_product_tag){
            showUpgradePlanBanner();
            return;
        }
        increaseDiscardFlag(discard_flag + 1);
        let icon = {...icons[index], ['show_condition']: value};
        if (value === false) {
            icon['tags'] = '';
        }
        let _icons = [...icons];
        _icons[index] = icon;
        setIcons(_icons);
    };

    const handleIconInputChange = (value, id, index) => {
        increaseDiscardFlag(discard_flag + 1);
        let icon = {...icons[index], [id]: value};
        let _icons = [...icons];
        _icons[index] = icon;
        setIcons(_icons);
    };

    const handleShowLink = (index, value) => {
        if(!plan.add_link){
            showUpgradePlanBanner();
            return;
        }
        increaseDiscardFlag(discard_flag + 1);
        let icon = {...icons[index], ['show_link']: value};
        if (value === false) {
            icon['open_to_new_tab'] = false;
            icon['link'] = '';
        }
        let _icons = [...icons];
        _icons[index] = icon;
        setIcons(_icons);
    };

    const handleIconOpenTabStatus = (index, status) => {
        increaseDiscardFlag(discard_flag + 1);
        let icon = {...icons[index], open_to_new_tab: status};
        let _icons = [...icons];
        _icons[index] = icon;
        setIcons(_icons);
    };

    const handleRemoveIcon = (index) => {
        increaseDiscardFlag(discard_flag + 1);
        let _icons = [...icons];
        _icons.splice(index, 1);
        setIcons(_icons);
    };

    const moveIconPosition = (from, to) => {
        increaseDiscardFlag(discard_flag + 1);
        let _icons = [...icons];
        var f = _icons.splice(from, 1)[0];

        _icons.splice(to, 0, f);

        _icons = _icons.map((i, index) => {
            i.position = index + 1;
            return i;
        });
        setIcons(_icons);

        let position = from > to ? 'Up' : 'Down';

        showToast(`Icon Position Changed To ${position}`,
            {type: 'success', autoClose: 1000});
    };

    const handleAddIcon = () => {
        if (plan?.icon_per_block_limit != null && icons.length ===
            plan.icon_per_block_limit) {
            showUpgradePlanBanner();
            return;
        }
        increaseDiscardFlag(discard_flag + 1);
        let _icons = [...icons];
        let icon = {
            icon: {
                name: add_more.name,
                id: add_more.id,
                type: add_more.type,
                svg: add_more.svg,
            },
            title: 'Title',
            subtitle: 'Subtitle',
            show_link: false,
            show_condition: false,
            link: '',
            position: _icons.length + 1,
            open_to_new_tab: false,
            tags: '',
        };
        _icons.push(icon);
        setIcons(_icons);
    };

    const handleShowIconModal = (index) => {
        setSelectedIconIndex(index);
        setShowIconModal(true);
        if(user.segment_events.clicked_on_library_of_icons === 0 ){
            SegmentService.sendSegmentEvent('clicked_on_library_of_icons')
            .then((response) =>{
                if (response.data.success){
                    user.segment_events.clicked_on_library_of_icons = 1;
                    setUser(user);
                }
            }).catch((error)=>{
            })
        }
    };

    const setUserIcon = (icon) => {
        increaseDiscardFlag(discard_flag + 1);
        IconService.getSingleIcon(icon.id, icon.type).then((response) => {
            if (response.data?.data) {
                let _icon = {
                    ...icons[selectedIconIndex],
                    icon: {
                        name: icon.name,
                        id: icon.id,
                        type: icon.type,
                        svg: response.data.data,
                    },
                };

                let _icons = [...icons];
                _icons[selectedIconIndex] = _icon;
                setIcons(_icons);
                setSelectedIconIndex('');
                setShowIconModal(false);
            }
        }).catch((error) => {
        }).finally(() => {

        });
    };

    return (
        <>
            {
                showIconModal ?
                    <IconModal setIcon={setUserIcon} showUpgradePlanBanner={() => showUpgradePlanBanner()}
                               onClose={() => setShowIconModal(false)}/>
                    : null
            }

            <div className={'block-card'}>
                {
                    icons.map((icon, index) => {
                        return (

                            <Box
                                title="Block" key={index}>
                                <LegacyStack distribution={'equalSpacing'}>
                                    <LegacyStack.Item>

                                        <Text variant={'headingSm'}
                                              as={'h6'}> ICON #{index +
                                            1}</Text>
                                    </LegacyStack.Item>
                                    <LegacyStack.Item>
                                        {
                                            index > 0 ?
                                                <Button
                                                    onClick={() => moveIconPosition(
                                                        index, index - 1)}>
                                                    <Icon
                                                        source={ArrowUpMinor}></Icon>
                                                </Button>
                                                : null
                                        }

                                        {
                                            index < icons.length - 1 ?
                                                <Button
                                                    onClick={() => moveIconPosition(
                                                        index, index + 1)}>
                                                    <Icon
                                                        source={ArrowDownMinor}></Icon>
                                                </Button>
                                                : null
                                        }

                                        <Button destructive
                                                onClick={() => handleRemoveIcon(
                                                    index)}>
                                            <Icon source={DeleteMajor}></Icon>
                                        </Button>
                                    </LegacyStack.Item>
                                </LegacyStack>

                                <LegacyStack distribution={'equalSpacing'}>
                                    <LegacyStack.Item>
                                        <div onClick={() => handleShowIconModal(
                                            index)} className={'icons-wrapper'}>
                                            {
                                                (icon.icon.svg.startsWith(
                                                        '<svg') ||
                                                    icon.icon.svg.startsWith(
                                                        '<?xml'))
                                                    ?
                                                    <div
                                                        className={'icon-thumb'}
                                                        dangerouslySetInnerHTML={{__html: icon.icon.svg}}/>
                                                    :
                                                    <Thumbnail size={'large'}
                                                               source={icon.icon.svg}
                                                               alt={'Icon Image'}></Thumbnail>
                                            }
                                        </div>
                                    </LegacyStack.Item>
                                    <LegacyStack.Item fill>
                                        <LegacyStack vertical spacing={'loose'}>
                                            <TextField
                                                label={<Text
                                                    variant={'headingSm'}
                                                    as={'h1'}> Title </Text>}
                                                placeholder="Enter Title"
                                                id={'title'}
                                                value={icon.title}
                                                onChange={(
                                                    value,
                                                    id) => handleIconInputChange(
                                                    value, id, index)}
                                            />

                                            <TextField
                                                label={<Text
                                                    variant={'headingSm'}
                                                    as={'h1'}> Subtitle </Text>}
                                                placeholder="Enter header name"
                                                id={'subtitle'}
                                                value={icon.subtitle}
                                                onChange={(
                                                    value,
                                                    id) => handleIconInputChange(
                                                    value, id, index)}
                                            />
                                        </LegacyStack>
                                    </LegacyStack.Item>
                                </LegacyStack>
                                <div className={'icon-action-links'}>
                                    <LegacyStack spacing={'loose'}>
                                        {
                                            !icon.show_link ?
                                                <Link
                                                    onClick={() => handleShowLink(
                                                        index,
                                                        !icon.show_link)}>Add
                                                    Link </Link>
                                                : null
                                        }
                                        {
                                            !icon.show_condition ?
                                                <Link
                                                    onClick={() => handleShowCondition(
                                                        index,
                                                        !icon?.show_condition)}> Add
                                                    condition (show by product
                                                    tag)</Link>
                                                : null
                                        }
                                    </LegacyStack>
                                </div>
                                <div className={'icon-textfields'}>

                                    {
                                        plan.add_link &&  icon.show_link ?
                                            <>
                                                <TextField
                                                    label="Link"
                                                    id={'link'}
                                                    value={icon.link}
                                                    onChange={(
                                                        value,
                                                        id) => handleIconInputChange(
                                                        value, id, index)}
                                                    labelAction={{
                                                        content: 'Remove Link',
                                                        onAction: () => handleShowLink(
                                                            index,
                                                            !icon.show_link),
                                                    }}
                                                    placeholder={'i.e. https://example.com'}
                                                    autoComplete="off"
                                                />

                                                <Checkbox
                                                    label="Open in new tab"
                                                    checked={icon.open_to_new_tab}
                                                    id={'open_to_new_tab'}
                                                    onChange={() => handleIconOpenTabStatus(
                                                        index,
                                                        !icon.open_to_new_tab)}
                                                />
                                            </>
                                            : null
                                    }
                                    {
                                        plan.trigger_product_tag && icon.show_condition ?
                                            <TextField
                                                label="Show only on products with tags"
                                                id={'tags'}
                                                value={icon.tags}
                                                onChange={(
                                                    value,
                                                    id) => handleIconInputChange(
                                                    value, id, index)}
                                                labelAction={{
                                                    content: 'Remove condition',
                                                    onAction: () => handleShowCondition(
                                                        index, !icon.show_condition),
                                                }}
                                                placeholder={'i.e. Black,White'}
                                                autoComplete="off"
                                                helpText={'Enter product tags with comma separated. i.e. Black,White'}
                                            />
                                            : null
                                    }

                                </div>

                                <div className={'divider'}>
                                    <Divider/>
                                </div>
                            </Box>
                        );
                    })
                }
                <div className={'add-more-icon-button'}>
                    <Button onClick={() => handleAddIcon()} primary
                            size={'large'} fullWidth> Add More </Button>
                </div>
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
